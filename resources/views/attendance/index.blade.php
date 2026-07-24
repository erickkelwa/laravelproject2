@extends('layouts.master')

@section('title', 'Attendance Management - Student Management System')

@push('styles')
<style>
    /* ── Attendance Cards ── */
    .att-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .att-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.1) !important;
    }
    .att-icon {
        width: 50px;
        height: 50px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    /* ── Matrix Table Styling ── */
    .weekly-matrix-table th {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        vertical-align: middle;
    }
    .day-col-header {
        min-width: 110px;
    }
    .day-col-header.active-day {
        background-color: rgba(13, 110, 253, 0.12) !important;
        border-bottom: 3px solid #0d6efd !important;
    }
    .att-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35rem 0.6rem;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .att-badge-present { background-color: #d1e7dd; color: #0f5132; }
    .att-badge-absent { background-color: #f8d7da; color: #842029; }
    .att-badge-late { background-color: #fff3cd; color: #664d03; }
    .att-badge-excused { background-color: #e2e3e5; color: #41464b; }
    .att-badge-unmarked { background-color: #f8f9fa; color: #adb5bd; border: 1px dashed #ced4da; }

    /* ── Quick Action Buttons ── */
    .btn-quick-action {
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 0.6rem;
        padding: 0.4rem 0.85rem;
        transition: all 0.2s ease;
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="mb-0 fw-bold">
            <i class="bi bi-calendar-check-fill text-primary me-2"></i>Attendance Management
        </h2>
        <small class="text-muted">
            Monday to Friday Weekly Overview & Teacher Daily Register
        </small>
    </div>

    {{-- Week Navigation Buttons --}}
    @php
        $prevWeek = \Carbon\Carbon::parse($startOfWeek)->subWeek()->format('Y-m-d');
        $nextWeek = \Carbon\Carbon::parse($startOfWeek)->addWeek()->format('Y-m-d');
        $today    = date('Y-m-d');
    @endphp
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('attendance.index', ['date' => $prevWeek, 'course' => $courseFilter, 'search' => $search]) }}"
           class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-chevron-left me-1"></i> Prev Week
        </a>
        <a href="{{ route('attendance.index', ['date' => $today, 'course' => $courseFilter, 'search' => $search]) }}"
           class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
            Today
        </a>
        <a href="{{ route('attendance.index', ['date' => $nextWeek, 'course' => $courseFilter, 'search' => $search]) }}"
           class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            Next Week <i class="bi bi-chevron-right ms-1"></i>
        </a>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     SECTION 1: WEEKLY KPI SUMMARY CARDS
     ══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Total Students --}}
    <div class="col-6 col-lg-3">
        <div class="card att-card shadow-sm h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="att-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Students</div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $students->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Rate --}}
    <div class="col-6 col-lg-3">
        <div class="card att-card shadow-sm h-100" style="border-left: 4px solid #198754;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="att-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
                <div class="w-100">
                    <div class="text-muted small fw-bold text-uppercase">Weekly Attendance Rate</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $weeklyAttendanceRate }}%</h4>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $weeklyAttendanceRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Present Days --}}
    <div class="col-6 col-lg-3">
        <div class="card att-card shadow-sm h-100" style="border-left: 4px solid #0dcaf0;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="att-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Days Present</div>
                    <h4 class="mb-0 fw-bold text-info">{{ $totalPresentCount }} <small class="fs-6 text-muted">days</small></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Absent Days --}}
    <div class="col-6 col-lg-3">
        <div class="card att-card shadow-sm h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="att-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Days Absent</div>
                    <h4 class="mb-0 fw-bold text-danger">{{ $totalAbsentCount }} <small class="fs-6 text-muted">days</small></h4>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     SECTION 2: FILTERS & NAVIGATION BAR
     ══════════════════════════════════════════════════════ --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form action="{{ route('attendance.index') }}" method="GET" class="row g-2 align-items-center">
            {{-- Date Selector --}}
            <div class="col-md-3">
                <label for="date" class="form-label small fw-bold text-muted mb-1">Select Date / Week:</label>
                <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
            </div>

            {{-- Course Filter --}}
            <div class="col-md-3">
                <label for="course" class="form-label small fw-bold text-muted mb-1">Course Filter:</label>
                <select name="course" id="course" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c }}" {{ $courseFilter == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div class="col-md-4">
                <label for="search" class="form-label small fw-bold text-muted mb-1">Search Student:</label>
                <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Search by name or email…" value="{{ $search }}">
            </div>

            <div class="col-md-2 text-end mt-4">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="bi bi-funnel me-1"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     SECTION 3: TEACHER TABS (WEEKLY MATRIX vs DAILY REGISTER)
     ══════════════════════════════════════════════════════ --}}
<ul class="nav nav-pills mb-3 gap-2" id="attendanceTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'weekly' ? 'active' : '' }} fw-bold px-4 rounded-pill" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly-pane" type="button" role="tab">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Weekly View (Mon – Fri)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'daily' ? 'active' : '' }} fw-bold px-4 rounded-pill" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily-pane" type="button" role="tab">
            <i class="bi bi-pencil-square me-2"></i>Teacher Register (Daily Marking)
        </button>
    </li>
</ul>

<div class="tab-content" id="attendanceTabsContent">

    {{-- ──────────────────────────────────────────────────
         TAB 1: WEEKLY ATTENDANCE MATRIX (MON - FRI)
         ────────────────────────────────────────────────── --}}
    <div class="tab-pane fade {{ $activeTab === 'weekly' ? 'show active' : '' }}" id="weekly-pane" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar3 me-2 text-primary"></i>Weekly Attendance Grid
                    </h5>
                    <small class="text-muted">
                        Showing Monday {{ \Carbon\Carbon::parse($weekDates['Monday'])->format('M d') }} to Friday {{ \Carbon\Carbon::parse($weekDates['Friday'])->format('M d, Y') }}
                    </small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2">
                    Week of {{ \Carbon\Carbon::parse($startOfWeek)->format('M d, Y') }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 weekly-matrix-table">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Student</th>
                                <th>Course</th>
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $dayName)
                                    @php
                                        $dDate = $weekDates[$dayName];
                                        $isToday = $dDate === date('Y-m-d');
                                        $isSelected = $dDate === $date;
                                    @endphp
                                    <th class="text-center day-col-header {{ $isSelected ? 'active-day' : '' }}">
                                        <div>{{ substr($dayName, 0, 3) }}</div>
                                        <small class="fw-normal text-muted" style="font-size:0.7rem;">
                                            {{ \Carbon\Carbon::parse($dDate)->format('d M') }}
                                        </small>
                                        @if($isToday)
                                            <span class="badge bg-primary text-white d-block mt-1" style="font-size:0.6rem;">TODAY</span>
                                        @endif
                                    </th>
                                @endforeach
                                <th class="text-center">Present / Week</th>
                                <th class="text-center">Absent</th>
                                <th class="text-end pe-4">Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $idx => $student)
                                @php
                                    $presentCount = 0;
                                    $absentCount  = 0;
                                    $totalRecorded = 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                    <td class="fw-bold">{{ $student->name }}</td>
                                    <td><span class="badge bg-info text-dark">{{ $student->course }}</span></td>

                                    {{-- Monday to Friday Cells --}}
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $dayName)
                                        @php
                                            $dayDate = $weekDates[$dayName];
                                            $record  = $weeklyMatrix[$student->id][$dayDate] ?? null;
                                            $status  = $record ? $record->status : null;

                                            if ($status === 'Present' || $status === 'Late') { $presentCount++; $totalRecorded++; }
                                            elseif ($status === 'Absent') { $absentCount++; $totalRecorded++; }
                                            elseif ($status === 'Excused') { $totalRecorded++; }
                                        @endphp
                                        <td class="text-center">
                                            @if($status === 'Present')
                                                <span class="att-badge att-badge-present" title="Present">
                                                    <i class="bi bi-check-circle-fill"></i> Present
                                                </span>
                                            @elseif($status === 'Absent')
                                                <span class="att-badge att-badge-absent" title="Absent: {{ $record->remarks ?? 'No remark' }}">
                                                    <i class="bi bi-x-circle-fill"></i> Absent
                                                </span>
                                            @elseif($status === 'Late')
                                                <span class="att-badge att-badge-late" title="Late">
                                                    <i class="bi bi-clock-fill"></i> Late
                                                </span>
                                            @elseif($status === 'Excused')
                                                <span class="att-badge att-badge-excused" title="Excused">
                                                    <i class="bi bi-info-circle-fill"></i> Excused
                                                </span>
                                            @else
                                                <a href="{{ route('attendance.index', ['date' => $dayDate, 'tab' => 'daily', 'course' => $courseFilter, 'search' => $search]) }}"
                                                   class="att-badge att-badge-unmarked text-decoration-none" title="Click to mark attendance for {{ $dayName }}">
                                                    <i class="bi bi-dash-circle me-1"></i> Mark
                                                </a>
                                            @endif
                                        </td>
                                    @endforeach

                                    {{-- Totals --}}
                                    @php
                                        $ratePct = $totalRecorded > 0 ? round(($presentCount / $totalRecorded) * 100, 0) : 0;
                                        $badgeColor = $ratePct >= 80 ? 'bg-success' : ($ratePct >= 50 ? 'bg-warning text-dark' : 'bg-danger');
                                    @endphp
                                    <td class="text-center fw-bold text-success">
                                        {{ $presentCount }} / 5
                                    </td>
                                    <td class="text-center fw-bold text-danger">
                                        {{ $absentCount }}
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($totalRecorded > 0)
                                            <span class="badge {{ $badgeColor }} fs-7">{{ $ratePct }}%</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-2"></i>No students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- ──────────────────────────────────────────────────
         TAB 2: TEACHER DAILY REGISTER (MARKING FORM)
         ────────────────────────────────────────────────── --}}
    <div class="tab-pane fade {{ $activeTab === 'daily' ? 'show active' : '' }}" id="daily-pane" role="tabpanel">
        {{-- Quick Day Selector Pills --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="fw-bold text-muted small">
                    <i class="bi bi-calendar-event me-1"></i>Select Day to Mark:
                </div>
                <div class="btn-group" role="group">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $dayName)
                        @php
                            $dDate = $weekDates[$dayName];
                            $isCurrentActive = $dDate === $date;
                        @endphp
                        <a href="{{ route('attendance.index', ['date' => $dDate, 'tab' => 'daily', 'course' => $courseFilter, 'search' => $search]) }}"
                           class="btn btn-sm {{ $isCurrentActive ? 'btn-primary' : 'btn-outline-secondary' }} px-3">
                            {{ $dayName }}
                            <small class="d-block text-opacity-75" style="font-size:0.65rem;">
                                {{ \Carbon\Carbon::parse($dDate)->format('M d') }}
                            </small>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0 fw-bold">
                        Mark Attendance for <span class="text-primary">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</span>
                    </h5>
                </div>

                {{-- Teacher Bulk Action Buttons --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small fw-bold me-1">Quick Fill:</span>
                    <button type="button" class="btn btn-outline-success btn-quick-action" onclick="markAll('Present')">
                        <i class="bi bi-check-all me-1"></i> All Present
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-quick-action" onclick="markAll('Absent')">
                        <i class="bi bi-x-lg me-1"></i> All Absent
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-quick-action text-dark" onclick="markAll('Late')">
                        <i class="bi bi-clock me-1"></i> All Late
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <form action="{{ route('attendance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Student</th>
                                    <th>Course</th>
                                    <th class="text-center">Status</th>
                                    <th>Remarks / Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    @php
                                        $att = $dailyAttendances->get($student->id);
                                        $status = $att ? $att->status : 'Present';
                                        $remarks = $att ? $att->remarks : '';
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $student->name }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $student->course }}</span></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Status for {{ $student->name }}">
                                                {{-- Present --}}
                                                <input type="radio" class="btn-check att-radio" name="attendance[{{ $student->id }}][status]"
                                                       id="status_p_{{ $student->id }}" value="Present" {{ $status == 'Present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm px-3" for="status_p_{{ $student->id }}">
                                                    <i class="bi bi-check-lg"></i> Present
                                                </label>

                                                {{-- Absent --}}
                                                <input type="radio" class="btn-check att-radio" name="attendance[{{ $student->id }}][status]"
                                                       id="status_a_{{ $student->id }}" value="Absent" {{ $status == 'Absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm px-3" for="status_a_{{ $student->id }}">
                                                    <i class="bi bi-x-lg"></i> Absent
                                                </label>

                                                {{-- Late --}}
                                                <input type="radio" class="btn-check att-radio" name="attendance[{{ $student->id }}][status]"
                                                       id="status_l_{{ $student->id }}" value="Late" {{ $status == 'Late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning btn-sm px-3" for="status_l_{{ $student->id }}">
                                                    <i class="bi bi-clock"></i> Late
                                                </label>

                                                {{-- Excused --}}
                                                <input type="radio" class="btn-check att-radio" name="attendance[{{ $student->id }}][status]"
                                                       id="status_e_{{ $student->id }}" value="Excused" {{ $status == 'Excused' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-secondary btn-sm px-3" for="status_e_{{ $student->id }}">
                                                    <i class="bi bi-info-circle"></i> Excused
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="attendance[{{ $student->id }}][remarks]"
                                                   class="form-control form-control-sm" placeholder="Optional remark (e.g. Sick, Permission…)"
                                                   value="{{ $remarks }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->count() > 0)
                        <div class="p-3 bg-light border-top text-end">
                            <button type="submit" class="btn btn-success px-5 fw-bold">
                                <i class="bi bi-save me-2"></i> Save Attendance Register
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // ── Teacher Quick Mark All Function ──
    function markAll(status) {
        const valMap = {
            'Present': 'status_p_',
            'Absent':  'status_a_',
            'Late':    'status_l_',
            'Excused': 'status_e_'
        };

        const prefix = valMap[status];
        if (!prefix) return;

        const radios = document.querySelectorAll(`input[id^="${prefix}"]`);
        radios.forEach(radio => {
            radio.checked = true;
        });
    }
</script>
@endpush
