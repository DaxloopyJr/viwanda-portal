@extends('layouts.app')

@section('title', $consumer->exists ? 'Edit Consumer: ' . $consumer->code : 'New Data Consumer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('datasets.index') }}">Data Catalogue</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consumers.index') }}">Data Consumers</a></li>
    <li class="breadcrumb-item active">{{ $consumer->exists ? 'Edit ' . $consumer->code : 'New consumer' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>{{ $consumer->exists ? 'Update data consumer' : 'Register a data consumer' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $consumer->exists ? route('consumers.update', $consumer) : route('consumers.store') }}">
                    @csrf
                    @if($consumer->exists) @method('PUT') @endif
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $consumer->code) }}" required maxlength="60" placeholder="e.g. TR, MIT, Public">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Description</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $consumer->name) }}" maxlength="255" placeholder="e.g. Ministry of Industry and Trade">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Scope</label>
                            @if($lockedInstitution)
                                <input type="hidden" name="institution_id" value="{{ auth()->user()->institution_id }}">
                                <input type="text" class="form-control" value="{{ $institutions->first()->name ?? auth()->user()->institution->name }}" disabled>
                                <div class="form-text">Consumers you add are visible to your institution's officers (in addition to the global list).</div>
                            @else
                                <select name="institution_id" class="form-select">
                                    <option value="">— Global (all institutions) —</option>
                                    @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected((string) old('institution_id', $consumer->institution_id) === (string) $institution->id)>{{ $institution->code }} — {{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $consumer->exists ? $consumer->is_active : true))>
                                <label class="form-check-label" for="isActive">Active — selectable at submission time</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $consumer->exists ? 'Update Consumer' : 'Add Consumer' }}</button>
                        <a href="{{ route('consumers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
