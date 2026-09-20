@extends('layouts.app')
@section('title', $dataset->code.' — '.$dataset->name)
@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span>Dataset definition</span>
                @can('datasets.manage')<a href="{{ route('datasets.edit', $dataset) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>@endcan
            </div>
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-4">Code</dt><dd class="col-8">{{ $dataset->code }}</dd>
                    <dt class="col-4">Name</dt><dd class="col-8">{{ $dataset->name }}</dd>
                    <dt class="col-4">Description</dt><dd class="col-8">{{ $dataset->description }}</dd>
                    <dt class="col-4">Institution</dt><dd class="col-8">{{ $dataset->institution->name }}</dd>
                    <dt class="col-4">Frequency</dt><dd class="col-8">{{ $dataset->frequency }}</dd>
                    <dt class="col-4">Priority</dt><dd class="col-8">{{ ucfirst($dataset->priority) }}</dd>
                    <dt class="col-4">Source system</dt><dd class="col-8">{{ $dataset->source_system ?? 'None (manual entry)' }}</dd>
                    <dt class="col-4">Consumers</dt><dd class="col-8">{{ $dataset->consumers ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Data dictionary ({{ count($dataset->fields ?? []) }} fields)</span>
                <a href="{{ route('datasets.template', $dataset) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> CSV template</a>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Required</th><th>Allowed values</th></tr></thead>
                    <tbody>
                    @foreach($dataset->fields as $f)
                        <tr>
                            <td><code>{{ $f['name'] }}</code></td>
                            <td>{{ $f['label'] }}</td>
                            <td>{{ $f['type'] ?? 'string' }}</td>
                            <td>{!! ($f['required'] ?? false) ? '<span class="badge text-bg-danger">Mandatory</span>' : '<span class="badge text-bg-secondary">Optional</span>' !!}</td>
                            <td class="small">{{ !empty($f['options']) ? implode(', ', $f['options']) : '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Recent submissions ({{ $dataset->submissions_count }})</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Reference</th><th>Period</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recent as $s)
                        <tr>
                            <td><a href="{{ route('submissions.show', $s) }}">{{ $s->reference }}</a></td>
                            <td>{{ $s->reporting_period }}</td>
                            <td><span class="badge text-bg-{{ $s->statusBadge() }}">{{ str_replace('_',' ',ucfirst($s->status)) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No submissions yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
