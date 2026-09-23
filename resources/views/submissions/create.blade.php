@extends('layouts.app')
@section('title', 'New Submission')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Submissions</a></li>
    <li class="breadcrumb-item active">New submission</li>
@endsection

@section('content')
<form method="POST" action="{{ route('submissions.store') }}" id="batchForm">
@csrf

{{-- Step 1: reporting period + consumers --}}
<div class="card mb-3">
    <div class="card-header"><span class="badge text-bg-primary me-2">1</span>Reporting period &amp; consumers</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Reporting Period <span class="text-danger">*</span></label>
                @if($periods->isNotEmpty())
                <select id="periodSelect" class="form-select" required>
                    <option value="">— Select reporting period —</option>
                    @foreach($periods as $p)
                    <option value="{{ $p->name }}" data-frequency="{{ strtolower($p->frequency) }}">{{ $p->name }}
                        ({{ ucfirst($p->frequency) }})</option>
                    @endforeach
                </select>
                <input type="hidden" name="reporting_period" id="periodInput" value="{{ old('reporting_period') }}">
                <div class="form-text">Configured by your administrator under Data Catalogue → Submission Periods.</div>
                @else
                <input name="reporting_period" id="periodInput" class="form-control" placeholder="e.g. {{ now()->format('Y') }} - Q{{ (int) ceil(now()->month/3) }}" value="{{ old('reporting_period') }}" required>
                <div class="form-text text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No submission periods are configured yet — ask your administrator to configure them, or type a period.</div>
                @endif
            </div>
            <div class="col-md-5">
                <label class="form-label">Data Consumers</label>
                <select name="consumers[]" class="form-select" multiple size="4">
                    @foreach($consumers as $c)
                    <option value="{{ $c->code }}" @selected(in_array($c->code, old('consumers', [])))>{{ $c->code }}{{ $c->name ? ' — '.$c->name : '' }}</option>
                    @endforeach
                </select>
                <div class="form-text">Hold Ctrl/Cmd to select multiple consumers of this data (e.g. TR, MIT, Public).</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Transaction Reference (optional)</label>
                <input name="transaction_reference" class="form-control" placeholder="Prevents duplicates" value="{{ old('transaction_reference') }}">
            </div>
        </div>
    </div>
</div>

{{-- Step 2: pick datasets --}}
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><span class="badge text-bg-primary me-2">2</span>Select datasets from your data catalogue</span>
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label" for="selectAll">Select all</label>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3" id="datasetHint">Select a reporting period first — the catalogue is filtered by the period's submission frequency.</p>
        <div class="row g-3" id="datasetPicker"></div>
        <p class="text-muted mb-0 d-none" id="noDatasets">No active datasets match this period's frequency. Contact your Institution Admin.</p>
    </div>
</div>

{{-- Step 3: data entry --}}
<div id="entrySection" class="d-none">
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><span class="badge text-bg-primary me-2">3</span>Enter records for each selected dataset</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="rebuildBtn"><i class="bi bi-arrow-repeat me-1"></i>Rebuild entry forms</button>
        </div>
        <div class="card-body">
            <div id="entryForms"></div>
        </div>
    </div>
    <div class="text-end mb-4">
        <button class="btn btn-success btn-lg"><i class="bi bi-save me-1"></i>Save batch as drafts</button>
    </div>
</div>
</form>
@endsection

@push('scripts')
@php
    $datasetPayload = $datasets->map(fn ($d) => [
        'id' => $d->id, 'code' => $d->code, 'name' => $d->name,
        'description' => \Illuminate\Support\Str::limit($d->description, 110),
        'frequency' => strtolower($d->frequency), 'priority' => $d->priority,
        'owner' => $d->ownerLabel(), 'fields' => $d->fields,
    ])->values();
@endphp
<script>
const DATASETS = @json($datasetPayload);
const PRESELECT = @json($preselect);

const picker = document.getElementById('datasetPicker');
const periodSelect = document.getElementById('periodSelect');
const periodInput = document.getElementById('periodInput');
const entrySection = document.getElementById('entrySection');
const entryForms = document.getElementById('entryForms');
const noDatasets = document.getElementById('noDatasets');

function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

function currentFrequency() {
    if (!periodSelect) return null;
    const opt = periodSelect.options[periodSelect.selectedIndex];
    return opt && opt.dataset.frequency ? opt.dataset.frequency : null;
}

function renderPicker() {
    const freq = currentFrequency();
    if (periodSelect) periodInput.value = periodSelect.value;
    picker.innerHTML = '';
    const list = freq ? DATASETS.filter(d => d.frequency === freq) : DATASETS;
    noDatasets.classList.toggle('d-none', list.length > 0);
    list.forEach(d => {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        col.innerHTML = `
            <label class="border rounded p-3 d-block h-100 dataset-card" style="cursor:pointer">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="fw-bold">${esc(d.code)}</span>
                    <input class="form-check-input ds-check" type="checkbox" value="${d.id}" ${PRESELECT === d.code ? 'checked' : ''}>
                </div>
                <div class="fw-semibold mt-1">${esc(d.name)}</div>
                <div class="text-muted small">${esc(d.description)}</div>
                <div class="small text-muted mt-1">${esc(d.owner)} · ${d.frequency.charAt(0).toUpperCase() + d.frequency.slice(1)} · ${d.fields.length} fields</div>
            </label>`;
        picker.appendChild(col);
    });
    picker.querySelectorAll('.ds-check').forEach(cb => cb.addEventListener('change', buildForms));
    document.getElementById('selectAll').checked = false;
    if (PRESELECT) buildForms();
}

function fieldInput(d, di, ri, fld) {
    const name = `datasets[${di}][records][${ri}][${fld.name}]`;
    if (fld.options && fld.options.length) {
        return `<select name="${name}" class="form-select form-select-sm"><option value="">—</option>` +
            fld.options.map(o => `<option value="${esc(o)}">${esc(o)}</option>`).join('') + '</select>';
    }
    if (fld.type === 'date') return `<input type="date" name="${name}" class="form-control form-control-sm">`;
    if (fld.type === 'text') return `<textarea name="${name}" class="form-control form-control-sm" rows="1"></textarea>`;
    if (fld.type === 'integer' || fld.type === 'number') return `<input type="number" step="any" name="${name}" class="form-control form-control-sm">`;
    return `<input name="${name}" class="form-control form-control-sm">`;
}

function addRow(tbody, d, di) {
    const ri = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td class="row-num text-muted">${ri + 1}</td>` +
        d.fields.map(fld => `<td>${fieldInput(d, di, ri, fld)}</td>`).join('') +
        `<td><button type="button" class="btn btn-sm btn-outline-danger delRow"><i class="bi bi-trash"></i></button></td>`;
    tr.querySelector('.delRow').addEventListener('click', () => {
        tr.remove();
        [...tbody.querySelectorAll('tr')].forEach((r, i) => r.querySelector('.row-num').textContent = i + 1);
    });
    tbody.appendChild(tr);
}

function buildForms() {
    const selected = DATASETS.filter(d => picker.querySelector(`.ds-check[value="${d.id}"]`)?.checked);
    entryForms.innerHTML = '';
    entrySection.classList.toggle('d-none', selected.length === 0);
    selected.forEach((d, di) => {
        const wrap = document.createElement('div');
        wrap.className = 'border rounded mb-4';
        wrap.innerHTML = `
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background:#f0f4fa">
                <div><code>${esc(d.code)}</code> <span class="fw-semibold">${esc(d.name)}</span></div>
                <button type="button" class="btn btn-sm btn-outline-primary addRowBtn"><i class="bi bi-plus-lg"></i> Add row</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead><tr><th style="width:40px">#</th>${d.fields.map(fld =>
                        `<th>${esc(fld.label)} ${fld.required ? '<span class="text-danger">*</span>' : ''}</th>`).join('')}<th style="width:50px"></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <input type="hidden" name="datasets[${di}][id]" value="${d.id}">`;
        entryForms.appendChild(wrap);
        const tbody = wrap.querySelector('tbody');
        wrap.querySelector('.addRowBtn').addEventListener('click', () => addRow(tbody, d, di));
        addRow(tbody, d, di);
    });
}

document.getElementById('selectAll').addEventListener('change', function () {
    picker.querySelectorAll('.ds-check').forEach(cb => cb.checked = this.checked);
    buildForms();
});
document.getElementById('rebuildBtn').addEventListener('click', buildForms);
if (periodSelect) periodSelect.addEventListener('change', renderPicker);
renderPicker();

document.getElementById('batchForm').addEventListener('submit', function (e) {
    if (!periodInput.value) {
        e.preventDefault();
        alert('Select the reporting period first.');
        return;
    }
    if (!entryForms.querySelector('input[type="hidden"]')) {
        e.preventDefault();
        alert('Select at least one dataset and fill in its records.');
    }
});
</script>
@endpush
