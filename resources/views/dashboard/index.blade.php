@extends('layouts.master')

@section('title', 'Dashboard - Student Management System')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12) !important;
    }
    .stat-card .card-body {
        padding: 1.4rem 1.5rem;
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.2rem;
    }
    .stat-value {
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.1;
        margin: 0;
    }
    .stat-badge {
        font-size: 0.7rem;
        padding: 0.25em 0.6em;
        border-radius: 0.4rem;
    }
    .chart-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    }
    .chart-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        border-radius: 1rem 1rem 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .chart-wrapper {
        position: relative;
        padding: 1.25rem;
    }
    .section-title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #adb5bd;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Dashboard Overview</h2>
        <small class="text-muted">Live data &mdash; {{ now()->format('l, d F Y') }}</small>
    </div>
    <div>
        <a href="{{ route('fees.create') }}" class="btn btn-success me-2">
            <i class="bi bi-cash me-1"></i> Add Payment
        </a>
        <a href="{{ route('students.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Student
        </a>
    </div>
</div>

{{-- ── Stat Cards Row 1 ── --}}
<p class="section-title">Key Metrics</p>
<div class="row g-3 mb-4">

    {{-- Total Students --}}
    <div class="col-12 col-md-4">
        <div class="card stat-card shadow-sm h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body d-flex flex-column gap-2">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Total Students</div>
                    <p class="stat-value text-primary">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Fees Collected --}}
    <div class="col-12 col-md-4">
        <div class="card stat-card shadow-sm h-100" style="border-left: 4px solid #198754;">
            <div class="card-body d-flex flex-column gap-2">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="stat-label">Fees Collected</div>
                    <p class="stat-value text-success" style="font-size:1.4rem;">KES {{ number_format($totalFeesCollected ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Collections --}}
    <div class="col-12 col-md-4">
        <div class="card stat-card shadow-sm h-100" style="border-left: 4px solid #ffc107;">
            <div class="card-body d-flex flex-column gap-2">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Today's Collections</div>
                    <p class="stat-value text-warning" style="font-size:1.4rem;">KES {{ number_format($todaysCollections ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

</div>



{{-- ── Charts Row ── --}}
<p class="section-title">Analytics</p>
<div class="row g-3 mb-4">

    {{-- Bar Chart: Monthly Fee Collections --}}
    <div class="col-lg-8">
        <div class="card chart-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Monthly Fee Collections</h6>
                <span class="badge bg-success bg-opacity-10 text-success">Last 6 Months</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="monthlyFeesChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Doughnut Chart: Course Breakdown --}}
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Course Distribution</h6>
            </div>
            <div class="chart-wrapper d-flex align-items-center justify-content-center">
                <canvas id="courseChart" height="200"></canvas>
            </div>
        </div>
    </div>

</div>


{{-- ── Tables Row ── --}}
<p class="section-title">Recent Activity</p>
<div class="row g-3 mb-4">

    {{-- Recent Transactions --}}
    <div class="col-lg-6">
        <div class="card chart-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Recent Transactions</h6>
                <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($recentTransactions ?? [] as $tx)
                        @php
                            // Extract initials
                            $words = explode(' ', trim($tx->student_name));
                            $initials = '';
                            if (count($words) > 0 && !empty($words[0])) {
                                $initials .= strtoupper($words[0][0]);
                                if (count($words) > 1 && !empty($words[1])) {
                                    $initials .= strtoupper($words[1][0]);
                                }
                            } else {
                                $initials = '?';
                            }
                            
                            $isMpesa = str_contains(strtolower($tx->method), 'mpesa');
                            $methodIcon = $isMpesa ? 'bi-phone' : 'bi-credit-card';
                            
                            // Status Config
                            if($tx->status === 'success') {
                                $statusColor = 'success';
                                $statusIcon = 'bi-check-circle-fill';
                                $statusText = 'Success';
                            } elseif($tx->status === 'failed') {
                                $statusColor = 'danger';
                                $statusIcon = 'bi-x-circle-fill';
                                $statusText = 'Failed';
                            } else {
                                $statusColor = 'warning';
                                $statusIcon = 'bi-clock-fill';
                                $statusText = 'Pending';
                                $textColor = 'text-dark'; // specialized for warning
                            }
                            $textColor = $textColor ?? 'text-'.$statusColor;
                        @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom bg-transparent" style="transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Premium Avatar -->
                                <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold shadow-sm" 
                                     style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--bs-primary), #6f42c1); font-size: 0.95rem; letter-spacing: 0.5px;">
                                    {{ $initials }}
                                </div>
                                <!-- Transaction Info -->
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $tx->student_name }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="text-secondary d-flex align-items-center gap-1" style="font-size: 0.8rem; font-weight: 500;">
                                            <i class="bi {{ $methodIcon }}"></i> {{ $tx->method }}
                                        </span>
                                        <span class="text-muted opacity-50" style="font-size: 0.4rem;">&#9679;</span>
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            {{ \Carbon\Carbon::parse($tx->date)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Amount & Status Badge -->
                            <div class="text-end">
                                <div class="fw-bold mb-1" style="font-size: 1.05rem; color: #212529; letter-spacing: -0.02em;">
                                    KES {{ number_format($tx->amount, 2) }}
                                </div>
                                <span class="badge bg-{{ $statusColor }} bg-opacity-10 {{ $textColor }} border border-{{ $statusColor }} border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.02em;">
                                    <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-5 text-center text-muted">
                            <i class="bi bi-inbox fs-2 mb-3 d-block text-black-50 opacity-50"></i>
                            <p class="mb-0 fw-medium">No recent transactions found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Top Paying Students --}}
    <div class="col-lg-6">
        <div class="card chart-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Top Paying Students</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Student</th>
                                <th>Total Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPayingStudents ?? [] as $i => $student)
                            <tr>
                                <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $student->name }}</td>
                                <td><span class="badge bg-warning text-dark">KES {{ number_format($student->fees_sum_amount, 2) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No fee records yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Bottom: Recent Students + Course Breakdown ── --}}
<div class="row g-3">

    {{-- Recent Students --}}
    <div class="col-lg-7">
        <div class="card chart-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Students</h6>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Student</th>
                                <th>Course</th>
                                <th>Enrolled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $student)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $student->name }}</td>
                                <td><span class="badge bg-info text-dark">{{ $student->course }}</span></td>
                                <td><small class="text-muted">{{ $student->created_at->diffForHumans() }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No students yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Course Breakdown List --}}
    <div class="col-lg-5">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Course Breakdown</h6>
            </div>
            <div class="card-body">
                @php
                    $maxCourse = $courseBreakdown->max('total') ?: 1;
                    $colors    = ['primary','info','success','warning','danger','secondary'];
                @endphp
                @forelse($courseBreakdown as $idx => $item)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold small">{{ $item->course }}</span>
                        <span class="badge bg-{{ $colors[$idx % count($colors)] }} bg-opacity-75">{{ $item->total }} students</span>
                    </div>
                    <div class="progress" style="height:7px; border-radius:4px;">
                        <div class="progress-bar bg-{{ $colors[$idx % count($colors)] }}"
                             style="width:{{ round(($item->total / $maxCourse) * 100) }}%">
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No course data available.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ── Student Fee Balances ── --}}
<p class="section-title mt-4">Student Fee Balances</p>
<div class="card chart-card mb-4">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-wallet-fill me-2 text-primary"></i>Fee Balances &amp; Paid Fees
        </h6>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            {{-- Status filter tabs --}}
            <div class="btn-group btn-group-sm" role="group" id="feeFilterGroup">
                <button type="button" class="btn btn-primary fee-filter active" data-filter="all">All</button>
                <button type="button" class="btn btn-outline-danger fee-filter" data-filter="unpaid">Unpaid</button>
                <button type="button" class="btn btn-outline-warning fee-filter" data-filter="partial">Partial</button>
                <button type="button" class="btn btn-outline-success fee-filter" data-filter="cleared">Cleared</button>
            </div>
            {{-- Search --}}
            <input type="text" id="feeSearch" class="form-control form-control-sm" placeholder="Search student…" style="width:180px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="feeBalanceTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Total Fee</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th style="min-width:140px;">Progress</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeBalances as $idx => $fb)
                    @php
                        $pct = $fb->total > 0 ? round(($fb->paid / $fb->total) * 100) : 0;
                        $barColor = $fb->status === 'cleared' ? 'success'
                                  : ($fb->status === 'partial'  ? 'warning'
                                                                : 'danger');
                    @endphp
                    <tr class="fee-row" data-status="{{ $fb->status }}" data-name="{{ strtolower($fb->name) }}">
                        <td class="ps-4 text-muted">{{ $idx + 1 }}</td>
                        <td class="fw-semibold">{{ $fb->name }}</td>
                        <td><span class="badge bg-info text-dark">{{ $fb->course }}</span></td>
                        <td class="text-muted">KES {{ number_format($fb->total, 2) }}</td>
                        <td class="text-success fw-bold">KES {{ number_format($fb->paid, 2) }}</td>
                        <td class="{{ $fb->balance > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                            {{ $fb->balance > 0 ? 'KES '.number_format($fb->balance, 2) : '—' }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px; border-radius:4px;">
                                    <div class="progress-bar bg-{{ $barColor }}" style="width:{{ $pct }}%"></div>
                                </div>
                                <small class="text-muted" style="min-width:36px; text-align:right;">{{ $pct }}%</small>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($fb->status === 'cleared')
                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Cleared</span>
                            @elseif($fb->status === 'partial')
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill me-1"></i>Partial</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Unpaid</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('fees.create') }}?student_id={{ $fb->id }}"
                               class="btn btn-sm btn-outline-primary" title="Add Payment">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>No students found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- Summary row --}}
                @if($feeBalances->count())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="ps-4 text-muted">Totals ({{ $feeBalances->count() }} students)</td>
                        <td class="text-muted">KES {{ number_format($feeBalances->sum('total'), 2) }}</td>
                        <td class="text-success">KES {{ number_format($feeBalances->sum('paid'), 2) }}</td>
                        <td class="text-danger">KES {{ number_format($feeBalances->sum('balance'), 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection


@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // ── Pass PHP data to JS ──
    const monthlyFeesData = @json($monthlyFees);
    const enrollmentData  = @json($monthlyEnrollments);
    const courseData      = @json($courseBreakdown);

    const chartMonths     = monthlyFeesData.map(d => d.month);
    const chartFeeAmounts = monthlyFeesData.map(d => parseFloat(d.amount));
    const chartEnrolCount = enrollmentData.map(d => d.count);
    const courseLabels    = courseData.map(d => d.course);
    const courseCounts    = courseData.map(d => d.total);

    // Shared chart defaults
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.plugins.legend.labels.boxWidth = 12;

    const palette = ['#0d6efd','#0dcaf0','#198754','#ffc107','#dc3545','#6f42c1','#fd7e14','#20c997'];

    // ── 1. Bar Chart: Monthly Fee Collections ──
    new Chart(document.getElementById('monthlyFeesChart'), {
        type: 'bar',
        data: {
            labels: chartMonths,
            datasets: [{
                label: 'Fees Collected (KES)',
                data: chartFeeAmounts,
                backgroundColor: 'rgba(25, 135, 84, 0.75)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' KES ' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' },
                    ticks: {
                        callback: v => 'KES ' + v.toLocaleString()
                    }
                }
            }
        }
    });

    // ── 2. Doughnut Chart: Course Distribution ──
    new Chart(document.getElementById('courseChart'), {
        type: 'doughnut',
        data: {
            labels: courseLabels.length ? courseLabels : ['No Data'],
            datasets: [{
                data: courseCounts.length ? courseCounts : [1],
                backgroundColor: palette.slice(0, courseLabels.length || 1),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} students`
                    }
                }
            }
        }
    });



    // ── Fee Balance Table: Filter + Search ──
    const feeRows   = document.querySelectorAll('.fee-row');
    const filterBtns = document.querySelectorAll('.fee-filter');
    const feeSearch  = document.getElementById('feeSearch');
    let activeFilter = 'all';

    function applyFeeFilters() {
        const query = feeSearch.value.toLowerCase().trim();
        feeRows.forEach(row => {
            const matchFilter = activeFilter === 'all' || row.dataset.status === activeFilter;
            const matchSearch = row.dataset.name.includes(query);
            row.style.display = (matchFilter && matchSearch) ? '' : 'none';
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => {
                b.classList.remove('active', 'btn-primary', 'btn-danger', 'btn-warning', 'btn-success');
                b.classList.add('btn-outline-' + (b.dataset.filter === 'all' ? 'primary'
                    : b.dataset.filter === 'unpaid'  ? 'danger'
                    : b.dataset.filter === 'partial' ? 'warning' : 'success'));
            });
            activeFilter = btn.dataset.filter;
            const colorMap = { all:'primary', unpaid:'danger', partial:'warning', cleared:'success' };
            btn.classList.remove('btn-outline-' + colorMap[activeFilter]);
            btn.classList.add('active', 'btn-' + colorMap[activeFilter]);
            applyFeeFilters();
        });
    });

    feeSearch.addEventListener('input', applyFeeFilters);
</script>
@endpush

