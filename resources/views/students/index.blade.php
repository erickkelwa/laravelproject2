@extends('layouts.master')

@section('title', 'Students List - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Students Management</h2>
    <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Student</a>
</div>

<div class="card shadow-sm border-0 bg-white mb-4">
    <div class="card-body">
        <form action="{{ route('students.index') }}" method="GET" class="d-flex w-50">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name, email or course..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
            @if(request('search'))
                <a href="{{ route('students.index') }}" class="btn btn-link">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Fee Balance</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">#{{ $student->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $student->name }}</h6>
                                        <small class="text-muted">{{ $student->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td><span class="badge bg-info text-dark">{{ $student->course }}</span></td>
                            <td>
                                @if($student->balance > 0)
                                    <span class="text-danger fw-bold">Ksh {{ number_format($student->balance, 2) }}</span>
                                @elseif($student->total_fee > 0)
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Cleared</span>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-secondary me-1" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($students->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $students->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
