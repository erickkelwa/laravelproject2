@extends('layouts.master')

@section('title', 'Payment Method Analysis Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Payment Method Analysis</h2>
        <small class="text-muted">Compare fee collection methods.</small>
    </div>
    <div class="no-print">
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Payment Method</th>
                        <th class="text-center">Total Transactions</th>
                        <th class="text-end pe-4">Total Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; $grandTx = 0; @endphp
                    @forelse($methods as $method)
                        @php 
                            $grandTotal += $method->total_amount; 
                            $grandTx += $method->transactions;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2">
                                    {{ $method->payment_method }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">{{ $method->transactions }}</td>
                            <td class="text-end pe-4 text-success fw-bold">{{ number_format($method->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No payment methods found.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($methods->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-end ps-4 text-uppercase">Grand Total:</td>
                        <td class="text-center text-dark">{{ $grandTx }}</td>
                        <td class="text-end pe-4 text-primary fs-5">KES {{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
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
