@extends('layouts.master')

@section('title', 'M-Pesa Transactions Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">M-Pesa Transactions Report</h2>
        <small class="text-muted">Monitor all M-Pesa transactions.</small>
    </div>
    <div class="no-print">
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.mpesa-transactions') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Completed (Success)</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
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
                        <th>Phone Number</th>
                        <th class="text-end">Amount (KES)</th>
                        <th class="text-center pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td class="ps-4">{{ $t->created_at->format('d M Y, h:i A') }}</td>
                            <td class="fw-bold">{{ $t->receipt_number ?? '-' }}</td>
                            <td>{{ $t->student->name ?? 'Unknown' }}</td>
                            <td>{{ $t->phone_number }}</td>
                            <td class="text-end text-dark fw-bold">{{ number_format($t->amount, 2) }}</td>
                            <td class="text-center pe-4">
                                @if($t->status == 'success')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Completed</span>
                                @elseif($t->status == 'failed')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Failed</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No M-Pesa transactions found.</td>
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
