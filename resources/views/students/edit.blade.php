@extends('layouts.master')

@section('title', 'Edit Student - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Student: {{ $student->name }}</h2>
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

                <form action="{{ route('students.update', $student) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                    </div>
                    
                    <div class="mb-4">
                        <label for="course" class="form-label fw-bold">Course Enrolled</label>
                        <select class="form-select" id="course" name="course" required>
                            <option value="" disabled>Select a course...</option>
                            <option value="Computer Science" {{ old('course', $student->course) == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                            <option value="Information Technology" {{ old('course', $student->course) == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                            <option value="Business Administration" {{ old('course', $student->course) == 'Business Administration' ? 'selected' : '' }}>Business Administration</option>
                            <option value="Data Science" {{ old('course', $student->course) == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                            <option value="Cyber Security" {{ old('course', $student->course) == 'Cyber Security' ? 'selected' : '' }}>Cyber Security</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="total_fee" class="form-label fw-bold">Total Course Fee (Ksh)</label>
                        <input type="number" step="0.01" class="form-control" id="total_fee" name="total_fee" value="{{ old('total_fee', $student->total_fee) }}" required>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-4">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
