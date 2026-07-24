@extends('layouts.master')

@section('title', 'Payment Details - Student Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold">Payment Details</h2>
    <div>
        <a href="{{ route('fees.edit', $fee) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 max-w-2xl mx-auto">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 text-success fw-bold"><i class="bi bi-receipt me-2"></i>Official Receipt</h5>
    </div>
    <div class="card-body p-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase fw-bold mb-1">Student Name</h6>
                <p class="fs-5">{{ $fee->student->name ?? 'Deleted Student' }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="text-muted text-uppercase fw-bold mb-1">Course</h6>
                <p class="fs-5 badge bg-info text-dark">{{ $fee->student->course ?? 'N/A' }}</p>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-muted fw-bold mb-1">Amount Paid</h6>
                <h3 class="text-success fw-bold">KES {{ number_format($fee->amount, 2) }}</h3>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted fw-bold mb-1">Payment Method</h6>
                <p class="fs-5 fw-semibold mb-0">
                    @if($fee->payment_method == 'Mpesa')
                        <span class="text-success"><i class="bi bi-phone"></i> Mpesa</span>
                    @else
                        {{ $fee->payment_method }}
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted fw-bold mb-1">Receipt Number</h6>
                <p class="fs-5">{{ $fee->receipt_no ?: 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted fw-bold mb-1">Payment Date</h6>
                <p class="fs-5">{{ $fee->payment_date->format('l, d F Y') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted fw-bold mb-1">Recorded On</h6>
                <p class="fs-6 text-muted">{{ $fee->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
