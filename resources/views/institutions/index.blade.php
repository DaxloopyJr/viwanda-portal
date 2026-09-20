@extends('layouts.app')
@section('title', 'Institutions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Ministry departments and subordinate institutions reporting through the Portal.</p>
    <a href="{{ route('institutions.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Institution</a>
</div>
<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped table-hover mb-0">
        <thead><tr><th>Code</th><th>Name</th><th>Contact</th><th>Integration</th><th>Datasets</th><th>Submissions</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($institutions as $inst)
            <tr>
                <td class="fw-semibold">{{ $inst->code }}</td>
                <td>{{ $inst->name }}</td>
                <td class="small">{{ $inst->contact_email }}</td>
                <td><span class="badge text-bg-{{ $inst->integration_mode === 'api' ? 'info' : 'light' }}">{{ strtoupper($inst->integration_mode) }}</span></td>
                <td>{{ $inst->datasets_count }}</td>
                <td>{{ $inst->submissions_count }}</td>
                <td><span class="badge text-bg-{{ $inst->is_active ? 'success' : 'secondary' }}">{{ $inst->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td><a href="{{ route('institutions.edit', $inst) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($institutions->hasPages())<div class="card-footer">{{ $institutions->links() }}</div>@endif
</div>
@endsection
