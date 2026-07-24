@extends('layouts.master')

@section('title', 'Daily Collection Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Daily Collection Report</h2>
        <small class="text-muted">Display all payments received today ({{ now()->format('d M Y') }}).</small>
    </div>
    <div class="no-print">
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #198754;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Today's Total Collections</small>
                <h3 class="mb-0 text-success fw-bold">KES {{ number_format($todayTotal, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Number of Transactions</small>
                <h3 class="mb-0 text-primary fw-bold">{{ $numTransactions }}</h3>
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
                        <th class="ps-4">Receipt No</th>
                        <th>Student</th>
                        <th>Method</th>
                        <th class="text-end pe-4">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collections as $fee)
                        <tr>
                            <td class="ps-4">{{ $fee->receipt_no ?? '-' }}</td>
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
                            <td colspan="4" class="text-center py-4 text-muted">No payments received today.</td>
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
