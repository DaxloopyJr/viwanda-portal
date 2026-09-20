@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted small">Active Institutions</div>
            <div class="display-6 fw-bold">{{ $stats['institutions'] }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green"><div class="card-body">
            <div class="text-muted small">Total Submissions</div>
            <div class="display-6 fw-bold">{{ $stats['submissions'] }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card orange"><div class="card-body">
            <div class="text-muted small">Pending Review</div>
            <div class="display-6 fw-bold">{{ $stats['pending_review'] }}</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card red"><div class="card-body">
            <div class="text-muted small">Acceptance Rate</div>
            <div class="display-6 fw-bold">{{ $stats['acceptance_rate'] !== null ? $stats['acceptance_rate'].'%' : '—' }}</div>
        </div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">Submissions Trend (last 12 months)</div>
            <div class="card-body"><canvas id="trendChart" height="110"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Submission Status Distribution</div>
            <div class="card-body"><canvas id="statusPie"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">Submissions by Institution</div>
            <div class="card-body"><canvas id="instBar" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Submissions</span>
                @can('submissions.view')<a href="{{ route('submissions.index') }}" class="btn btn-sm btn-outline-primary">View all</a>@endcan
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead><tr><th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th><th>Channel</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recent as $s)
                        <tr>
                            <td><a href="{{ route('submissions.show', $s) }}">{{ $s->reference }}</a></td>
                            <td>{{ $s->institution->code }}</td>
                            <td>{{ $s->dataset->code }}</td>
                            <td>{{ $s->reporting_period }}</td>
                            <td><span class="badge text-bg-light text-uppercase">{{ $s->channel }}</span></td>
                            <td><span class="badge text-bg-{{ $s->statusBadge() }}">{{ str_replace('_', ' ', ucfirst($s->status)) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No submissions yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Submission Compliance — current period ({{ $period }})</div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped mb-0">
            <thead><tr><th>Institution</th><th>Integration</th><th>Submitted this period</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($compliance as $inst)
                <tr>
                    <td>{{ $inst->code }} — {{ $inst->name }}</td>
                    <td><span class="badge text-bg-{{ $inst->integration_mode === 'api' ? 'info' : 'light' }}">{{ strtoupper($inst->integration_mode) }}</span></td>
                    <td>{{ $inst->period_submissions }}</td>
                    <td>
                        @if($inst->period_submissions > 0)
                            <span class="badge text-bg-success"><i class="bi bi-check-circle"></i> Compliant</span>
                        @else
                            <span class="badge text-bg-danger"><i class="bi bi-exclamation-circle"></i> Outstanding</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const trendCtx = document.getElementById('trendChart');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($monthlyLabels),
        datasets: [{
            label: 'Submissions',
            data: @json($monthlyData),
            borderColor: '#1a3c6e', backgroundColor: 'rgba(26,60,110,.15)',
            fill: true, tension: .3, pointRadius: 4,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

const statusColors = { draft:'#6c757d', submitted:'#0dcaf0', under_review:'#ffc107', returned:'#e6a23c', accepted:'#198754', rejected:'#dc3545', published:'#1a3c6e' };
const statusOrder = @json($statusOrder);
const statusCounts = @json($statusCounts);
new Chart(document.getElementById('statusPie'), {
    type: 'pie',
    data: {
        labels: statusOrder.filter(s => statusCounts[s]).map(s => s.replace('_',' ').replace(/\b\w/g, c => c.toUpperCase())),
        datasets: [{ data: statusOrder.filter(s => statusCounts[s]).map(s => statusCounts[s]),
            backgroundColor: statusOrder.filter(s => statusCounts[s]).map(s => statusColors[s]) }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('instBar'), {
    type: 'bar',
    data: {
        labels: @json($perInstitution->pluck('code')),
        datasets: [{ label: 'Submissions', data: @json($perInstitution->pluck('total')), backgroundColor: '#1a9e5c' }]
    },
    options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>
@endpush
