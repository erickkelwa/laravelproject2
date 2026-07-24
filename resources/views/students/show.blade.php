@extends('layouts.master')

@section('title', 'Student Details - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Student Details</h2>
    <div>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary me-2"><i class="bi bi-pencil me-1"></i> Edit</a>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-white text-center p-4">
            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <h4 class="fw-bold">{{ $student->name }}</h4>
            <span class="badge bg-info text-dark mb-3">{{ $student->course }}</span>
            <p class="text-muted mb-0">Student ID: #{{ $student->id }}</p>
            <p class="text-muted small">Registered: {{ $student->created_at->format('M d, Y') }}</p>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm border-0 bg-white h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-bold">Full Name</div>
                    <div class="col-sm-9">{{ $student->name }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-bold">Email</div>
                    <div class="col-sm-9"><a href="mailto:{{ $student->email }}" class="text-decoration-none">{{ $student->email }}</a></div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-bold">Phone</div>
                    <div class="col-sm-9">{{ $student->phone ?? 'Not provided' }}</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3 text-muted fw-bold">Course</div>
                    <div class="col-sm-9">{{ $student->course }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
