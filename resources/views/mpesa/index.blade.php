@extends('layouts.master')

@section('title', 'M-Pesa Transactions - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>M-Pesa Transactions</h2>
</div>

<div class="card shadow-sm border-0 bg-white mb-4">
    <div class="card-body">
        <form action="{{ route('mpesa.index') }}" method="GET" class="d-flex w-75">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by Receipt No, Phone, or Date (YYYY-MM-DD)..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
            @if(request('search'))
                <a href="{{ route('mpesa.index') }}" class="btn btn-link">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 bg-white mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Student</th>
                        <th>Phone</th>
                        <th>Amount</th>
                        <th>Receipt No</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="ps-4 text-secondary">#{{ $transaction->id }}</td>
                            <td>
                                @if($transaction->student)
                                    <a href="{{ route('students.show', $transaction->student->id) }}" class="text-decoration-none fw-bold">
                                        {{ $transaction->student->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Unknown Student</span>
                                @endif
                            </td>
                            <td>{{ $transaction->phone_number }}</td>
                            <td class="fw-bold text-success">Ksh {{ number_format($transaction->amount, 2) }}</td>
                            <td>
                                @if($transaction->receipt_number)
                                    <span class="badge bg-secondary">{{ $transaction->receipt_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->status == 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($transaction->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-3"></i>
                                No M-Pesa transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($transactions->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
