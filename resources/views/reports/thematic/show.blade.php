@extends('layouts.app')

@section('title', $def['key'].' — '.$def['title'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.thematic') }}">Management Reports</a></li>
    <li class="breadcrumb-item active">{{ $def['key'] }}</li>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
window.vpChart = function (id, config) {
    const el = document.getElementById(id);
    if (el && window.Chart) { new Chart(el.getContext('2d'), config); }
};
</script>
@endpush

@section('content')
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="{{ route('reports.thematic') }}"><i class="bi bi-journal-richtext me-1"></i>Management Reports</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.consolidated') }}"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.compliance') }}"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
</ul>

{{-- Report header --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="badge text-bg-navy px-3 py-2 fs-6">{{ $def['key'] }}</span>
            <span class="badge {{ $def['priority'] === 'High' ? 'text-bg-danger' : 'text-bg-secondary' }}">{{ $def['priority'] }} priority</span>
            <span class="badge text-bg-light border"><i class="bi bi-people me-1"></i>{{ $def['audience'] }}</span>
            <span class="badge text-bg-light border"><i class="bi bi-calendar3 me-1"></i>{{ $def['frequency'] }}</span>
            <span class="badge text-bg-light border"><i class="bi bi-layers me-1"></i>Tier {{ $def['tier'] }} — {{ $def['tier'] === 3 ? 'published data only' : 'Ministry-approved data' }}</span>
        </div>
        <h4 class="fw-bold mb-2">{{ $def['title'] }}</h4>
        <p class="text-muted mb-2">{{ $def['summary'] }}</p>
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:#f1f5fb;border-left:3px solid #1d2f6f">
                    <div class="small fw-bold text-uppercase text-muted mb-1"><i class="bi bi-database me-1"></i>Source data parameters</div>
                    <div class="small">{{ $def['parameters'] }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:#f2faf4;border-left:3px solid #16a34a">
                    <div class="small fw-bold text-uppercase text-muted mb-1"><i class="bi bi-signpost-2 me-1"></i>Decisions it supports</div>
                    <div class="small">{{ $def['decisions'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Reporting period</label>
                <select name="period" class="form-select form-select-sm">
                    <option value="all" @selected($filters['period'] === 'all')>All periods</option>
                    @foreach($periods['years'] as $year)
                        <option value="{{ $year }}" @selected($filters['period'] === $year)>Year {{ $year }} (annual roll-up)</option>
                    @endforeach
                    @foreach($periods['periods'] as $p)
                        <option value="{{ $p }}" @selected($filters['period'] === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            @unless(auth()->user()->isInstitutionUser() || auth()->user()->isMinistryDepartmentUser())
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Institution</label>
                <select name="institution" class="form-select form-select-sm">
                    <option value="all" @selected($filters['institution'] === 'all')>All institutions</option>
                    <option value="MIT" @selected($filters['institution'] === 'MIT')>Ministry departments</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->code }}" @selected($filters['institution'] === $inst->code)>{{ $inst->code }} — {{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>
            @endunless
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
                <a href="{{ route('reports.thematic.show', strtolower($def['key'])) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
            @can('reports.export')
            <div class="col-auto ms-auto">
                <a href="{{ route('reports.thematic.export', array_merge([strtolower($def['key'])], array_filter(request()->only('period', 'institution')))) }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
            </div>
            @endcan
        </form>
    </div>
</div>

@php $chartIdx = 0; @endphp

@foreach($sections as $section)

    @if($section['type'] === 'kpis')
        {{-- KPI stat cards + full indicator table --}}
        <div class="row g-3 mb-3">
            @foreach(collect($section['items'])->take(6) as $item)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100 text-center kpi-card">
                    <div class="card-body py-3 px-2">
                        <div class="fs-4 fw-bold text-primary">{{ $item['display'] }}</div>
                        <div class="small text-muted">{{ $item['label'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-list-ol me-2"></i>{{ $section['title'] }}</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Indicator</th><th class="text-end">Value</th><th class="text-muted">Basis</th></tr></thead>
                    <tbody>
                        @foreach($section['items'] as $item)
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td class="text-end fw-bold">{{ $item['display'] }}</td>
                            <td class="text-muted small">{{ $item['basis'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($section['type'] === 'breakdown')
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-pie-chart me-2"></i>{{ $section['title'] }}</div>
            <div class="card-body">
                @if(empty($section['rows']))
                    <div class="text-muted small">No approved data for this breakdown in the selected scope.</div>
                @else
                <div class="row align-items-center g-3">
                    <div class="col-md-7">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light"><tr><th>Category</th><th class="text-end">Records</th><th class="text-end">Share</th></tr></thead>
                            <tbody>
                                @foreach($section['rows'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="text-end">{{ number_format($row['count']) }}</td>
                                    <td class="text-end text-muted">{{ $section['total'] > 0 ? number_format($row['count'] / $section['total'] * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-5"><canvas id="chart{{ $chartIdx }}" height="220"></canvas></div>
                </div>
                @push('scripts')
                <script>
                vpChart('chart{{ $chartIdx }}', {
                    type: 'doughnut',
                    data: {
                        labels: @json(collect($section['rows'])->pluck('label')),
                        datasets: [{ data: @json(collect($section['rows'])->pluck('count')),
                            backgroundColor: ['#1d2f6f','#2f5fb3','#16a34a','#ffc928','#e05c2a','#7c3aed','#0ea5b7','#94a3b8','#cbd5e1'] }]
                    },
                    options: { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } }, cutout: '58%' }
                });
                </script>
                @endpush
                @php $chartIdx++; @endphp
                @endif
            </div>
        </div>
    @endif

    @if($section['type'] === 'trend')
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-graph-up me-2"></i>{{ $section['title'] }}</span>
                @if($section['delta'] !== null)
                <span class="badge {{ $section['delta'] >= 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                    <i class="bi bi-arrow-{{ $section['delta'] >= 0 ? 'up' : 'down' }}-right me-1"></i>{{ $section['delta'] > 0 ? '+' : '' }}{{ $section['delta'] }}% vs previous period
                </span>
                @endif
            </div>
            <div class="card-body">
                @if(empty($section['points']))
                    <div class="text-muted small">No approved data for this trend in the selected scope.</div>
                @else
                <canvas id="chart{{ $chartIdx }}" height="80"></canvas>
                @push('scripts')
                <script>
                vpChart('chart{{ $chartIdx }}', {
                    type: '{{ ($section['measure']['type'] ?? 'count') === 'count' ? 'bar' : 'line' }}',
                    data: {
                        labels: @json(collect($section['points'])->pluck('period')),
                        datasets: [{
                            label: @json($section['title']),
                            data: @json(collect($section['points'])->pluck('value')),
                            backgroundColor: 'rgba(29,47,111,.75)', borderColor: '#1d2f6f',
                            borderWidth: 2, fill: {{ ($section['measure']['type'] ?? 'count') === 'count' ? 'true' : "'origin'" }},
                            tension: .3, pointRadius: 4
                        }]
                    },
                    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
                </script>
                @endpush
                @php $chartIdx++; @endphp
                @endif
            </div>
        </div>
    @endif

    @if($section['type'] === 'submission_compliance')
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-check2-square me-2"></i>{{ $section['title'] }}</span>
                <span class="badge text-bg-navy">Period: {{ $section['period_used'] }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Institution / owner</th>
                            <th class="text-end">Expected</th><th class="text-end">Received</th>
                            <th class="text-end">Coverage</th><th class="text-end">Approved</th>
                            <th class="text-end">In pipeline</th><th class="text-end">Returned/Rejected</th>
                            <th class="text-end">First-pass rate</th><th class="text-end">Returns</th><th class="text-end">On-time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($section['rows'] as $row)
                        <tr>
                            <td><code>{{ $row['owner'] }}</code> <span class="text-muted small">{{ \Illuminate\Support\Str::limit($row['name'], 38) }}</span></td>
                            <td class="text-end">{{ $row['expected'] }}</td>
                            <td class="text-end">{{ $row['received'] }}</td>
                            <td class="text-end">
                                @if($row['coverage'] !== null)
                                    <span class="badge {{ $row['coverage'] >= 80 ? 'text-bg-success' : ($row['coverage'] >= 50 ? 'text-bg-warning' : 'text-bg-danger') }}">{{ $row['coverage'] }}%</span>
                                @else — @endif
                            </td>
                            <td class="text-end"><span class="badge text-bg-success">{{ $row['approved'] }}</span></td>
                            <td class="text-end"><span class="badge text-bg-warning">{{ $row['pipeline'] }}</span></td>
                            <td class="text-end"><span class="badge text-bg-danger">{{ $row['returned'] }}</span></td>
                            <td class="text-end fw-semibold">{{ $row['first_pass'] !== null ? $row['first_pass'].'%' : '—' }}</td>
                            <td class="text-end">{{ $row['returns'] }}</td>
                            <td class="text-end">{{ $row['timeliness'] !== null ? $row['timeliness'].'%' : '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-muted">No reporting activity in the selected scope.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                First-pass rate = submissions never returned for rectification ÷ received (from the audit trail).
                On-time = submitted before the configured period deadline, where one is configured.
            </div>
        </div>
    @endif

@endforeach

@endsection
