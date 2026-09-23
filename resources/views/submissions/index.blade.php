@extends('layouts.app')
@section('title', 'Submissions')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Submissions</a></li>
    <li class="breadcrumb-item active">All submissions</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2">
        <select id="filterStatus" class="form-select">
            <option value="">All statuses</option>
            @foreach($statuses as $st)<option value="{{ $st }}">{{ \App\Models\Submission::STATUS_LABELS[$st] ?? ucfirst(str_replace('_',' ',$st)) }}</option>@endforeach
        </select>
        <select id="filterPeriod" class="form-select">
            <option value="">All periods</option>
            @foreach($periods as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach
        </select>
    </div>
    @can('submissions.create')
    <a href="{{ route('submissions.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Submission</a>
    @endcan
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table id="submissionsTable" class="table table-striped table-hover w-100">
            <thead><tr>
                <th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th>
                <th>Channel</th><th>Status</th><th>Records</th><th>Submitted By</th><th>Created</th>
            </tr></thead>
        </table>
    </div>
</div></div>
@endsection

@push('scripts')
<script>
const subsTable = vpDataTable('#submissionsTable', '{{ route('submissions.datatable') }}', [
    { data: 'reference', name: 'reference' },
    { data: 'institution', name: 'institution' },
    { data: 'dataset', name: 'dataset' },
    { data: 'period', name: 'period' },
    { data: 'channel', name: 'channel' },
    { data: 'status', name: 'status' },
    { data: 'records', name: 'records' },
    { data: 'submitter', name: 'submitter' },
    { data: 'created', name: 'created' }
], { order: [[8, 'desc']] });

function reloadWithFilters() {
    const params = new URLSearchParams();
    const st = document.getElementById('filterStatus').value;
    const pe = document.getElementById('filterPeriod').value;
    if (st) params.set('status', st);
    if (pe) params.set('period', pe);
    subsTable.ajax.url('{{ route('submissions.datatable') }}' + (params.toString() ? '?' + params.toString() : '')).load();
}
document.getElementById('filterStatus').addEventListener('change', reloadWithFilters);
document.getElementById('filterPeriod').addEventListener('change', reloadWithFilters);
</script>
@endpush
