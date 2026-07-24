@extends('layouts.master')

@section('title', 'Student Statement Report')

@push('styles')
<style>
    .statement-box {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
    }
    .summary-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
    }
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Student Statement Report</h2>
        <small class="text-muted">Generate a complete fee statement for an individual student.</small>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Reports</a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.student-statement') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="student_id" class="form-label fw-bold">Select Student</label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <option value="">-- Select a Student --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ $s->course }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Generate Statement</button>
            </div>
        </form>
    </div>
</div>

@if($student && $statement)
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center no-print">
        <h5 class="fw-bold mb-0">Statement Result</h5>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print Statement</button>
            <!-- Bonus: Export to PDF could be handled via an additional route if implemented, using window.print as fallback -->
        </div>
    </div>
    <div class="card-body print-area">
        <div class="text-center mb-4">
            <h3 class="fw-bold">FEE STATEMENT</h3>
            <p class="text-muted mb-0">Date Generated: {{ now()->format('d M Y, h:i A') }}</p>
        </div>

        <!-- Student Information -->
        <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Student Information</h6>
        <div class="row mb-4">
            <div class="col-sm-6">
                <p class="mb-1"><strong>Name:</strong> {{ $student->name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $student->email }}</p>
            </div>
            <div class="col-sm-6">
                <p class="mb-1"><strong>Course:</strong> {{ $student->course }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $student->phone }}</p>
            </div>
        </div>

        <!-- Fee Summary -->
        <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Fee Summary</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="summary-card">
                    <small class="text-muted d-block fw-bold text-uppercase">Total Expected</small>
                    <span class="fs-4 fw-bold text-dark">KES {{ number_format($statement->totalExpected, 2) }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <small class="text-muted d-block fw-bold text-uppercase">Total Paid</small>
                    <span class="fs-4 fw-bold text-success">KES {{ number_format($statement->totalPaid, 2) }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <small class="text-muted d-block fw-bold text-uppercase">Outstanding Balance</small>
                    <span class="fs-4 fw-bold {{ $statement->balance > 0 ? 'text-danger' : 'text-success' }}">
                        KES {{ number_format($statement->balance, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Payment History</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Receipt No</th>
                        <th>Term</th>
                        <th>Payment Method</th>
                        <th class="text-end">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statement->fees as $fee)
                        <tr>
                            <td>{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('d M Y') : '' }}</td>
                            <td>{{ $fee->receipt_no ?? '-' }}</td>
                            <td>{{ $fee->term }}</td>
                            <td>{{ $fee->payment_method }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($fee->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No payments recorded for this student.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($statement->fees->count() > 0)
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Paid:</th>
                        <th class="text-end text-success fw-bold">{{ number_format($statement->totalPaid, 2) }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

@endsection
