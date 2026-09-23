@extends('layouts.app')
@section('title', 'Edit '.$submission->reference)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Submissions</a></li>
    <li class="breadcrumb-item"><a href="{{ route('submissions.show', $submission) }}">{{ $submission->reference }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('submissions.update', $submission) }}">
        @csrf @method('PUT')
        <div class="row g-3 mb-3">
            <div class="col-md-4"><label class="form-label">Dataset</label><input class="form-control" value="{{ $submission->dataset->code }} — {{ $submission->dataset->name }}" disabled></div>
            <div class="col-md-4"><label class="form-label">Reporting Period <span class="text-danger">*</span></label><input name="reporting_period" class="form-control" value="{{ old('reporting_period', $submission->reporting_period) }}" required></div>
            <div class="col-md-4"><label class="form-label">Status</label><input class="form-control" value="{{ $submission->statusLabel() }}" disabled></div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Records</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addRow"><i class="bi bi-plus-lg"></i> Add row</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="recordsTable">
                <thead><tr>
                    <th style="width:40px">#</th>
                    @foreach($submission->dataset->fields as $f)<th>{{ $f['label'] }} @if($f['required'] ?? false)<span class="text-danger">*</span>@endif</th>@endforeach
                    <th style="width:50px"></th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between">
            <a href="{{ route('submissions.show', $submission) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-success"><i class="bi bi-save"></i> Save changes</button>
        </div>
    </form>
</div></div>

<template id="rowTemplate">
    <tr>
        <td class="row-num text-muted"></td>
        @foreach($submission->dataset->fields as $f)
        <td>
            @if(!empty($f['options']))
                <select data-field="{{ $f['name'] }}" class="form-select form-select-sm">
                    <option value="">—</option>
                    @foreach($f['options'] as $opt)<option value="{{ $opt }}">{{ $opt }}</option>@endforeach
                </select>
            @elseif(($f['type'] ?? 'string') === 'date')
                <input type="date" data-field="{{ $f['name'] }}" class="form-control form-control-sm">
            @elseif(($f['type'] ?? 'string') === 'text')
                <textarea data-field="{{ $f['name'] }}" class="form-control form-control-sm" rows="1"></textarea>
            @else
                <input data-field="{{ $f['name'] }}" class="form-control form-control-sm">
            @endif
        </td>
        @endforeach
        <td><button type="button" class="btn btn-sm btn-outline-danger delRow"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
const tbody = document.querySelector('#recordsTable tbody');
const tpl = document.getElementById('rowTemplate');
const fields = @json(array_column($submission->dataset->fields, 'name'));
const existing = @json($submission->records->pluck('data'));

function renumber() {
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.querySelector('.row-num').textContent = i + 1;
        tr.querySelectorAll('[data-field]').forEach(el => { el.name = `records[${i}][${el.dataset.field}]`; });
    });
}
function addRow(data = {}) {
    const tr = tpl.content.cloneNode(true);
    tr.querySelector('.delRow').addEventListener('click', e => { e.target.closest('tr').remove(); renumber(); });
    const row = tr.querySelector('tr');
    row.querySelectorAll('[data-field]').forEach(el => { if (data[el.dataset.field] !== undefined) el.value = data[el.dataset.field]; });
    tbody.appendChild(tr);
    renumber();
}
document.getElementById('addRow').addEventListener('click', () => addRow());
existing.forEach(r => addRow(r));
if (!existing.length) addRow();
</script>
@endpush
