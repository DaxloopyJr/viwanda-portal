@extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')
<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped mb-0">
        <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Object</th><th>IP</th></tr></thead>
        <tbody>
        @foreach($logs as $log)
            <tr>
                <td class="text-nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                <td>{{ $log->user?->name ?? 'System' }}</td>
                <td><code>{{ $log->action }}</code></td>
                <td class="small">{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}</td>
                <td class="small">{{ $log->ip_address }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($logs->hasPages())<div class="card-footer">{{ $logs->links() }}</div>@endif
</div>
@endsection
