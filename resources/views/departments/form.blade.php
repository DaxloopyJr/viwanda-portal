@extends('layouts.app')

@section('title', $department->exists ? 'Edit Department: ' . $department->code : 'New Ministry Department')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Ministry Departments</a></li>
    <li class="breadcrumb-item active">{{ $department->exists ? 'Edit ' . $department->code : 'New department' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>{{ $department->exists ? 'Update ministry department' : 'Register a ministry department' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $department->exists ? route('departments.update', $department) : route('departments.store') }}">
                    @csrf
                    @if($department->exists) @method('PUT') @endif
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $department->code) }}" required maxlength="30" placeholder="e.g. IND">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required maxlength="255" placeholder="e.g. Industry Department">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $department->description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $department->exists ? $department->is_active : true))>
                                <label class="form-check-label" for="isActive">Active — the department can be assigned users and datasets</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $department->exists ? 'Update Department' : 'Create Department' }}</button>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>How ministry departments work</div>
            <div class="card-body small text-muted">
                <p>After registering a department:</p>
                <ol class="mb-0">
                    <li>Assign a user the <strong>Ministry Department Data Officer</strong> role and select this department (Users).</li>
                    <li>Register datasets owned by this department (Data Catalogue → New Dataset → pick a department instead of an institution).</li>
                    <li>The officer submits data like an institution officer; the ministry reviewer and final approver handle it in the normal approval circle.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
