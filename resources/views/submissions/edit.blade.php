@extends('layouts.app')
@section('title', 'Edit '.$submission->reference)
@section('content')
<form method="POST" action="{{ route('submissions.update', $submission) }}">
    @csrf @method('PUT')
    <div class="card mb-3"><div class="card-body row g-3">
        <div class="col-md-4">
            <label class="form-label">Dataset</label>
            <input class="form-control" value="{{ $submission->dataset->code }} — {{ $submission->dataset->name }}" disabled>
        </div>
        <div class="col-md-3">
            <label class="form-label">Reporting Period</label>
            <input name="reporting_period" class="form-control" value="{{ old('reporting_period', $submission->reporting_period) }}" required>
        </div>
    </div></div>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Records</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addRow"><i class="bi bi-plus-lg"></i> Add row</button>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table mb-0" id="recordsTable">
                <thead><tr><th>#</th>@foreach($submission->dataset->fields as $f)<th>{{ $f['label'] }}</th>@endforeach<th></th></tr></thead>
                <tbody>
                @foreach($submission->records as $record)
                    <tr>
                        <td class="row-num text-muted">{{ $record->row_number }}</td>
                        @foreach($submission->dataset->fields as $f)
                        <td>
                            @php $type = $f['type'] ?? 'string'; $val = old("records.".$loop->parent->index.".".$f['name'], $record->data[$f['name']] ?? ''); @endphp
                            @if(!empty($f['options']))
                                <select class="form-select form-select-sm field-input" data-field="{{ $f['name'] }}">
                                    <option value="">—</option>
                                    @foreach($f['options'] as $opt)<option value="{{ $opt }}" @selected($val === $opt)>{{ $opt }}</option>@endforeach
                                </select>
                            @elseif($type === 'text')
                                <textarea class="form-control form-control-sm field-input" data-field="{{ $f['name'] }}" rows="1">{{ $val }}</textarea>
                            @else
                                <input class="form-control form-control-sm field-input" data-field="{{ $f['name'] }}" value="{{ $val }}"
                                       type="{{ $type === 'date' ? 'date' : ($type === 'integer' || $type === 'number' ? 'number' : 'text') }}">
                            @endif
                        </td>
                        @endforeach
                        <td><button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="bi bi-trash"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('submissions.show', $submission) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-success"><i class="bi bi-save"></i> Save changes</button>
        </div>
    </div>
</form>

<template id="rowTemplate">
    <tr>
        <td class="row-num text-muted"></td>
        @foreach($submission->dataset->fields as $f)
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
@endsection

@push('scripts')
<script>
const tbody = document.querySelector('#recordsTable tbody');
const tpl = document.getElementById('rowTemplate');
function renumber() {
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.querySelector('.row-num').textContent = i + 1;
        tr.querySelectorAll('.field-input').forEach(inp => inp.name = `records[${i}][${inp.dataset.field}]`);
    });
}
document.getElementById('addRow').addEventListener('click', () => { tbody.appendChild(tpl.content.cloneNode(true)); renumber(); });
tbody.addEventListener('click', e => { if (e.target.closest('.removeRow')) { e.target.closest('tr').remove(); renumber(); } });
renumber();
</script>
@endpush
