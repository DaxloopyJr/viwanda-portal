@extends('layouts.app')
@section('title', 'Portal Settings')
@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Branding &amp; Identity</div>
            <div class="card-body">
                <p class="text-muted small">These settings control the portal name and logo shown on the landing page, the floating header, and the navigation bar after login.</p>
                <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Portal name *</label>
                        <input name="site_name" class="form-control" value="{{ old('site_name', $siteName) }}" required maxlength="120">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input name="site_tagline" class="form-control" value="{{ old('site_tagline', $tagline) }}" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        @if($logoUrl)
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 border rounded">
                                <img src="{{ $logoUrl }}" alt="logo" style="height:56px;object-fit:contain">
                                <div class="form-check">
                                    <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="removeLogo">
                                    <label class="form-check-label" for="removeLogo">Remove current logo</label>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                        <div class="form-text">PNG, JPG, SVG or WebP, max 2 MB. Leave empty to keep the current logo.</div>
                    </div>
                    <button class="btn btn-success"><i class="bi bi-save"></i> Save settings</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Preview</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 p-2 rounded-pill shadow-sm border">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" style="height:34px;width:34px;object-fit:contain" alt="">
                    @else
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="height:34px;width:34px;background:linear-gradient(135deg,#0e8a9c,#1a73e8)"><i class="bi bi-building"></i></span>
                    @endif
                    <span class="fw-bold" style="color:#0b7285">{{ $siteName }}</span>
                </div>
                <p class="text-muted small mt-3 mb-0">Header preview as it appears on the landing page.</p>
            </div>
        </div>
    </div>
</div>
@endsection
