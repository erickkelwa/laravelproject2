<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\MpesaController;
use App\Models\Student;
use App\Models\Fee;

// ── Health check for Render (no auth required) ──
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// ── Root → redirect based on role ──
Route::get('/', function () {
    if (auth()->check() && auth()->user()->role == 'student') {
        return redirect()->route('student.dashboard');
    }
    return redirect()->route('dashboard');
});

// ── General Protected Routes (Any Authenticated User) ──
Route::middleware(['auth'])->group(function () {
    Route::get('/fees/{fee}/receipt', [FeeController::class, 'downloadReceipt'])->name('fees.receipt');
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-as-read');
    
    Route::post('/notifications/{id}/mark-as-read', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    })->name('notifications.mark-as-read.single');
});

// ── Protected Admin Routes (Breeze auth + Admin Role) ──
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard with live stats
    Route::get('/dashboard', function () {
        $totalStudents  = Student::count();
        $totalCourses   = Student::distinct('course')->count('course');
        $latestStudent  = Student::latest()->first();
        $recentStudents = Student::latest()->take(5)->get();
        $courseBreakdown = Student::selectRaw('course, COUNT(*) as total')
            ->groupBy('course')
            ->orderByDesc('total')
            ->get();
        $newThisMonth = Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Fee Analytics
        $totalFeesCollected = Fee::sum('amount');
        $todaysCollections  = Fee::whereDate('payment_date', now()->toDateString())->sum('amount');
        
        // Unified Recent Transactions (Fees + M-Pesa)
        $recentFees = Fee::with('student')
            ->whereNotIn('payment_method', ['MPESA', 'M-Pesa', 'M-Pesa STK', 'mpesa'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function($f) {
                return (object) [
                    'type' => 'fee',
                    'student_name' => $f->student->name ?? 'N/A',
                    'amount' => $f->amount,
                    'method' => $f->payment_method ?? 'Manual',
                    'status' => 'success',
                    'date' => $f->payment_date,
                    'created_at' => $f->created_at,
                ];
            });

        $recentMpesa = \App\Models\MpesaTransaction::with('student')
            ->latest()
            ->take(6)
            ->get()
            ->map(function($m) {
                return (object) [
                    'type' => 'mpesa',
                    'student_name' => $m->student->name ?? 'N/A',
                    'amount' => $m->amount,
                    'method' => 'M-Pesa STK',
                    'status' => $m->status,
                    'date' => $m->created_at,
                    'created_at' => $m->created_at,
                ];
            });

        $recentTransactions = $recentFees->concat($recentMpesa)
            ->sortByDesc('created_at')
            ->take(6);

        $topPayingStudents = Student::withSum('fees', 'amount')
            ->whereHas('fees')
            ->orderByRaw('(select sum(fees.amount) from fees where fees.student_id = students.id) desc nulls last')
            ->take(5)
            ->get();

        // Pending balance = sum of all total_fees minus all fees paid
        $totalFeesBilled  = Student::sum('total_fee');
        $totalPending     = max(0, $totalFeesBilled - $totalFeesCollected);
        $avgFeePerStudent = $totalStudents > 0 ? round($totalFeesBilled / $totalStudents, 2) : 0;

        // Monthly fee collections — last 6 months
        $monthlyFees = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month'  => $date->format('M Y'),
                'amount' => Fee::whereYear('payment_date', $date->year)
                               ->whereMonth('payment_date', $date->month)
                               ->sum('amount'),
            ];
        });

        // Monthly student enrollments — last 6 months
        $monthlyEnrollments = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'count' => Student::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count(),
            ];
        });

        // Per-student fee balance breakdown
        $feeBalances = Student::withSum('fees', 'amount')
            ->orderBy('name')
            ->get()
            ->map(function ($s) {
                $paid    = (float) ($s->fees_sum_amount ?? 0);
                $total   = (float) $s->total_fee;
                $balance = max(0, $total - $paid);
                $status  = $balance <= 0 ? 'cleared'
                         : ($paid > 0    ? 'partial'
                                         : 'unpaid');
                return (object) [
                    'id'      => $s->id,
                    'name'    => $s->name,
                    'course'  => $s->course,
                    'total'   => $total,
                    'paid'    => $paid,
                    'balance' => $balance,
                    'status'  => $status,
                ];
            });

        return view('dashboard.index', compact(
            'totalStudents', 'totalCourses', 'latestStudent',
            'recentStudents', 'courseBreakdown', 'newThisMonth',
            'totalFeesCollected', 'todaysCollections', 'recentTransactions', 'topPayingStudents',
            'totalPending', 'avgFeePerStudent', 'monthlyFees', 'monthlyEnrollments',
            'feeBalances'
        ));
    })->name('dashboard');

    // Student CRUD
    Route::resource('students', StudentController::class);
    
    // Fees CRUD
    Route::resource('fees', FeeController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/student-statement', [\App\Http\Controllers\ReportController::class, 'studentStatement'])->name('student-statement');
        Route::get('/fee-collection', [\App\Http\Controllers\ReportController::class, 'feeCollection'])->name('fee-collection');
        Route::get('/outstanding-balances', [\App\Http\Controllers\ReportController::class, 'outstandingBalances'])->name('outstanding-balances');
        Route::get('/course-revenue', [\App\Http\Controllers\ReportController::class, 'courseRevenue'])->name('course-revenue');
        Route::get('/daily-collection', [\App\Http\Controllers\ReportController::class, 'dailyCollection'])->name('daily-collection');
        Route::get('/monthly-collection', [\App\Http\Controllers\ReportController::class, 'monthlyCollection'])->name('monthly-collection');
        Route::get('/mpesa-transactions', [\App\Http\Controllers\ReportController::class, 'mpesaTransactions'])->name('mpesa-transactions');
        Route::get('/payment-methods', [\App\Http\Controllers\ReportController::class, 'paymentMethods'])->name('payment-methods');
    });

    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');

    // Exam Results
    Route::get('/exams', [\App\Http\Controllers\ExamResultController::class, 'index'])->name('exams.index');
    Route::post('/exams', [\App\Http\Controllers\ExamResultController::class, 'store'])->name('exams.store');
    Route::delete('/exams/{exam}', [\App\Http\Controllers\ExamResultController::class, 'destroy'])->name('exams.destroy');

    // Staff Portal
    Route::resource('staff', \App\Http\Controllers\StaffController::class);

    // M-Pesa Routes
    Route::get('/mpesa/transactions', [MpesaController::class, 'index'])->name('mpesa.index');
    Route::post('/mpesa/stkpush', [MpesaController::class, 'stkPush'])->name('mpesa.stkpush');
    Route::get('/mpesa/status/{checkoutRequestId}', [MpesaController::class, 'checkStatus'])->name('mpesa.status');
});

// M-Pesa Callback (Must not be under auth middleware)
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

// ── Protected Student Routes (Breeze auth + Student Role) ──
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'index'])->name('student.dashboard');
    // We also need students to be able to hit the mpesa stk push route
    Route::post('/student/mpesa/stkpush', [MpesaController::class, 'stkPush'])->name('student.mpesa.stkpush');
});

// ── Breeze Auth Routes (login, register, logout, password reset…) ──
require __DIR__.'/auth.php';
