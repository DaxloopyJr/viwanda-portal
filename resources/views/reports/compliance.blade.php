@extends('layouts.app')

@section('title', 'Submission Compliance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active">Submission Compliance</li>
@endsection

@section('content')
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.consolidated') }}"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('reports.compliance') }}"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
    @can('reports.export')
    <li class="nav-item ms-auto"><a class="nav-link" href="{{ route('reports.export') }}"><i class="bi bi-download me-1"></i>Export CSV</a></li>
    @endcan
</ul>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-check2-square me-2"></i>Expected datasets vs. actual submissions</span>
        <div class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0 small text-muted">Period</label>
            <select id="filterPeriod" class="form-select form-select-sm" style="width:auto">
                @foreach($periods as $p)
                <option value="{{ $p }}" @selected($p === $period)>{{ $p }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="complianceTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Institution</th><th>Dataset</th><th>Frequency</th>
                        <th>Submission</th><th>Status</th><th>Compliance</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const complianceTable = vpDataTable('#complianceTable', '{{ route('reports.compliance.data', ['period' => $period]) }}', [
    { data: 'institution', name: 'institution' },
    { data: 'dataset', name: 'dataset' },
    { data: 'frequency', name: 'frequency' },
    { data: 'submission', name: 'submission' },
    { data: 'status', name: 'status' },
    { data: 'compliance', name: 'compliance' }
]);
document.getElementById('filterPeriod').addEventListener('change', function () {
    complianceTable.ajax.url('{{ route('reports.compliance.data') }}?period=' + encodeURIComponent(this.value)).load();
});
</script>
@endpush
