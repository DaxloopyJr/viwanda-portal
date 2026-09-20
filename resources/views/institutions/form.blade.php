@extends('layouts.app')
@section('title', $institution->exists ? 'Edit Institution' : 'New Institution')
@section('content')
<form method="POST" action="{{ $institution->exists ? route('institutions.update', $institution) : route('institutions.store') }}">
    @csrf
    @if($institution->exists) @method('PUT') @endif
    <div class="card"><div class="card-body row g-3">
        <div class="col-md-3"><label class="form-label">Code *</label><input name="code" class="form-control" value="{{ old('code', $institution->code) }}" required></div>
        <div class="col-md-9"><label class="form-label">Name *</label><input name="name" class="form-control" value="{{ old('name', $institution->name) }}" required></div>
        <div class="col-md-4"><label class="form-label">Contact email</label><input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $institution->contact_email) }}"></div>
        <div class="col-md-4"><label class="form-label">Contact phone</label><input name="contact_phone" class="form-control" value="{{ old('contact_phone', $institution->contact_phone) }}"></div>
        <div class="col-md-4"><label class="form-label">Integration mode *</label>
            <select name="integration_mode" class="form-select">
                <option value="manual" @selected(old('integration_mode', $institution->integration_mode) === 'manual')>Manual (portal forms / file upload)</option>
                <option value="api" @selected(old('integration_mode', $institution->integration_mode) === 'api')>API integration</option>
            </select>
        </div>
        <div class="col-12 form-check ms-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', $institution->is_active ?? true))>
            <label class="form-check-label" for="active">Active</label>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        @if($institution->exists)
        <button form="deleteInst" class="btn btn-outline-danger" onclick="return confirm('Delete this institution?')"><i class="bi bi-trash"></i> Delete</button>
        @else <span></span> @endif
        <button class="btn btn-success"><i class="bi bi-save"></i> Save institution</button>
    </div>
    </div>
</form>
@if($institution->exists)
<form id="deleteInst" method="POST" action="{{ route('institutions.destroy', $institution) }}" class="d-none">@csrf @method('DELETE')</form>
@endif
@endsection
