@extends('layouts.app')
@section('title', 'New Submission')
@section('content')
@if(!$dataset)
<div class="card"><div class="card-body">
    <h5 class="mb-3">Select the dataset to report</h5>
    <div class="row g-3">
        @foreach($datasets as $ds)
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between">
                    <strong>{{ $ds->code }}</strong>
                    <span class="badge text-bg-{{ $ds->priority === 'high' ? 'danger' : ($ds->priority === 'medium' ? 'warning' : 'secondary') }}">{{ ucfirst($ds->priority) }}</span>
                </div>
                <div class="fw-semibold">{{ $ds->name }}</div>
                <p class="text-muted small flex-grow-1">{{ Str::limit($ds->description, 110) }}</p>
                <div class="small text-muted">{{ $ds->institution->name }} · {{ $ds->frequency }}</div>
                <a href="{{ route('submissions.create', ['dataset' => $ds->code]) }}" class="btn btn-sm btn-primary mt-2 align-self-start">Enter data</a>
            </div>
        </div>
        @endforeach
    </div>
</div></div>
@else
<div class="mb-3">
    <a href="{{ route('submissions.create') }}" class="btn btn-sm btn-outline-secondary">&larr; Change dataset</a>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#manual">Manual Entry</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#upload">CSV File Upload</button></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="manual">
<form method="POST" action="{{ route('submissions.store') }}">
    @csrf
    <input type="hidden" name="dataset_id" value="{{ $dataset->id }}">
    <div class="card mb-3"><div class="card-body row g-3">
        <div class="col-md-4">
            <label class="form-label">Dataset</label>
            <input class="form-control" value="{{ $dataset->code }} — {{ $dataset->name }}" disabled>
        </div>
        <div class="col-md-3">
            <label class="form-label">Reporting Period <span class="text-danger">*</span></label>
            <input name="reporting_period" class="form-control" placeholder="e.g. {{ now()->format('Y') }}-Q{{ ceil(now()->month/3) }}" required>
        </div>
        <div class="col-md-5">
            <label class="form-label">Transaction Reference (optional, prevents duplicates)</label>
            <input name="transaction_reference" class="form-control" placeholder="e.g. {{ $dataset->code }}-{{ now()->format('Y') }}-Q{{ ceil(now()->month/3) }}-001">
        </div>
    </div></div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Records — {{ $dataset->name }}</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addRow"><i class="bi bi-plus-lg"></i> Add row</button>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table mb-0" id="recordsTable">
                <thead><tr>
                    <th>#</th>
                    @foreach($dataset->fields as $f)
                        <th>{{ $f['label'] }} @if($f['required'] ?? false)<span class="text-danger">*</span>@endif</th>
                    @endforeach
                    <th></th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-success"><i class="bi bi-save"></i> Save as Draft</button>
        </div>
    </div>
</form>
</div>

<div class="tab-pane fade" id="upload">
    <div class="card"><div class="card-body">
        <p>Upload a CSV file matching the dataset template. The file is validated on receipt; rows with errors are highlighted for correction.</p>
        <p><a href="{{ route('datasets.template', $dataset) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Download CSV template ({{ $dataset->code }})</a></p>
        <form method="POST" action="{{ route('submissions.upload') }}" enctype="multipart/form-data" class="row g-3">
            @csrf
            <input type="hidden" name="dataset_id" value="{{ $dataset->id }}">
            <div class="col-md-3">
                <label class="form-label">Reporting Period <span class="text-danger">*</span></label>
                <input name="reporting_period" class="form-control" placeholder="e.g. {{ now()->format('Y') }}-Q{{ ceil(now()->month/3) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">CSV File <span class="text-danger">*</span></label>
                <input type="file" name="file" class="form-control" accept=".csv" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-success"><i class="bi bi-upload"></i> Upload &amp; Validate</button>
            </div>
        </form>
    </div></div>
</div>
</div>

<template id="rowTemplate">
    <tr>
        <td class="row-num text-muted"></td>
        @foreach($dataset->fields as $f)
        <td>
            @php $type = $f['type'] ?? 'string'; @endphp
            @if(!empty($f['options']))
                <select class="form-select form-select-sm field-input" data-field="{{ $f['name'] }}">
                    <option value="">—</option>
                    @foreach($f['options'] as $opt)<option value="{{ $opt }}">{{ $opt }}</option>@endforeach
                </select>
            @elseif($type === 'text')
                <textarea class="form-control form-control-sm field-input" data-field="{{ $f['name'] }}" rows="1"></textarea>
            @else
                <input class="form-control form-control-sm field-input" data-field="{{ $f['name'] }}"
                       type="{{ $type === 'date' ? 'date' : ($type === 'integer' || $type === 'number' ? 'number' : 'text') }}">
            @endif
        </td>
        @endforeach
        <td><button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>
@endif
@endsection

@if($dataset)
@push('scripts')
<script>
const tbody = document.querySelector('#recordsTable tbody');
const tpl = document.getElementById('rowTemplate');
function addRow() {
    const tr = tpl.content.cloneNode(true);
    tbody.appendChild(tr);
    renumber();
}
function renumber() {
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.querySelector('.row-num').textContent = i + 1;
        tr.querySelectorAll('.field-input').forEach(inp => inp.name = `records[${i}][${inp.dataset.field}]`);
    });
}
document.getElementById('addRow').addEventListener('click', addRow);
tbody.addEventListener('click', e => { if (e.target.closest('.removeRow')) { e.target.closest('tr').remove(); renumber(); } });
addRow();
</script>
@endpush
@endif
