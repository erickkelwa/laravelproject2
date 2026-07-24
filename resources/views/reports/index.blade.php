@extends('layouts.master')

@section('title', 'Reports & Analytics Dashboard')

@push('styles')
<style>
    .report-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 100%;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        color: inherit;
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Reports & Analytics Dashboard</h2>
        <small class="text-muted">Overview of Financial Performance and Reporting Modules</small>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <a href="{{ route('reports.student-statement') }}" class="card report-card p-4">
            <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-person-lines-fill"></i></div>
            <h6 class="fw-bold mb-1">1. Student Statement</h6>
            <small class="text-muted">Individual fee statements</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.fee-collection') }}" class="card report-card p-4">
            <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-cash-stack"></i></div>
            <h6 class="fw-bold mb-1">2. Fee Collection</h6>
            <small class="text-muted">Collections within a period</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.outstanding-balances') }}" class="card report-card p-4">
            <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
            <h6 class="fw-bold mb-1">3. Outstanding Balances</h6>
            <small class="text-muted">Students with unpaid fees</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.course-revenue') }}" class="card report-card p-4">
            <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-journal-bookmark"></i></div>
            <h6 class="fw-bold mb-1">4. Course Revenue</h6>
            <small class="text-muted">Revenue by course</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.daily-collection') }}" class="card report-card p-4">
            <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-day"></i></div>
            <h6 class="fw-bold mb-1">5. Daily Collection</h6>
            <small class="text-muted">Today's collections</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.monthly-collection') }}" class="card report-card p-4">
            <div class="icon-box bg-purple bg-opacity-10" style="color: purple;"><i class="bi bi-calendar-month"></i></div>
            <h6 class="fw-bold mb-1">6. Monthly Collection</h6>
            <small class="text-muted">Monthly revenue performance</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.mpesa-transactions') }}" class="card report-card p-4">
            <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-phone"></i></div>
            <h6 class="fw-bold mb-1">7. M-Pesa Transactions</h6>
            <small class="text-muted">Monitor M-Pesa transactions</small>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.payment-methods') }}" class="card report-card p-4">
            <div class="icon-box bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-pie-chart"></i></div>
            <h6 class="fw-bold mb-1">8. Payment Methods</h6>
            <small class="text-muted">Analysis of collection methods</small>
        </a>
    </div>
</div>

<h4 class="fw-bold mb-3">Financial Overview</h4>
<div class="row g-4">
    <!-- Chart 1: Monthly Revenue Trend -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">Monthly Revenue Trend</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart 2: Payment Method Distribution -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">Payment Method Distribution</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="chart-container" style="height: 250px;">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 3: Revenue by Course -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">Revenue by Course</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 350px;">
                    <canvas id="courseRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Monthly Trend
        const monthlyData = @json($monthlyRevenue);
        if(monthlyData.length > 0) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const labels = monthlyData.map(d => months[d.month - 1] + ' ' + d.year);
            const totals = monthlyData.map(d => d.total);
            
            new Chart(document.getElementById('monthlyTrendChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (KES)',
                        data: totals,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // 2. Payment Methods
        const methodData = @json($paymentMethods);
        if(methodData.length > 0) {
            new Chart(document.getElementById('paymentMethodChart'), {
                type: 'doughnut',
                data: {
                    labels: methodData.map(d => d.payment_method),
                    datasets: [{
                        data: methodData.map(d => d.total_amount),
                        backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#dc3545', '#6f42c1'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // 3. Course Revenue
        const courseData = @json($revenueByCourse);
        if(courseData.length > 0) {
            new Chart(document.getElementById('courseRevenueChart'), {
                type: 'bar',
                data: {
                    labels: courseData.map(d => d.course || 'Unassigned'),
                    datasets: [{
                        label: 'Revenue (KES)',
                        data: courseData.map(d => d.total_revenue),
                        backgroundColor: 'rgba(111, 66, 193, 0.7)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });
</script>
@endpush
