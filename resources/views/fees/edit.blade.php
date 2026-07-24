@extends('layouts.master')

@section('title', 'Edit Fee Payment - Student Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold">Edit Fee Payment</h2>
    <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card shadow-sm border-0 max-w-2xl mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('fees.update', $fee) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Student --}}
            <div class="mb-3">
                <label for="student_id" class="form-label fw-bold">Student <span class="text-danger">*</span></label>
                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ (old('student_id') ?? $fee->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->name }} ({{ $student->course }})
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Term --}}
            <div class="mb-3">
                <label for="term" class="form-label fw-bold">Term <span class="text-danger">*</span></label>
                <select name="term" id="term" class="form-select @error('term') is-invalid @enderror" required>
                    <option value="">Select Term</option>
                    <option value="Term 1" {{ (old('term') ?? $fee->term) == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                    <option value="Term 2" {{ (old('term') ?? $fee->term) == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                    <option value="Term 3" {{ (old('term') ?? $fee->term) == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                </select>
                @error('term')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Term Fee Due --}}
            <div class="mb-3">
                <label for="term_fee" class="form-label fw-bold">
                    Term Fee Due (KES)
                    <span class="text-muted fw-normal small">— how much is owed for this term</span>
                </label>
                <input type="number" step="0.01" min="0" name="term_fee" id="term_fee"
                       class="form-control @error('term_fee') is-invalid @enderror"
                       value="{{ old('term_fee', $fee->term_fee) }}">
                @error('term_fee')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="mb-3">
                <label for="amount" class="form-label fw-bold">Amount (KES) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="1" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $fee->amount) }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Payment Method --}}
            <div class="mb-3">
                <label for="payment_method" class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="">Select Method</option>
                    <option value="Cash" {{ (old('payment_method') ?? $fee->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Bank Transfer" {{ (old('payment_method') ?? $fee->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="Cheque" {{ (old('payment_method') ?? $fee->payment_method) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="Mpesa" {{ (old('payment_method') ?? $fee->payment_method) == 'Mpesa' ? 'selected' : '' }}>Mpesa</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Receipt Number --}}
            <div class="mb-3">
                <label for="receipt_no" class="form-label fw-bold">Receipt Number / Transaction ID</label>
                <input type="text" name="receipt_no" id="receipt_no" class="form-control @error('receipt_no') is-invalid @enderror" value="{{ old('receipt_no', $fee->receipt_no) }}">
                @error('receipt_no')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Payment Date --}}
            <div class="mb-4">
                <label for="payment_date" class="form-label fw-bold">Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $fee->payment_date->format('Y-m-d')) }}" required>
                @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Update Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
