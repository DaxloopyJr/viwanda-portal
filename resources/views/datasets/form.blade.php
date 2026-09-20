@extends('layouts.app')
@section('title', $dataset->exists ? 'Edit Dataset' : 'New Dataset')
@section('content')
<form method="POST" action="{{ $dataset->exists ? route('datasets.update', $dataset) : route('datasets.store') }}">
    @csrf
    @if($dataset->exists) @method('PUT') @endif
    <div class="card"><div class="card-body row g-3">
        <div class="col-md-3"><label class="form-label">Code *</label><input name="code" class="form-control" value="{{ old('code', $dataset->code) }}" required></div>
        <div class="col-md-5"><label class="form-label">Name *</label><input name="name" class="form-control" value="{{ old('name', $dataset->name) }}" required></div>
        <div class="col-md-4"><label class="form-label">Institution *</label>
            <select name="institution_id" class="form-select" required>
                @foreach($institutions as $inst)<option value="{{ $inst->id }}" @selected(old('institution_id', $dataset->institution_id) == $inst->id)>{{ $inst->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ old('description', $dataset->description) }}</textarea></div>
        <div class="col-md-3"><label class="form-label">Frequency *</label>
            <select name="frequency" class="form-select">@foreach(['Weekly','Monthly','Quarterly','Annually'] as $f)<option @selected(old('frequency', $dataset->frequency) === $f)>{{ $f }}</option>@endforeach</select>
        </div>
        <div class="col-md-3"><label class="form-label">Priority *</label>
            <select name="priority" class="form-select">@foreach(['low','medium','high'] as $p)<option value="{{ $p }}" @selected(old('priority', $dataset->priority) === $p)>{{ ucfirst($p) }}</option>@endforeach</select>
        </div>
        <div class="col-md-3"><label class="form-label">Source system</label><input name="source_system" class="form-control" value="{{ old('source_system', $dataset->source_system) }}"></div>
        <div class="col-md-3"><label class="form-label">Consumers</label><input name="consumers" class="form-control" value="{{ old('consumers', $dataset->consumers) }}"></div>
        <div class="col-12">
            <label class="form-label">Data dictionary fields (JSON) *</label>
            <textarea name="fields" class="form-control font-monospace" rows="8" required>{{ old('fields', json_encode($dataset->fields ?? [['name'=>'','label'=>'','type'=>'string','required'=>true]], JSON_PRETTY_PRINT)) }}</textarea>
            <div class="form-text">Array of fields: name, label, type (string/date/integer/number/text), required (bool), options (array, optional).</div>
        </div>
        <div class="col-12 form-check ms-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', $dataset->is_active ?? true))>
            <label class="form-check-label" for="active">Active</label>
        </div>
    </div>
    <div class="card-footer text-end"><button class="btn btn-success"><i class="bi bi-save"></i> Save dataset</button></div>
    </div>
</form>
@endsection
