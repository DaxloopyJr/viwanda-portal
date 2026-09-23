@extends('layouts.app')

@section('title', $dataset->exists ? 'Edit Dataset: ' . $dataset->code : 'New Dataset')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('datasets.index') }}">Data Catalogue</a></li>
    <li class="breadcrumb-item active">{{ $dataset->exists ? 'Edit ' . $dataset->code : 'New dataset' }}</li>
@endsection

@section('content')
@php
$defaultFields = $dataset->exists
    ? json_encode($dataset->fields, JSON_PRETTY_PRINT)
    : json_encode([
        ['name' => 'item_name', 'label' => 'Item name', 'type' => 'string', 'required' => true],
        ['name' => 'quantity', 'label' => 'Quantity', 'type' => 'number', 'required' => true],
        ['name' => 'unit', 'label' => 'Unit', 'type' => 'string', 'required' => false],
        ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
    ], JSON_PRETTY_PRINT);
@endphp
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-text me-2"></i>{{ $dataset->exists ? 'Update catalogue entry' : 'Register a new dataset in the data catalogue' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $dataset->exists ? route('datasets.update', $dataset) : route('datasets.store') }}">
                    @csrf
                    @if($dataset->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Dataset Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $dataset->code) }}" required maxlength="30" placeholder="e.g. IND-PROD-01">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Dataset Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $dataset->name) }}" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $dataset->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Institution <span class="text-danger">*</span></label>
                            @if($lockedInstitution)
                                <input type="hidden" name="institution_id" value="{{ auth()->user()->institution_id }}">
                                <input type="text" class="form-control" value="{{ $institutions->first()->name ?? auth()->user()->institution->name }}" disabled>
                                <div class="form-text">Institution administrators can only configure the catalogue of their own institution.</div>
                            @else
                                <select name="institution_id" id="institutionSelect" class="form-select">
                                    <option value="">— Ministry dataset (pick a department below) —</option>
                                    @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected((string) old('institution_id', $dataset->institution_id) === (string) $institution->id)>{{ $institution->code }} — {{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @if(! $lockedInstitution)
                        <div class="col-md-6">
                            <label class="form-label">Ministry Department (for ministry-internal datasets)</label>
                            <select name="department_id" id="departmentSelect" class="form-select">
                                <option value="">— Not a ministry department dataset —</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" @selected((string) old('department_id', $dataset->department_id) === (string) $dept->id)>{{ $dept->code }} — {{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Selecting a department makes this a ministry-internal dataset reported by that department's data officer.</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Owning Department (label)</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $dataset->department) }}" maxlength="255" placeholder="e.g. Legal Department">
                            <div class="form-text">Department within the owning institution, as in the data-requirements document.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" class="form-select" required>
                                @foreach(['Weekly', 'Monthly', 'Quarterly', 'Annually'] as $freq)
                                <option value="{{ strtolower($freq) }}" @selected(old('frequency', $dataset->frequency) === strtolower($freq))>{{ $freq }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                @foreach(['low', 'medium', 'high'] as $prio)
                                <option value="{{ $prio }}" @selected(old('priority', $dataset->priority ?: 'medium') === $prio)>{{ ucfirst($prio) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Source System</label>
                            <input type="text" name="source_system" class="form-control" value="{{ old('source_system', $dataset->source_system) }}" maxlength="255" placeholder="e.g. MIS, manual register">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Consumers</label>
                            <select name="consumers[]" class="form-select" multiple size="3">
                                @php $selectedConsumers = old('consumers', array_filter(array_map('trim', explode(',', $dataset->consumers ?? '')))); @endphp
                                @foreach($consumerOptions as $c)
                                <option value="{{ $c->code }}" @selected(in_array($c->code, $selectedConsumers))>{{ $c->code }}{{ $c->name ? ' — '.$c->name : '' }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd for multiple. Manage the list under Data Catalogue → Data Consumers.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Field Definitions (JSON) <span class="text-danger">*</span></label>
                            <textarea name="fields" class="form-control font-monospace" rows="10" required>{{ old('fields', $defaultFields) }}</textarea>
                            <div class="form-text">Array of fields. Each field: <code>name</code>, <code>label</code>, <code>type</code> (string, number, date, text, options), optional <code>required</code> and <code>options</code> (for type <code>options</code>).</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $dataset->exists ? $dataset->is_active : true))>
                                <label class="form-check-label" for="isActive">Active — institution officers can prepare submissions against this dataset</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $dataset->exists ? 'Update Dataset' : 'Register Dataset' }}</button>
                        <a href="{{ route('datasets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>About the Data Catalogue</div>
            <div class="card-body small text-muted">
                <p>The data catalogue defines what each institution must report. Institution officers use the pre-defined field definitions to prepare data for each reporting period.</p>
                <p class="mb-0">Field types: <code>string</code> (short text), <code>text</code> (long text), <code>number</code>, <code>date</code>, and <code>options</code> (a dropdown — provide an <code>options</code> array).</p>
            </div>
        </div>
    </div>
</div>
@endsection
