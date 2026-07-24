@extends('layouts.master')

@section('title', 'Edit Staff Member')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square text-warning me-2"></i> Edit Staff Member</h2>
    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Staff
    </a>
</div>

<div class="card shadow-sm border-0" style="max-width:700px;">
    <div class="card-body p-4">
        <form action="{{ route('staff.update', $staff) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $staff->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address *</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staff->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}" placeholder="e.g. 0712345678">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Role / Title *</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach(['Teacher', 'Head of Department', 'Principal', 'Deputy Principal', 'Librarian', 'Counselor', 'Administrator', 'Support Staff'] as $role)
                            <option value="{{ $role }}" {{ old('role', $staff->role) == $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" name="department" class="form-control" value="{{ old('department', $staff->department) }}" placeholder="e.g. Science & Mathematics">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Course / Subject Taught</label>
                    <select name="course" class="form-select @error('course') is-invalid @enderror">
                        <option value="">-- Select Course (Optional) --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c }}" {{ old('course', $staff->course) == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hire Date</label>
                    <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $staff->hire_date) }}">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                    <i class="bi bi-save me-1"></i> Update Staff Member
                </button>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
