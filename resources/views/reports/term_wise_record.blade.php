@extends('layouts.master')

@section('title', 'Term-wise Fee Record')

@push('styles')
<style>
    .term-card {
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .term-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.10) !important;
    }
    .term-header {
        padding: 1.25rem 1.5rem;
        color: #fff;
        font-weight: 700;
        font-size: 1.15rem;
        letter-spacing: 0.03em;
    }
    .term-1-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .term-2-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .term-3-header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .term-body { padding: 1.5rem; }
    .term-stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 600;
        opacity: 0.65;
    }
    .term-stat-value {
        font-size: 1.35rem;
        font-weight: 700;
    }
    .progress {
        height: 10px;
        border-radius: 8px;
        overflow: hidden;
    }
    .progress-bar {
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .status-cleared { background: #d1fae5; color: #065f46; }
    .status-partial { background: #fef3c7; color: #92400e; }
    .status-unpaid  { background: #fee2e2; color: #991b1b; }
    [data-bs-theme="dark"] .status-cleared { background: #064e3b; color: #6ee7b7; }
    [data-bs-theme="dark"] .status-partial { background: #78350f; color: #fcd34d; }
    [data-bs-theme="dark"] .status-unpaid  { background: #7f1d1d; color: #fca5a5; }
    .overall-summary {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 1rem;
        color: #fff;
        padding: 1.75rem 2rem;
    }
    [data-bs-theme="dark"] .overall-summary {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .overall-stat { text-align: center; }
    .overall-stat-value { font-size: 1.5rem; font-weight: 800; }
    .overall-stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.7;
        margin-top: 0.25rem;
    }
    .term-payment-table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .term-card:hover { transform: none; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Term-wise Fee Record</h2>
        <small class="text-muted">View a student's fee breakdown by Term 1, 2 and 3 with balances.</small>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Reports</a>
</div>

{{-- Student Selector --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.term-wise-record') }}" method="GET" class="row g-3 align-items-end">
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
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Generate Record</button>
            </div>
        </form>
    </div>
</div>

@if($student && $termData)
<div class="print-area">
    {{-- Student Info --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <h4 class="fw-bold mb-1">{{ $student->name }}</h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-mortarboard me-1"></i> {{ $student->course }}
                        <span class="mx-2">•</span>
                        <i class="bi bi-envelope me-1"></i> {{ $student->email }}
                        @if($student->phone)
                            <span class="mx-2">•</span>
                            <i class="bi bi-telephone me-1"></i> {{ $student->phone }}
                        @endif
                    </p>
                </div>
                <button class="btn btn-outline-primary btn-sm no-print" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print Record
                </button>
            </div>
        </div>
    </div>

    {{-- Term Cards --}}
    <div class="row g-4 mb-4">
        @foreach($termData as $index => $term)
        <div class="col-md-4">
            <div class="card term-card border-0 shadow-sm h-100">
                <div class="term-header term-{{ $index + 1 }}-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-calendar3 me-2"></i>{{ $term->term }}</span>
                    <span class="badge rounded-pill
                        {{ $term->status === 'cleared' ? 'status-cleared' : ($term->status === 'partial' ? 'status-partial' : 'status-unpaid') }}"
                        style="font-size: 0.75rem;">
                        {{ ucfirst($term->status) }}
                    </span>
                </div>
                <div class="term-body">
                    {{-- Expected --}}
                    <div class="mb-3">
                        <div class="term-stat-label">Expected Fee</div>
                        <div class="term-stat-value">KES {{ number_format($term->expected, 2) }}</div>
                    </div>
                    {{-- Paid --}}
                    <div class="mb-3">
                        <div class="term-stat-label">Amount Paid</div>
                        <div class="term-stat-value text-success">KES {{ number_format($term->paid, 2) }}</div>
                    </div>
                    {{-- Balance --}}
                    <div class="mb-3">
                        <div class="term-stat-label">Balance</div>
                        <div class="term-stat-value {{ $term->balance > 0 ? 'text-danger' : 'text-success' }}">
                            KES {{ number_format($term->balance, 2) }}
                        </div>
                    </div>
                    {{-- Progress --}}
                    <div class="term-stat-label mb-1">Payment Progress — {{ $term->percent }}%</div>
                    <div class="progress">
                        <div class="progress-bar
                            {{ $term->percent >= 100 ? 'bg-success' : ($term->percent >= 50 ? 'bg-warning' : 'bg-danger') }}"
                            role="progressbar"
                            style="width: {{ $term->percent }}%"
                            aria-valuenow="{{ $term->percent }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Overall Summary --}}
    @if($overall)
    <div class="overall-summary mb-4">
        <div class="row">
            <div class="col-md-4 overall-stat">
                <div class="overall-stat-value">KES {{ number_format($overall->expected, 2) }}</div>
                <div class="overall-stat-label">Total Expected</div>
            </div>
            <div class="col-md-4 overall-stat">
                <div class="overall-stat-value" style="color: #34d399;">KES {{ number_format($overall->paid, 2) }}</div>
                <div class="overall-stat-label">Total Paid</div>
            </div>
            <div class="col-md-4 overall-stat">
                <div class="overall-stat-value" style="color: {{ $overall->balance > 0 ? '#f87171' : '#34d399' }};">
                    KES {{ number_format($overall->balance, 2) }}
                </div>
                <div class="overall-stat-label">Outstanding Balance</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Detailed Payment History by Term --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
            <h5 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Payment History by Term</h5>
        </div>
        <div class="card-body">
            @foreach($termData as $index => $term)
            <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3 mt-{{ $index > 0 ? '4' : '2' }}">
                <i class="bi bi-caret-right-fill me-1"></i>{{ $term->term }}
                <span class="float-end badge bg-secondary">{{ $term->fees->count() }} payment{{ $term->fees->count() !== 1 ? 's' : '' }}</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle term-payment-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Receipt No</th>
                            <th>Payment Method</th>
                            <th class="text-end">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($term->fees as $fee)
                        <tr>
                            <td>{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('d M Y') : '-' }}</td>
                            <td>{{ $fee->receipt_no ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $fee->payment_method }}</span>
                            </td>
                            <td class="text-end fw-bold text-success">{{ number_format($fee->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">
                                <i class="bi bi-inbox me-1"></i> No payments recorded for {{ $term->term }}.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($term->fees->count() > 0)
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="3" class="text-end">Subtotal:</th>
                            <th class="text-end text-success">{{ number_format($term->paid, 2) }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @endforeach
        </div>
    </div>
</div>
@elseif(request('student_id'))
<div class="text-center py-5">
    <i class="bi bi-person-x-fill display-4 text-muted"></i>
    <p class="text-muted mt-3">Student not found. Please select a valid student.</p>
</div>
@else
<div class="text-center py-5">
    <i class="bi bi-grid-3x3-gap display-4 text-muted" style="opacity: 0.4;"></i>
    <p class="text-muted mt-3">Select a student above to view their term-wise fee record.</p>
</div>
@endif

@endsection
