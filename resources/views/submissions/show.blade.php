@extends('layouts.app')
@section('title', 'Submission '.$submission->reference)
@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $submission->reference }} — {{ $submission->dataset->name }}</span>
                <span class="badge text-bg-{{ $submission->statusBadge() }} fs-6">{{ str_replace('_',' ',ucfirst($submission->status)) }}</span>
            </div>
            <div class="card-body">
                <div class="row small">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5">Institution</dt><dd class="col-7">{{ $submission->institution->name }}</dd>
                            <dt class="col-5">Dataset</dt><dd class="col-7">{{ $submission->dataset->code }} — {{ $submission->dataset->name }}</dd>
                            <dt class="col-5">Period</dt><dd class="col-7">{{ $submission->reporting_period }}</dd>
                            <dt class="col-5">Channel</dt><dd class="col-7 text-uppercase">{{ $submission->channel }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5">Submitted by</dt><dd class="col-7">{{ $submission->submitter?->name ?? '—' }}</dd>
                            <dt class="col-5">Submitted at</dt><dd class="col-7">{{ $submission->submitted_at?->format('d M Y H:i') ?? '—' }}</dd>
                            <dt class="col-5">Reviewed by</dt><dd class="col-7">{{ $submission->reviewer?->name ?? '—' }}</dd>
                            <dt class="col-5">Txn reference</dt><dd class="col-7">{{ $submission->transaction_reference ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
                @if($submission->review_comments)
                    <div class="alert alert-warning mt-3 mb-0"><strong>Review comments:</strong> {{ $submission->review_comments }}</div>
                @endif
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                @if($submission->isEditable())
                    @can('submissions.edit-own')
                    <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit records</a>
                    <form method="POST" action="{{ route('submissions.submit', $submission) }}" onsubmit="return confirm('Submit for Ministry review?')">@csrf
                        <button class="btn btn-sm btn-success"><i class="bi bi-send"></i> Submit for review</button>
                    </form>
                    @endcan
                    @if($submission->status === 'draft')
                    @can('submissions.delete-own')
                    <form method="POST" action="{{ route('submissions.destroy', $submission) }}" onsubmit="return confirm('Delete this draft?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete draft</button>
                    </form>
                    @endcan
                    @endif
                @endif
                @can('submissions.review')
                    @if($submission->status === 'submitted')
                    <form method="POST" action="{{ route('submissions.review', $submission) }}">@csrf
                        <button class="btn btn-sm btn-warning"><i class="bi bi-eye"></i> Start review</button>
                    </form>
                    @endif
                @endcan
                @can('submissions.accept')
                    @if(in_array($submission->status, ['submitted','under_review']))
                    <form method="POST" action="{{ route('submissions.accept', $submission) }}" onsubmit="return confirm('Accept this submission?')">@csrf
                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Accept</button>
                    </form>
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#returnModal"><i class="bi bi-arrow-counterclockwise"></i> Return</button>
                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-lg"></i> Reject</button>
                    @endif
                @endcan
                @can('submissions.publish')
                    @if($submission->status === 'accepted')
                    <form method="POST" action="{{ route('submissions.publish', $submission) }}" onsubmit="return confirm('Publish to the central repository?')">@csrf
                        <button class="btn btn-sm btn-primary"><i class="bi bi-globe"></i> Publish</button>
                    </form>
                    @endif
                @endcan
            </div>
        </div>

        <div class="card">
            <div class="card-header">Records ({{ $submission->records_count }})</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>#</th>@foreach($submission->dataset->fields as $f)<th>{{ $f['label'] }}</th>@endforeach</tr></thead>
                    <tbody>
                    @foreach($submission->records as $record)
                        <tr class="{{ $record->errors ? 'row-invalid' : '' }}">
                            <td>{{ $record->row_number }}</td>
                            @foreach($submission->dataset->fields as $f)
                                <td>
                                    {{ $record->data[$f['name']] ?? '' }}
                                    @if(isset($record->errors[$f['name']]))
                                        <div class="validation-error"><i class="bi bi-exclamation-triangle"></i> {{ implode(' ', $record->errors[$f['name']]) }}</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Audit Trail</div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                @forelse($audits as $log)
                    <li class="mb-3">
                        <div class="fw-semibold small">{{ str_replace('.', ' — ', $log->action) }}</div>
                        <div class="text-muted small">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->format('d M Y H:i') }}</div>
                    </li>
                @empty
                    <li class="text-muted small">No audit events recorded yet.</li>
                @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Return modal --}}
<div class="modal fade" id="returnModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('submissions.return', $submission) }}">@csrf
    <div class="modal-header"><h5 class="modal-title">Return for correction</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Comments for the institution <span class="text-danger">*</span></label>
        <textarea name="review_comments" class="form-control" rows="4" required></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-warning">Return submission</button></div>
    </form>
</div></div></div>

{{-- Reject modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('submissions.reject', $submission) }}">@csrf
    <div class="modal-header"><h5 class="modal-title">Reject submission</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
        <textarea name="review_comments" class="form-control" rows="4" required></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-danger">Reject submission</button></div>
    </form>
</div></div></div>
@endsection
