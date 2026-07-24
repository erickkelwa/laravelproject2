@extends('layouts.master')

@section('title', 'Monthly Collection Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Monthly Collection Report</h2>
        <small class="text-muted">Display monthly revenue performance.</small>
    </div>
    <div class="no-print">
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.monthly-collection') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Month</label>
                <select name="month" class="form-select">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Year</label>
                <select name="year" class="form-select">
                    @for($i=now()->year; $i>=now()->year-5; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #198754;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Total Collected</small>
                <h3 class="mb-0 text-success fw-bold">KES {{ number_format($totalCollected, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Number of Transactions</small>
                <h3 class="mb-0 text-primary fw-bold">{{ $numTransactions }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Highest Payment</small>
                <h3 class="mb-0 text-danger fw-bold">KES {{ number_format($highestPayment, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #ffc107;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Average Payment</small>
                <h3 class="mb-0 text-warning fw-bold">KES {{ number_format($averagePayment, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Receipt No</th>
                        <th>Student</th>
                        <th>Method</th>
                        <th class="text-end pe-4">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collections as $fee)
                        <tr>
                            <td class="ps-4">{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('d M Y') : '' }}</td>
                            <td>{{ $fee->receipt_no ?? '-' }}</td>
                            <td class="fw-bold">{{ $fee->student->name ?? 'Unknown' }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                    {{ $fee->payment_method }}
                                </span>
                            </td>
                            <td class="text-end pe-4 text-success fw-bold">{{ number_format($fee->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No collections found for the selected month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        .card, .card *, .row, .row * { visibility: visible; }
        .no-print { display: none !important; }
        .card { box-shadow: none !important; }
    }
</style>
@endpush
