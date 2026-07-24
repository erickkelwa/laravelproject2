@extends('layouts.master')

@section('title', 'Staff & Faculty Management - Student Management System')

@push('styles')
<style>
    .staff-summary-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .staff-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.1) !important;
    }
    .staff-icon {
        width: 50px;
        height: 50px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold"><i class="bi bi-person-workspace text-warning me-2"></i>Staff & Faculty Portal</h2>
        <small class="text-muted">Manage teachers, lecturers, departments & assigned courses</small>
    </div>
    <a href="{{ route('staff.create') }}" class="btn btn-warning text-dark fw-bold">
        <i class="bi bi-person-plus-fill me-1"></i> Add Staff / Teacher
    </a>
</div>

{{-- ── Summary Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="card staff-summary-card shadow-sm h-100" style="border-left: 4px solid #ffc107;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="staff-icon bg-warning bg-opacity-10 text-dark">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Staff Members</div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $totalStaff }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="card staff-summary-card shadow-sm h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="staff-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Teaching Staff / Lecturers</div>
                    <h4 class="mb-0 fw-bold text-primary">{{ $teacherCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card staff-summary-card shadow-sm h-100" style="border-left: 4px solid #198754;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="staff-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-book-half"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Assigned Courses</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $assignedCoursesCount }} <small class="fs-6 text-muted">active courses</small></h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filters Bar ── --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form action="{{ route('staff.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, role, or course…" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="course" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Filter by Course Taught (All)</option>
                    @foreach($courses as $c)
                        <option value="{{ $c }}" {{ request('course') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-3">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                @if(request('search') || request('course'))
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Staff Table ── --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">All Staff & Faculty Members</h5>
        <span class="badge bg-secondary px-3 py-2">{{ $staff->total() }} Members</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Course Taught</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $index => $member)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $staff->firstItem() + $index }}</td>
                            <td class="fw-bold">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-dark fw-bold shadow-sm" style="width:38px;height:38px;font-size:0.9rem;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $member->name }}</div>
                                        <small class="text-muted" style="font-size:0.72rem;">Hired: {{ $member->hire_date ? \Carbon\Carbon::parse($member->hire_date)->format('M Y') : 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark px-3 py-1 fw-bold">{{ $member->role }}</span>
                            </td>
                            <td>
                                @if($member->course)
                                    <span class="badge bg-info text-dark px-3 py-1 fw-bold">
                                        <i class="bi bi-book me-1"></i>{{ $member->course }}
                                    </span>
                                @else
                                    <span class="text-muted small">— None —</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $member->department ?? '—' }}</td>
                            <td><a href="mailto:{{ $member->email }}" class="text-decoration-none">{{ $member->email }}</a></td>
                            <td>{{ $member->phone ?? '—' }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Staff Member">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('staff.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this staff member?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                No staff members found. Click <strong>Add Staff / Teacher</strong> to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($staff->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $staff->links() }}
        </div>
    @endif
</div>
@endsection
