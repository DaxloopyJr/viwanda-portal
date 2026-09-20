@extends('layouts.app')
@section('title', 'Submissions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <select name="status" class="form-select form-select-sm" style="width:auto">
            <option value="">All statuses</option>
            @foreach($statuses as $st)<option value="{{ $st }}" @selected(request('status') === $st)>{{ str_replace('_',' ',ucfirst($st)) }}</option>@endforeach
        </select>
        <select name="period" class="form-select form-select-sm" style="width:auto">
            <option value="">All periods</option>
            @foreach($periods as $p)<option value="{{ $p }}" @selected(request('period') === $p)>{{ $p }}</option>@endforeach
        </select>
        <button class="btn btn-sm btn-primary">Filter</button>
    </form>
    @can('submissions.create')
    <a href="{{ route('submissions.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Submission</a>
    @endcan
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-hover mb-0">
            <thead><tr><th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th><th>Channel</th><th>Records</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
            <tbody>
            @forelse($submissions as $s)
                <tr>
                    <td class="fw-semibold">{{ $s->reference }}</td>
                    <td>{{ $s->institution->code }}</td>
                    <td>{{ $s->dataset->code }} — {{ $s->dataset->name }}</td>
                    <td>{{ $s->reporting_period }}</td>
                    <td><span class="badge text-bg-light text-uppercase">{{ $s->channel }}</span></td>
                    <td>{{ $s->records_count }}</td>
                    <td><span class="badge text-bg-{{ $s->statusBadge() }}">{{ str_replace('_',' ',ucfirst($s->status)) }}</span></td>
                    <td>{{ $s->submitted_at?->format('d M Y H:i') ?? '—' }}</td>
                    <td><a href="{{ route('submissions.show', $s) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No submissions found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($submissions->hasPages())<div class="card-footer">{{ $submissions->links() }}</div>@endif
</div>
@endsection
