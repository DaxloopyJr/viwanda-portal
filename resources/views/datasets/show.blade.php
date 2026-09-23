@extends('layouts.app')

@section('title', $dataset->code . ' — ' . $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('datasets.index') }}">Data Catalogue</a></li>
    <li class="breadcrumb-item active">{{ $dataset->code }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-text me-2"></i>Dataset Definition</span>
                @canany(['datasets.manage', 'datasets.manage-own'])
                <a href="{{ route('datasets.edit', $dataset) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                @endcanany
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Code</dt><dd class="col-sm-8"><code>{{ $dataset->code }}</code></dd>
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $dataset->name }}</dd>
                    <dt class="col-sm-4">Owner</dt><dd class="col-sm-8">
                        @if($dataset->institution) {{ $dataset->institution->name }} ({{ $dataset->institution->code }})
                        @elseif($dataset->ministryDepartment) Ministry — {{ $dataset->ministryDepartment->name }}
                        @else Ministry @endif
                        @if($dataset->department)<div class="text-muted small">Dept: {{ $dataset->department }}</div>@endif
                    </dd>
                    <dt class="col-sm-4">Frequency</dt><dd class="col-sm-8">{{ ucfirst($dataset->frequency) }}</dd>
                    <dt class="col-sm-4">Priority</dt><dd class="col-sm-8"><span class="badge text-bg-{{ $dataset->priority === 'high' ? 'danger' : ($dataset->priority === 'medium' ? 'warning' : 'secondary') }}">{{ ucfirst($dataset->priority) }}</span></dd>
                    <dt class="col-sm-4">Source system</dt><dd class="col-sm-8">{{ $dataset->source_system ?: '—' }}</dd>
                    <dt class="col-sm-4">Consumers</dt><dd class="col-sm-8">{{ $dataset->consumers ?: '—' }}</dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $dataset->is_active ? 'Active' : 'Inactive' }}</dd>
                    <dt class="col-sm-4">Description</dt><dd class="col-sm-8">{{ $dataset->description ?: '—' }}</dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-table me-2"></i>Data Fields ({{ count($dataset->fields ?? []) }})</div>
            <div class="table-responsive">
                <table id="fieldsTable" class="table table-sm table-striped w-100">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Required</th></tr></thead>
                    <tbody>
                        @foreach($dataset->fields ?? [] as $field)
                        <tr>
                            <td><code>{{ $field['name'] ?? '' }}</code></td>
                            <td>{{ $field['label'] ?? '' }}</td>
                            <td>{{ $field['type'] ?? 'string' }}</td>
                            <td>{!! !empty($field['required']) ? '<span class="badge text-bg-danger">Yes</span>' : '<span class="text-muted">No</span>' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox me-2"></i>Recent Submissions ({{ $dataset->submissions_count }} total)</span>
                @can('submissions.create')
                <a href="{{ route('submissions.create', ['dataset' => $dataset->code]) }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Submission</a>
                @endcan
            </div>
            <div class="table-responsive">
                <table id="recentTable" class="table table-hover align-middle w-100">
                    <thead><tr><th>Reference</th><th>Period</th><th>Status</th><th>Records</th><th>Submitted</th></tr></thead>
                    <tbody>
                        @forelse($recent as $s)
                        <tr>
                            <td><a href="{{ route('submissions.show', $s) }}" class="text-decoration-none"><code>{{ $s->reference }}</code></a></td>
                            <td>{{ $s->reporting_period }}</td>
                            <td><span class="badge text-bg-{{ $s->statusBadge() }}">{{ $s->statusLabel() }}</span></td>
                            <td>{{ $s->records_count }}</td>
                            <td class="text-muted small">{{ $s->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No submissions for this dataset yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('submissions.create')
        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-upload me-2"></i>Upload CSV for this dataset</div>
            <div class="card-body">
                <form method="POST" action="{{ route('submissions.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="dataset_id" value="{{ $dataset->id }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Reporting Period <span class="text-danger">*</span></label>
                            @if($periods->isNotEmpty())
                            <select name="reporting_period" class="form-select" required>
                                @foreach($periods as $p)<option value="{{ $p->name }}">{{ $p->name }}</option>@endforeach
                            </select>
                            @else
                            <input name="reporting_period" class="form-control" placeholder="e.g. {{ now()->format('Y') }} - Q{{ (int) ceil(now()->month/3) }}" required>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">CSV file <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success w-100"><i class="bi bi-upload me-1"></i>Upload</button>
                        </div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Columns:
                        @foreach($dataset->fields as $f)<code>{{ $f['name'] }}</code>{{ $loop->last ? '' : ', ' }}@endforeach.
                        <a href="{{ route('datasets.template', $dataset) }}">Download the CSV template</a>.</p>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection

@push('scripts')
<script>
vpDataTable('#recentTable', null, null, { pageLength: 10, order: [] });
vpDataTable('#fieldsTable', null, null, { pageLength: 10, order: [] });
</script>
@endpush
