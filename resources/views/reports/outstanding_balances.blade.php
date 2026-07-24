@extends('layouts.master')

@section('title', 'Outstanding Balances Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold">Outstanding Balances</h2>
        <small class="text-muted">Display students with unpaid balances.</small>
    </div>
    <div class="no-print">
        <button class="btn btn-outline-success" onclick="exportToCSV()"><i class="bi bi-file-earmark-excel"></i> Export Excel (CSV)</button>
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.outstanding-balances') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Course Filter</label>
                <select name="course" class="form-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>{{ $course }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="balancesTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Course</th>
                        <th class="text-end">Expected Fees (KES)</th>
                        <th class="text-end">Paid (KES)</th>
                        <th class="text-end pe-4">Balance (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalBalance = 0; @endphp
                    @forelse($balances as $b)
                        @php $totalBalance += $b->balance; @endphp
                        <tr>
                            <td class="ps-4 fw-bold">{{ $b->name }}</td>
                            <td>{{ $b->course ?? '-' }}</td>
                            <td class="text-end">{{ number_format($b->expected, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($b->paid, 2) }}</td>
                            <td class="text-end pe-4 text-danger fw-bold">{{ number_format($b->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No outstanding balances found.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($balances->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Total Arrears:</td>
                        <td class="text-end pe-4 text-danger">KES {{ number_format($totalBalance, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function exportToCSV() {
        const rows = document.querySelectorAll('#balancesTable tr');
        let csv = [];
        rows.forEach((row, i) => {
            const cols = row.querySelectorAll('th, td');
            let rowData = [];
            cols.forEach((col, j) => {
                rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
            });
            csv.push(rowData.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'outstanding_balances.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        .card, .card * { visibility: visible; }
        .no-print { display: none !important; }
        .card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
    }
</style>
@endpush
