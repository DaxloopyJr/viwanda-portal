@extends('layouts.app')

@section('title', $period->exists ? 'Edit Period: ' . $period->name : 'New Submission Period')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('datasets.index') }}">Data Catalogue</a></li>
    <li class="breadcrumb-item"><a href="{{ route('periods.index') }}">Submission Periods</a></li>
    <li class="breadcrumb-item active">{{ $period->exists ? 'Edit ' . $period->name : 'New period' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar3 me-2"></i>{{ $period->exists ? 'Update submission period' : 'Configure a submission period' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $period->exists ? route('periods.update', $period) : route('periods.store') }}">
                    @csrf
                    @if($period->exists) @method('PUT') @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Scope</label>
                            @if($lockedInstitution)
                                <input type="hidden" name="institution_id" value="{{ auth()->user()->institution_id }}">
                                <input type="text" class="form-control" value="{{ $institutions->first()->name ?? auth()->user()->institution->name }}" disabled>
                                <div class="form-text">Periods you configure apply to your institution only.</div>
                            @else
                                <select name="institution_id" class="form-select">
                                    <option value="">— Ministry-wide (ministry departments) —</option>
                                    @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected((string) old('institution_id', $period->institution_id) === (string) $institution->id)>{{ $institution->code }} — {{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" id="frequency" class="form-select" required>
                                @foreach(\App\Models\SubmissionPeriod::FREQUENCIES as $freq)
                                <option value="{{ $freq }}" @selected(old('frequency', $period->frequency ?: 'quarterly') === $freq)>{{ ucfirst($freq) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', $period->year ?: now()->format('Y')) }}" min="2000" max="2100" required>
                        </div>
                        <div class="col-md-4" id="quarterWrap">
                            <label class="form-label">Quarter <span class="text-danger">*</span></label>
                            <select name="quarter" class="form-select">
                                @foreach([1, 2, 3, 4] as $q)
                                <option value="{{ $q }}" @selected((string) old('quarter', $period->quarter) === (string) $q)>Q{{ $q }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-none" id="monthWrap">
                            <label class="form-label">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" @selected((string) old('month', $period->month) === (string) $m)>{{ \Carbon\Carbon::create(null, $m)->format('M') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Opens</label>
                            <input type="date" name="opens_at" class="form-control" value="{{ old('opens_at', $period->opens_at?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Closes</label>
                            <input type="date" name="closes_at" class="form-control" value="{{ old('closes_at', $period->closes_at?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info small mb-0"><i class="bi bi-tag me-1"></i>Period label is generated automatically: <strong id="namePreview"></strong></div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $period->exists ? $period->is_active : true))>
                                <label class="form-check-label" for="isActive">Active — officers can submit against this period</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $period->exists ? 'Update Period' : 'Create Period' }}</button>
                        <a href="{{ route('periods.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const freq = document.getElementById('frequency');
function syncForm() {
    const f = freq.value;
    document.getElementById('quarterWrap').classList.toggle('d-none', f !== 'quarterly');
    document.getElementById('monthWrap').classList.toggle('d-none', f !== 'monthly');
    const year = document.querySelector('[name=year]').value || new Date().getFullYear();
    const quarter = document.querySelector('[name=quarter]').value;
    const month = document.querySelector('[name=month]').value;
    let label = year + ' - Annually';
    if (f === 'quarterly') label = year + ' - Q' + quarter;
    else if (f === 'monthly') label = year + ' - M' + String(month).padStart(2, '0');
    else if (f === 'weekly') label = year + ' - Weekly';
    document.getElementById('namePreview').textContent = label;
}
freq.addEventListener('change', syncForm);
document.querySelector('[name=year]').addEventListener('input', syncForm);
document.querySelector('[name=quarter]').addEventListener('change', syncForm);
document.querySelector('[name=month]').addEventListener('change', syncForm);
syncForm();
</script>
@endpush
