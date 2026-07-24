@extends('layouts.master')

@section('title', 'Payments - Student Management System')

@push('styles')
<style>
    /* ── Summary Cards ── */
    .fee-summary-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .fee-summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12) !important;
    }
    .fee-summary-card .card-body {
        padding: 1.5rem;
    }
    .fee-summary-icon {
        width: 56px;
        height: 56px;
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .fee-summary-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.15rem;
    }
    .fee-summary-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
    }

    /* ── Term Cards ── */
    .term-summary-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .term-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.1) !important;
    }
    .term-summary-card .term-header {
        padding: 1rem 1.25rem;
        font-weight: 800;
        font-size: 0.95rem;
        letter-spacing: 0.04em;
    }
    .term-summary-card .term-body {
        padding: 1.25rem;
    }
    .term-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.45rem 0;
    }
    .term-row:not(:last-child) {
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .term-row-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
    }
    .term-row-value {
        font-size: 1rem;
        font-weight: 700;
    }
    .progress-thick {
        height: 10px;
        border-radius: 6px;
    }

    /* ── Section Title ── */
    .section-title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #adb5bd;
        margin-bottom: 0.75rem;
    }

    /* ── Chart Card ── */
    .chart-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    }
    .chart-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        border-radius: 1rem 1rem 0 0 !important;
        padding: 1rem 1.25rem;
    }

    /* ── Table Enhancements ── */
    .student-fee-table th {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #6c757d;
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Payments</h2>
        <small class="text-muted">School-wide payments overview &mdash; {{ now()->format('l, d F Y') }}</small>
    </div>
    <a href="{{ route('fees.create') }}" class="btn btn-success">
        <i class="bi bi-cash me-1"></i> Add Payment
    </a>
</div>

{{-- ══════════════════════════════════════════════════════
     SECTION 5: RECENT PAYMENT RECORDS
     ══════════════════════════════════════════════════════ --}}
<p class="section-title" id="recent-payments">Recent Payment Records</p>
<div class="card chart-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2 text-success"></i>Payment History
        </h6>
        <div>
            <form action="{{ route('fees.index') }}" method="GET" class="d-flex gap-2 mb-0">
                <select name="method" class="form-select form-select-sm shadow-sm" style="width: 150px; font-weight: 600;" onchange="this.form.submit()">
                    <option value="">All Methods</option>
                    <option value="MPESA" {{ strcasecmp(request('method') ?? '', 'MPESA') == 0 ? 'selected' : '' }}>M-Pesa</option>
                    <option value="Bank Transfer" {{ strcasecmp(request('method') ?? '', 'Bank Transfer') == 0 ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="Cash" {{ strcasecmp(request('method') ?? '', 'Cash') == 0 ? 'selected' : '' }}>Cash</option>
                    <option value="Cheque" {{ strcasecmp(request('method') ?? '', 'Cheque') == 0 ? 'selected' : '' }}>Cheque</option>
                </select>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #343a40; color: #ffffff; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                    <tr>
                        <th class="ps-4 py-3 border-0 rounded-start">#</th>
                        <th class="py-3 border-0">Student</th>
                        <th class="py-3 border-0">Course</th>
                        <th class="py-3 border-0">Amount (KES)</th>
                        <th class="py-3 border-0">Method</th>
                        <th class="py-3 border-0 text-center">Status</th>
                        <th class="py-3 border-0">Reference</th>
                        <th class="py-3 border-0">Date</th>
                        <th class="pe-4 py-3 border-0 text-end rounded-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $idx => $fee)
                    <tr class="align-middle" style="border-bottom: 1px solid #f1f3f5;">
                        <td class="ps-4 fw-medium text-dark">{{ $idx + 1 + ($fees->currentPage() - 1) * $fees->perPage() }}</td>
                        <td class="fw-bold text-dark">{{ $fee->student->name ?? 'Deleted Student' }}</td>
                        <td class="text-secondary small">{{ $fee->course ?? '—' }}</td>
                        <td class="fw-bold text-dark">{{ number_format($fee->amount, 2) }}</td>
                        <td>
                            @php
                                $method = strtolower(trim($fee->method ?? ''));
                                $ismpesa = in_array($method, ['mpesa', 'mpesa stk', 'm-pesa']);
                                $iscash  = $method === 'cash';
                                $isbank  = str_contains($method, 'bank');
                            @endphp

                            @if($ismpesa)
                                <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded fw-bold"
                                      style="background:#e8f7ee; border:1px solid #00a651; font-size:0.75rem; color:#00a651;">
                                    <i class="bi bi-phone"></i> M-Pesa
                                </span>
                            @elseif($iscash)
                                <span class="d-inline-flex align-items-center gap-1 px-3 py-1 rounded fw-bold"
                                      style="background:#f8f9fa; border:1px solid #dee2e6; font-size:0.75rem; color:#495057;">
                                    Cash
                                </span>
                            @elseif($isbank)
                                <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded fw-bold"
                                      style="background:#e8f0fe; border:1px solid #1a73e8; font-size:0.75rem; color:#1a73e8;">
                                    Bank Transfer
                                </span>
                            @else
                                <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded fw-bold"
                                      style="background:#f1f3f4; border:1px solid #adb5bd; font-size:0.75rem; color:#495057;">
                                    {{ $fee->method }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($fee->status === 'Completed')
                                <span class="badge rounded px-3 py-1" style="background-color: #198754; font-size: 0.75rem; font-weight: 600;">Completed</span>
                            @elseif($fee->status === 'Pending')
                                <span class="badge rounded px-3 py-1 text-dark" style="background-color: #ffc107; font-size: 0.75rem; font-weight: 600;">Pending</span>
                            @elseif($fee->status === 'Failed')
                                <span class="badge rounded px-3 py-1" style="background-color: #dc3545; font-size: 0.75rem; font-weight: 600;">Failed</span>
                            @else
                                <span class="badge rounded px-3 py-1" style="background-color: #6c757d; font-size: 0.75rem; font-weight: 600;">N/A</span>
                            @endif
                        </td>
                        <td class="text-secondary" style="font-family: monospace; font-size: 0.85rem;">{{ $fee->reference }}</td>
                        <td class="text-secondary small">{{ \Carbon\Carbon::parse($fee->date)->format('Y-m-d') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                @if($fee->model_type === 'fee')
                                    <a href="{{ route('fees.show', $fee->id) }}" class="btn btn-sm d-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: #e8f0fe; color: #1a73e8; border: none;" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('fees.edit', $fee->id) }}" class="btn btn-sm d-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: #fff3cd; color: #ffc107; border: none;" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="{{ route('fees.receipt', $fee->id) }}" class="btn btn-sm d-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: #d1e7dd; color: #198754; border: none;" title="Download Receipt"><i class="bi bi-download"></i></a>
                                    <form action="{{ route('fees.destroy', $fee->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this payment record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: #f8d7da; color: #dc3545; border: none;" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                @else
                                    <button class="btn btn-sm d-flex align-items-center justify-content-center rounded opacity-50" style="width: 28px; height: 28px; background: #e8f0fe; color: #1a73e8; border: none;" title="Pending transaction cannot be edited" disabled><i class="bi bi-eye"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-cash-stack fs-1 d-block mb-2"></i>
                            No fee payments recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3 border-0">
        {{ $fees->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Payment specific scripts
</script>
@endpush
