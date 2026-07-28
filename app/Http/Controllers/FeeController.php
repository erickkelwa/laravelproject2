<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index()
    {
        // ── Paginated payment records for the bottom table (Unified) ──
        $feesList = Fee::with('student')->get()->map(function($f) {
            return (object)[
                'id' => $f->id,
                'model_type' => 'fee',
                'student' => $f->student,
                'course' => $f->student->course ?? '-',
                'amount' => $f->amount,
                'method' => $f->payment_method ?? 'Manual',
                'status' => 'Completed',
                'reference' => $f->receipt_no ?? '—',
                'date' => clone $f->payment_date,
                'created_at' => $f->created_at
            ];
        });

        // Also get M-Pesa STK transactions that don't have a fee record yet (e.g. pending/failed)
        $existingFeeReceipts = Fee::whereNotNull('receipt_no')->pluck('receipt_no')->filter()->toArray();
        $mpesaList = \App\Models\MpesaTransaction::with('student')->get()
            ->reject(function($m) use ($existingFeeReceipts) {
                return $m->status === 'success' && $m->receipt_number && in_array($m->receipt_number, $existingFeeReceipts);
            })
            ->map(function($m) {
                $statusText = 'Pending';
                if ($m->status === 'success') $statusText = 'Completed';
                if ($m->status === 'failed') $statusText = 'Failed';
                
                $ref = $m->receipt_number ?: ($m->checkout_request_id ? strtoupper(substr($m->checkout_request_id, 0, 10)) : '—');
                
                return (object)[
                    'id' => $m->id,
                    'model_type' => 'mpesa',
                    'student' => $m->student,
                    'course' => $m->student->course ?? '-',
                    'amount' => $m->amount,
                    'method' => 'M-Pesa STK',
                    'status' => $statusText,
                    'reference' => $ref,
                    'date' => clone $m->created_at,
                    'created_at' => $m->created_at
                ];
            });

        $allTransactions = $feesList->concat($mpesaList)->sortByDesc('created_at')->values();

        if (request()->filled('method')) {
            $searchMethod = strtolower(trim(request('method')));
            $allTransactions = $allTransactions->filter(function($tx) use ($searchMethod) {
                $m = strtolower(trim($tx->method ?? ''));
                if ($searchMethod === 'mpesa' || $searchMethod === 'm-pesa') {
                    return str_contains($m, 'mpesa') || str_contains($m, 'm-pesa') || str_contains($m, 'stk');
                }
                return str_contains($m, $searchMethod);
            })->values();
        }

        $perPage = 15;
        $page = request()->get('page', 1);
        $paginatedItems = $allTransactions->slice(($page - 1) * $perPage, $perPage)->values();
        $fees = new \Illuminate\Pagination\LengthAwarePaginator($paginatedItems, $allTransactions->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('fees.index', compact('fees'));
    }

    public function create()
    {
        $students = Student::with('fees')->get();
        return view('fees.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'     => 'required|exists:students,id',
            'term'           => 'required|in:Term 1,Term 2,Term 3',
            'term_fee'       => 'nullable|numeric|min:0',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'receipt_no'     => 'nullable|string|max:255',
            'payment_date'   => 'required|date',
        ]);

        $student = Student::find($validated['student_id']);
        if (empty($validated['term_fee']) || $validated['term_fee'] == 0) {
            if ($student && $student->total_fee > 0) {
                $validated['term_fee'] = round($student->total_fee / 3, 2);
            }
        }

        // Check for overpayment
        if ($student) {
            $balance = $student->balance;
            if ($balance <= 0) {
                return back()->withErrors(['amount' => 'This student has already cleared all their fees.'])->withInput();
            }
            if ($validated['amount'] > $balance) {
                return back()->withErrors(['amount' => 'Payment amount cannot exceed the student\'s remaining balance of KES ' . number_format($balance, 2)])->withInput();
            }
        }

        $fee = Fee::create($validated);

        if ($student) {
            \Illuminate\Support\Facades\Notification::send(
                \App\Models\User::all(),
                new \App\Notifications\PaymentRecorded($validated['amount'], $student->name, $validated['payment_method'])
            );
        }

        $msg = 'Fee payment recorded successfully.';

        // Send email receipt
        if ($fee->student && $fee->student->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($fee->student->email)
                    ->send(new \App\Mail\PaymentReceived($fee));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send email receipt: ' . $e->getMessage());
            }
        }

        return redirect()->to(route('fees.index') . '#recent-payments')
            ->with('success', $msg)
            ->with('new_fee_id', $fee->id);
    }

    public function show(Fee $fee)
    {
        $fee->load('student.course');
        return view('fees.show', compact('fee'));
    }

    public function edit(Fee $fee)
    {
        $students = Student::all();
        return view('fees.edit', compact('fee', 'students'));
    }

    public function update(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'student_id'     => 'required|exists:students,id',
            'term'           => 'required|in:Term 1,Term 2,Term 3',
            'term_fee'       => 'nullable|numeric|min:0',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'receipt_no'     => 'nullable|string|max:255',
            'payment_date'   => 'required|date',
        ]);

        $student = Student::find($validated['student_id']);
        if (empty($validated['term_fee']) || $validated['term_fee'] == 0) {
            if ($student && $student->total_fee > 0) {
                $validated['term_fee'] = round($student->total_fee / 3, 2);
            }
        }

        $fee->update($validated);

        return redirect()->route('fees.index')->with('success', 'Fee payment updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('fees.index')->with('success', 'Fee payment deleted successfully.');
    }

    public function downloadReceipt(Fee $fee)
    {
        $fee->load('student');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('fees.receipt', compact('fee'));
        
        return $pdf->download('receipt_' . ($fee->receipt_no ?? $fee->id) . '.pdf');
    }
}
