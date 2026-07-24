@extends('layouts.master')

@section('title', 'Exam Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-journal-text text-success me-2"></i> Exam Results</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addResultModal">
        <i class="bi bi-plus-circle me-1"></i> Add Result
    </button>
</div>

{{-- Term Filter --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('exams.index') }}" method="GET" class="d-flex align-items-center w-50">
            <label for="term" class="form-label fw-bold me-3 mb-0">Select Term:</label>
            <select name="term" id="term" class="form-select me-2">
                @foreach(['Term 1 - 2026', 'Term 2 - 2026', 'Term 3 - 2026'] as $t)
                    <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
</div>

{{-- Results Table --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Results for: <span class="text-success">{{ $term }}</span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Grade</th>
                        <th>Remarks</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examResults as $index => $result)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $result->student->name }}</td>
                            <td>{{ $result->subject }}</td>
                            <td>{{ $result->score }}%</td>
                            <td>
                                @php
                                    $badgeClass = match($result->grade) {
                                        'A' => 'bg-success',
                                        'B' => 'bg-primary',
                                        'C' => 'bg-info text-dark',
                                        'D' => 'bg-warning text-dark',
                                        default => 'bg-danger',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} fs-6 px-3">{{ $result->grade }}</span>
                            </td>
                            <td class="text-muted">{{ $result->remarks ?? '—' }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('exams.destroy', $result) }}" method="POST" onsubmit="return confirm('Delete this result?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                No results found for this term. Click <strong>Add Result</strong> to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Result Modal --}}
<div class="modal fade" id="addResultModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('exams.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i>Add Exam Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Student</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Term / Semester</label>
                    <select name="term_or_semester" class="form-select" required>
                        @foreach(['Term 1 - 2026', 'Term 2 - 2026', 'Term 3 - 2026'] as $t)
                            <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Mathematics" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Score (%)</label>
                    <input type="number" name="score" class="form-control" min="0" max="100" step="0.1" required>
                    <small class="text-muted">Grade is calculated automatically: A (80+), B (70+), C (60+), D (50+), E (below 50)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Remarks (Optional)</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="Any extra comments..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="bi bi-save me-1"></i> Save Result
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
