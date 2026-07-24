@extends('layouts.master')

@section('title', 'Fee Collection Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Fee Collection Report</h2>
        <small class="text-muted">Display all fee payments collected within a selected period.</small>
    </div>
    <div>
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.fee-collection') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-bold">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Method</label>
                <select name="payment_method" class="form-select">
                    <option value="">All Methods</option>
                    @foreach($methods as $method)
                        <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Course</label>
                <select name="course" class="form-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>{{ $course }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Term</label>
                <select name="term" class="form-select">
                    <option value="">All Terms</option>
                    <option value="Term 1" {{ request('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                    <option value="Term 2" {{ request('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                    <option value="Term 3" {{ request('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Section -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #198754;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Total Amount Collected</small>
                <h3 class="mb-0 text-success fw-bold">KES {{ number_format($totalAmount, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body">
                <small class="text-muted fw-bold text-uppercase">Total Transactions</small>
                <h3 class="mb-0 text-primary fw-bold">{{ $totalTransactions }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
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
                        <th class="ps-4">Receipt No</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collections as $fee)
                        <tr>
                            <td class="ps-4">{{ $fee->receipt_no ?? '-' }}</td>
                            <td class="fw-bold">{{ $fee->student->name ?? 'Unknown' }}</td>
                            <td>{{ $fee->student->course ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                    {{ $fee->payment_method }}
                                </span>
                            </td>
                            <td>{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('d M Y') : '' }}</td>
                            <td class="text-end pe-4 text-success fw-bold">{{ number_format($fee->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No collections found for the selected criteria.</td>
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
        .card, .card * { visibility: visible; }
        .no-print { display: none !important; }
        .card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
    }
</style>
@endpush
