<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use App\Models\Course;
use App\Models\MpesaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Dashboard with Bonus Charts (Monthly Revenue, Revenue by Course, Payment Method)
    public function index()
    {
        // 1. Monthly Revenue Trend
        $monthlyRevenue = Fee::selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();
        
        // 2. Revenue by Course
        $revenueByCourse = DB::table('fees')
            ->join('students', 'fees.student_id', '=', 'students.id')
            ->select('students.course', DB::raw('SUM(fees.amount) as total_revenue'))
            ->groupBy('students.course')
            ->get();

        // 3. Payment Method Distribution
        $paymentMethods = Fee::select('payment_method', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as total_transactions'))
            ->groupBy('payment_method')
            ->get();
            
        return view('reports.index', compact('monthlyRevenue', 'revenueByCourse', 'paymentMethods'));
    }

    // REPORT 1: STUDENT STATEMENT REPORT
    public function studentStatement(Request $request)
    {
        $studentId = $request->input('student_id');
        $student = null;
        $statement = null;

        if ($studentId) {
            $student = Student::with('fees')->find($studentId);
            if ($student) {
                $totalExpected = $student->total_fee > 0 ? $student->total_fee : 45000;
                $totalPaid = $student->fees->sum('amount');
                $balance = $totalExpected - $totalPaid;
                
                $statement = (object) [
                    'totalExpected' => $totalExpected,
                    'totalPaid' => $totalPaid,
                    'balance' => $balance,
                    'fees' => $student->fees
                ];
            }
        }

        $students = Student::orderBy('name')->get();
        return view('reports.student_statement', compact('students', 'student', 'statement'));
    }

    // REPORT 2: FEE COLLECTION REPORT
    public function feeCollection(Request $request)
    {
        $query = Fee::with('student');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }
        if ($request->filled('course')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('course', $request->course);
            });
        }

        $collections = $query->latest('payment_date')->get();

        $totalAmount = $collections->sum('amount');
        $totalTransactions = $collections->count();
        $averagePayment = $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0;

        $courses = Student::whereNotNull('course')->where('course', '!=', '')->distinct()->pluck('course');
        $methods = Fee::whereNotNull('payment_method')->where('payment_method', '!=', '')->distinct()->pluck('payment_method');
        
        return view('reports.fee_collection', compact('collections', 'totalAmount', 'totalTransactions', 'averagePayment', 'courses', 'methods'));
    }

    // REPORT 3: OUTSTANDING BALANCES REPORT
    public function outstandingBalances(Request $request)
    {
        $studentsQuery = Student::with('fees');
        
        if ($request->filled('course')) {
            $studentsQuery->where('course', $request->course);
        }
        
        $students = $studentsQuery->get();
        
        $balances = $students->map(function ($student) {
            $expected = $student->total_fee > 0 ? $student->total_fee : 45000;
            $paid = $student->fees->sum('amount');
            $balance = $expected - $paid;
            
            return (object) [
                'id' => $student->id,
                'name' => $student->name,
                'course' => $student->course,
                'expected' => $expected,
                'paid' => $paid,
                'balance' => $balance,
            ];
        })->filter(function ($item) {
            return $item->balance > 0;
        })->values();

        $courses = Student::whereNotNull('course')->where('course', '!=', '')->distinct()->pluck('course');
        return view('reports.outstanding_balances', compact('balances', 'courses'));
    }

    // REPORT 4: COURSE REVENUE REPORT
    public function courseRevenue()
    {
        $courseRevenues = DB::table('students')
            ->leftJoin('fees', 'students.id', '=', 'fees.student_id')
            ->select('students.course', DB::raw('COUNT(DISTINCT students.id) as number_of_students'), DB::raw('COALESCE(SUM(fees.amount), 0) as total_revenue'))
            ->whereNotNull('students.course')
            ->where('students.course', '!=', '')
            ->groupBy('students.course')
            ->get();
            
        $grandTotal = $courseRevenues->sum('total_revenue');
        
        return view('reports.course_revenue', compact('courseRevenues', 'grandTotal'));
    }

    // REPORT 5: DAILY COLLECTION REPORT
    public function dailyCollection()
    {
        $today = now()->toDateString();
        
        $collections = Fee::with('student')
            ->whereDate('payment_date', $today)
            ->get();
            
        $todayTotal = $collections->sum('amount');
        $numTransactions = $collections->count();
        
        return view('reports.daily_collection', compact('collections', 'todayTotal', 'numTransactions'));
    }

    // REPORT 6: MONTHLY COLLECTION REPORT
    public function monthlyCollection(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $collections = Fee::with('student')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->get();
            
        $totalCollected = $collections->sum('amount');
        $numTransactions = $collections->count();
        $highestPayment = $collections->max('amount') ?? 0;
        $averagePayment = $numTransactions > 0 ? $collections->avg('amount') : 0;
        
        return view('reports.monthly_collection', compact(
            'collections', 'totalCollected', 'numTransactions', 
            'highestPayment', 'averagePayment', 'month', 'year'
        ));
    }

    // REPORT 7: M-PESA TRANSACTIONS REPORT
    public function mpesaTransactions(Request $request)
    {
        $query = MpesaTransaction::with('student');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        
        $transactions = $query->latest()->get();
        return view('reports.mpesa_transactions', compact('transactions'));
    }

    // REPORT 8: PAYMENT METHOD ANALYSIS
    public function paymentMethods()
    {
        $methods = Fee::select('payment_method', DB::raw('COUNT(*) as transactions'), DB::raw('SUM(amount) as total_amount'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();
            
        return view('reports.payment_methods', compact('methods'));
    }

    // REPORT 9: TERM-WISE STUDENT FEE RECORD
    public function termWiseRecord(Request $request)
    {
        $studentId = $request->input('student_id');
        $student = null;
        $termData = null;
        $overall = null;

        if ($studentId) {
            $student = Student::with('fees')->find($studentId);

            if ($student) {
                $totalFee = $student->total_fee > 0 ? (float) $student->total_fee : 45000;
                $defaultTermFee = round($totalFee / 3, 2);

                $terms = ['Term 1', 'Term 2', 'Term 3'];
                $termData = collect($terms)->map(function ($term) use ($student, $defaultTermFee) {
                    $termFees = $student->fees->where('term', $term);
                    $paid = $termFees->sum('amount');

                    // Use term_fee from the first fee record if set, else default
                    $firstFee = $termFees->first();
                    $expected = ($firstFee && $firstFee->term_fee > 0)
                        ? (float) $firstFee->term_fee
                        : $defaultTermFee;

                    $balance = max(0, $expected - $paid);
                    $percent = $expected > 0 ? min(100, round(($paid / $expected) * 100)) : 0;

                    $status = $balance <= 0 ? 'cleared'
                        : ($paid > 0 ? 'partial' : 'unpaid');

                    return (object) [
                        'term'     => $term,
                        'expected' => $expected,
                        'paid'     => $paid,
                        'balance'  => $balance,
                        'percent'  => $percent,
                        'status'   => $status,
                        'fees'     => $termFees->sortByDesc('payment_date')->values(),
                    ];
                });

                $overall = (object) [
                    'expected' => $termData->sum('expected'),
                    'paid'     => $termData->sum('paid'),
                    'balance'  => $termData->sum('balance'),
                ];
            }
        }

        $students = Student::orderBy('name')->get();

        return view('reports.term_wise_record', compact('students', 'student', 'termData', 'overall'));
    }
}
