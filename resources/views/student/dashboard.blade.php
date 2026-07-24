@extends('layouts.student')

@section('title', 'My Dashboard')

@section('content')
<div class="row g-4">
    <!-- Profile & Balance Summary -->
    <div class="col-md-4">
        <div class="card h-100 border-top border-4 border-primary">
            <div class="card-body text-center pt-5">
                <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <h4 class="fw-bold">{{ $student->name }}</h4>
                <p class="text-muted">{{ $student->course }}</p>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Fee:</span>
                    <span class="fw-bold">Ksh {{ number_format($student->total_fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Fee Balance:</span>
                    @if($student->balance > 0)
                        <span class="fw-bold text-danger">Ksh {{ number_format($student->balance, 2) }}</span>
                    @elseif($student->balance < 0)
                        <span class="fw-bold text-warning" title="Total payments exceed expected fee">
                            + Ksh {{ number_format(abs($student->balance), 2) }} (Overpaid)
                        </span>
                    @else
                        <span class="fw-bold text-success">Cleared <i class="bi bi-check-circle-fill"></i></span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pay Fees Action -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-phone-vibrate me-2 text-success"></i>Pay Fees via M-Pesa</h5>
            </div>
            <div class="card-body">
                @if($student->balance > 0)
                    <p class="text-muted mb-4">You have a pending balance. Enter your phone number and the amount you wish to pay. You will receive an M-Pesa prompt on your phone to complete the transaction.</p>
                    <form action="{{ route('student.mpesa.stkpush') }}" method="POST" class="row g-3">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">M-Pesa Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="{{ $student->phone }}" required placeholder="e.g. 0712345678">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Amount to Pay (Ksh)</label>
                            <input type="number" class="form-control" name="amount" value="{{ min($student->balance, 5000) }}" max="{{ $student->balance }}" required>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Send M-Pesa Prompt
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check2-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 fw-bold text-success">All Clear!</h4>
                        <p class="text-muted">You have no pending fee balances for this course.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Recent Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Receipt No</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="ps-4 text-secondary fw-bold">{{ $payment->receipt_no ?? '-' }}</td>
                                    <td class="text-success fw-bold">Ksh {{ number_format($payment->amount, 2) }}</td>
                                    <td><span class="badge bg-secondary">{{ $payment->payment_method }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('fees.receipt', $payment) }}" class="btn btn-sm btn-outline-primary" title="Download Receipt">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No recent payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
