@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-3">
    @php
        $cards = [
            ['key' => 'institutions', 'label' => 'Active Institutions', 'value' => $stats['institutions'], 'color' => 'blue', 'icon' => 'bi-bank', 'sub' => null],
            ['key' => 'datasets', 'label' => 'Active Datasets', 'value' => $stats['datasets'], 'color' => 'teal', 'icon' => 'bi-journal-text', 'sub' => null],
            ['key' => 'submissions', 'label' => 'Total Submissions', 'value' => $stats['submissions'], 'color' => 'green', 'icon' => 'bi-inbox', 'sub' => null],
            ['key' => 'pending_review', 'label' => 'Pending Review', 'value' => $stats['pending_review'], 'color' => 'orange', 'icon' => 'bi-hourglass-split', 'sub' => ($stats['internal_queue'] ?? 0) > 0 ? $stats['internal_queue'].' in internal approval' : null],
            ['key' => 'internal_queue', 'label' => 'Internal Queue', 'value' => $stats['internal_queue'], 'color' => 'purple', 'icon' => 'bi-diagram-2', 'sub' => null],
            ['key' => 'published', 'label' => 'Published', 'value' => $stats['published'], 'color' => 'navy', 'icon' => 'bi-megaphone', 'sub' => null],
            ['key' => 'returned', 'label' => 'Returned / Rejected', 'value' => $stats['returned'], 'color' => 'pink', 'icon' => 'bi-arrow-counterclockwise', 'sub' => null],
            ['key' => 'acceptance_rate', 'label' => 'Acceptance Rate', 'value' => $stats['acceptance_rate'] !== null ? $stats['acceptance_rate'].'%' : '—', 'color' => 'red', 'icon' => 'bi-patch-check', 'sub' => 'decided submissions'],
        ];
    @endphp
    @foreach($cards as $card)
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card stat-card {{ $card['color'] }}" data-card="{{ $card['key'] }}" role="button" title="Click to view details">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="sa-icon"><i class="bi {{ $card['icon'] }}"></i></span>
                <div>
                    <div class="sa-num">{{ $card['value'] }}</div>
                    <div class="sa-lbl">{{ $card['label'] }}</div>
                    @if($card['sub'])<div class="small text-muted">{{ $card['sub'] }}</div>@endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card mb-3 d-none" id="cardDetailPanel">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-2"></i><span id="cardDetailTitle">Details</span></span>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="cardDetailClose"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Item</th><th>Details</th><th class="text-end">Value</th></tr></thead>
                <tbody id="cardDetailBody"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">Submissions Trend (last 12 months)</div>
            <div class="card-body"><canvas id="trendChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Submission Status Distribution</div>
            <div class="card-body"><canvas id="statusPie"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
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
            <div class="card-body">
                <div class="table-responsive">
                <table id="recentTable" class="table table-striped w-100">
                    <thead><tr><th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th><th>Channel</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($recent as $s)
                        <tr>
                            <td>@can('submissions.view')<a href="{{ route('submissions.show', $s) }}">{{ $s->reference }}</a>@else{{ $s->reference }}@endcan</td>
                            <td>{{ $s->institution?->code ?? $s->dataset->ownerLabel() }}</td>
                            <td>{{ $s->dataset->code }}</td>
                            <td>{{ $s->reporting_period }}</td>
                            <td class="text-uppercase small">{{ $s->channel }}</td>
                            <td><span class="badge text-bg-{{ $s->statusBadge() }}">{{ $s->statusLabel() }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Submission Compliance — current period ({{ $period }})</div>
    <div class="card-body">
        <div class="table-responsive">
        <table id="complianceTable" class="table table-striped w-100">
            <thead><tr><th>Institution</th><th>Integration</th><th>Submitted this period</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($compliance as $inst)
                <tr>
                    <td>{{ $inst->code }} — {{ $inst->name }}</td>
                    <td class="text-uppercase small">{{ $inst->integration_mode }}</td>
                    <td>{{ $inst->period_submissions }}</td>
                    <td>@if($inst->period_submissions > 0)<span class="badge text-bg-success">Compliant</span>@else<span class="badge text-bg-secondary">Outstanding</span>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
vpDataTable('#recentTable', null, null, { pageLength: 8, lengthMenu: [8, 15, 25] });
vpDataTable('#complianceTable', null, null, { pageLength: 10 });

const cardDetails = @json($cardDetails);
const cardTitles = {
    institutions: 'Active institutions', datasets: 'Active datasets', submissions: 'Latest submissions',
    pending_review: 'Submissions pending review', internal_queue: 'Submissions in internal approval',
    published: 'Published submissions', returned: 'Returned / rejected submissions',
    acceptance_rate: 'Decided submissions (accepted, rejected, published)'
};
const detailPanel = document.getElementById('cardDetailPanel');
const detailTitle = document.getElementById('cardDetailTitle');
const detailBody = document.getElementById('cardDetailBody');
document.querySelectorAll('.stat-card').forEach(function (card) {
    card.addEventListener('click', function () {
        const key = this.dataset.card;
        const rows = cardDetails[key] || [];
        detailTitle.textContent = cardTitles[key] || 'Details';
        detailBody.innerHTML = rows.length
            ? rows.map(function (r) {
                const label = r.url ? '<a href="' + r.url + '" class="text-decoration-none fw-semibold">' + r.label + '</a>' : '<span class="fw-semibold">' + r.label + '</span>';
                return '<tr><td>' + label + '</td><td class="text-muted small">' + (r.meta || '') + '</td><td class="text-end">' + (r.value || '') + '</td></tr>';
            }).join('')
            : '<tr><td colspan="3" class="text-center text-muted py-4">Nothing to show.</td></tr>';
        detailPanel.classList.remove('d-none');
        detailPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});
document.getElementById('cardDetailClose').addEventListener('click', function () {
    detailPanel.classList.add('d-none');
});

const statusCounts = @json($statusCounts);
const statusOrder = @json($statusOrder);
const statusColors = { draft:'#6c757d', internal_review:'#7e57c2', returned_officer:'#e6a23c', accounting_review:'#0e8a9c', returned_supervisor:'#e6a23c', submitted:'#0dcaf0', under_review:'#ffc107', returned:'#e6a23c', accepted:'#198754', rejected:'#dc3545', published:'#1a3c6e' };

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyLabels),
        datasets: [{ label: 'Submissions', data: @json($monthlyData), fill: true, tension: .3,
            borderColor: '#1a3c6e', backgroundColor: 'rgba(26,60,110,.15)' }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

new Chart(document.getElementById('statusPie'), {
    type: 'pie',
    data: {
        labels: statusOrder.filter(s => statusCounts[s]).map(s => s.replaceAll('_',' ').replace(/\b\w/g, c => c.toUpperCase())),
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
