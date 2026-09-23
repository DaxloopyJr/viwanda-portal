@extends('layouts.app')

@section('title', 'Submission Periods')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('datasets.index') }}">Data Catalogue</a></li>
    <li class="breadcrumb-item active">Submission periods</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar3 me-2"></i>Submission Periods</span>
        <a href="{{ route('periods.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Period</a>
    </div>
    <div class="card-body">
        <p class="text-muted small">Periods follow each dataset's submission frequency — e.g. <code>2026 - Q1</code>, <code>2026 - Q2</code> for quarterly datasets, <code>2026 - Annually</code> for annual ones. Officers pick from this list when creating a submission.</p>
        <div class="table-responsive">
            <table id="periodsTable" class="table table-hover align-middle w-100">
                <thead><tr><th>Period</th><th>Scope</th><th>Frequency</th><th>Window</th><th>Status</th><th class="no-export">Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
vpDataTable('#periodsTable', '{{ route('periods.datatable') }}', [
    { data: 'name' }, { data: 'scope' }, { data: 'frequency' },
    { data: 'window' }, { data: 'status' }, { data: 'actions', orderable: false }
], { order: [[0, 'desc']] });
</script>
@endpush
