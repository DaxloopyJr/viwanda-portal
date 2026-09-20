@extends('layouts.app')
@section('title', 'Submission Compliance Report')
@section('content')
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.consolidated') }}">Consolidated</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('reports.compliance') }}">Submission Compliance</a></li>
</ul>

<form method="GET" class="d-flex gap-2 mb-3">
    <select name="period" class="form-select" style="width:auto">
        @foreach($periods as $p)<option value="{{ $p }}" @selected($period === $p)>{{ $p }}</option>@endforeach
    </select>
    <button class="btn btn-primary">Apply</button>
</form>

<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped mb-0">
        <thead><tr><th>Institution</th><th>Dataset</th><th>Frequency</th><th>Submission</th><th>Status</th><th>Compliance</th></tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['institution']->code }}</td>
                <td>{{ $row['dataset']->code }} — {{ $row['dataset']->name }}</td>
                <td>{{ $row['dataset']->frequency }}</td>
                <td>@if($row['submission'])<a href="{{ route('submissions.show', $row['submission']) }}">{{ $row['submission']->reference }}</a>@else — @endif</td>
                <td>@if($row['submission'])<span class="badge text-bg-{{ $row['submission']->statusBadge() }}">{{ str_replace('_',' ',ucfirst($row['submission']->status)) }}</span>@else <span class="text-muted">No submission</span> @endif</td>
                <td>@if($row['compliant'])<span class="badge text-bg-success">Compliant</span>@else<span class="badge text-bg-danger">Outstanding</span>@endif</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
@endsection
