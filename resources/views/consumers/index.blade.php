@extends('layouts.app')

@section('title', 'Data Consumers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('datasets.index') }}">Data Catalogue</a></li>
    <li class="breadcrumb-item active">Data consumers</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Data Consumers</span>
        <a href="{{ route('consumers.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Consumer</a>
    </div>
    <div class="card-body">
        <p class="text-muted small">Consumers are the institutions or audiences that use the data (e.g. TR, MIT, Public). Officers pick from this list (multi-select) when creating a submission.</p>
        <div class="table-responsive">
            <table id="consumersTable" class="table table-hover align-middle w-100">
                <thead><tr><th>Code</th><th>Description</th><th>Scope</th><th>Status</th><th class="no-export">Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
vpDataTable('#consumersTable', '{{ route('consumers.datatable') }}', [
    { data: 'code' }, { data: 'name' }, { data: 'scope' },
    { data: 'status' }, { data: 'actions', orderable: false }
]);
</script>
@endpush
