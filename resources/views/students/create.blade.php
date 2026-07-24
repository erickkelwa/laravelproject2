@extends('layouts.master')

@section('title', 'Add Student - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add New Student</h2>
    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-body p-4">
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('students.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="e.g. john@example.com">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. +1 234 567 890">
                    </div>
                    
                    <div class="mb-4">
                        <label for="course" class="form-label fw-bold">Course Enrolled</label>
                        <select class="form-select" id="course" name="course" required>
                            <option value="" selected disabled>Select a course...</option>
                            <option value="Computer Science" {{ old('course') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                            <option value="Information Technology" {{ old('course') == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                            <option value="Business Administration" {{ old('course') == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                            <option value="Data Science" {{ old('course') == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                            <option value="Cyber Security" {{ old('course') == 'Cyber Security' ? 'selected' : '' }}>Cyber Security</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="total_fee" class="form-label fw-bold">Total Course Fee (Ksh)</label>
                        <input type="number" step="0.01" class="form-control" id="total_fee" name="total_fee" value="{{ old('total_fee', 0) }}" required placeholder="e.g. 50000">
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-light me-md-2">Reset</button>
                        <button type="submit" class="btn btn-primary px-4">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i> Instructions</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">Please fill in all the required fields to add a new student to the system. Email addresses must be unique.</p>
                <ul class="text-muted small ps-3">
                    <li>Name: Required</li>
                    <li>Email: Required, Unique</li>
                    <li>Phone: Optional</li>
                    <li>Course: Required</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
