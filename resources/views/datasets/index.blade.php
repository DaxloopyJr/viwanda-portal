@extends('layouts.app')
@section('title', 'Data Catalogue')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Approved datasets and their data dictionary definitions.</p>
    @can('datasets.manage')<a href="{{ route('datasets.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Dataset</a>@endcan
</div>
<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped table-hover mb-0">
        <thead><tr><th>Code</th><th>Name</th><th>Institution</th><th>Frequency</th><th>Priority</th><th>Fields</th><th>Submissions</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($datasets as $ds)
            <tr>
                <td class="fw-semibold">{{ $ds->code }}</td>
                <td>{{ $ds->name }}</td>
                <td>{{ $ds->institution->code }}</td>
                <td>{{ $ds->frequency }}</td>
                <td><span class="badge text-bg-{{ $ds->priority === 'high' ? 'danger' : ($ds->priority === 'medium' ? 'warning' : 'secondary') }}">{{ ucfirst($ds->priority) }}</span></td>
                <td>{{ count($ds->fields ?? []) }}</td>
                <td>{{ $ds->submissions_count }}</td>
                <td><span class="badge text-bg-{{ $ds->is_active ? 'success' : 'secondary' }}">{{ $ds->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td><a href="{{ route('datasets.show', $ds) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($datasets->hasPages())<div class="card-footer">{{ $datasets->links() }}</div>@endif
</div>
@endsection
