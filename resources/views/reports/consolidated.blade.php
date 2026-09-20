@extends('layouts.app')
@section('title', 'Consolidated Report')
@section('content')
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link active" href="{{ route('reports.consolidated') }}">Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.compliance') }}">Submission Compliance</a></li>
    @can('reports.export')<li class="nav-item ms-auto"><a class="btn btn-sm btn-outline-success" href="{{ route('reports.export') }}"><i class="bi bi-download"></i> Export CSV</a></li>@endcan
</ul>

<div class="row g-3">
    <div class="col-lg-7"><div class="card h-100">
        <div class="card-header">Submissions by Institution</div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped mb-0">
                <thead><tr><th>Institution</th><th>Total</th><th>Published</th><th>Accepted</th><th>Pending</th><th>Returned/Rejected</th></tr></thead>
                <tbody>
                @foreach($byInstitution as $row)
                    <tr>
                        <td>{{ $row->code }} — {{ $row->name }}</td>
                        <td>{{ $row->total }}</td>
                        <td><span class="badge text-bg-primary">{{ $row->published }}</span></td>
                        <td><span class="badge text-bg-success">{{ $row->accepted }}</span></td>
                        <td><span class="badge text-bg-warning">{{ $row->pending }}</span></td>
                        <td><span class="badge text-bg-danger">{{ $row->rejected_returned }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div></div>
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">Status mix</div>
            <div class="card-body"><canvas id="statusDonut"></canvas></div>
        </div>
        <div class="card">
            <div class="card-header">By reporting period</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Period</th><th>Submissions</th></tr></thead>
                    <tbody>@foreach($byPeriod as $row)<tr><td>{{ $row->reporting_period }}</td><td>{{ $row->total }}</td></tr>@endforeach</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const rows = @json($byInstitution);
new Chart(document.getElementById('statusDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Published', 'Accepted', 'Pending', 'Returned/Rejected'],
        datasets: [{
            data: [
                rows.reduce((a, r) => a + Number(r.published), 0),
                rows.reduce((a, r) => a + Number(r.accepted), 0),
                rows.reduce((a, r) => a + Number(r.pending), 0),
                rows.reduce((a, r) => a + Number(r.rejected_returned), 0),
            ],
            backgroundColor: ['#1a3c6e', '#198754', '#ffc107', '#dc3545'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
