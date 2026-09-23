@extends('layouts.app')

@section('title', 'Management Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active">Management Reports (R1–R14, N1–N10)</li>
@endsection

@section('content')
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="{{ route('reports.thematic') }}"><i class="bi bi-journal-richtext me-1"></i>Management Reports</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.consolidated') }}"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('reports.compliance') }}"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
</ul>

<div class="card mb-4 border-0 shadow-sm" style="background:linear-gradient(135deg,#1d2f6f,#16244f);color:#eef1fb">
    <div class="card-body py-4">
        <h5 class="fw-bold mb-2"><i class="bi bi-clipboard2-data me-2"></i>Proposed Management and Public Reports for National Growth Decision-Making</h5>
        <p class="mb-1 opacity-75">
            These reports are generated from the datasets that Ministry departments, units and subordinate institutions
            submit regularly — the FCT and TBS data parameters, and the MIT &amp; Institutions data-requirements
            catalogue for the data warehouse. Processed data tells three stories:
            <strong>how efficiently institutions deliver their mandates</strong>, <strong>how fairly and safely markets operate</strong>,
            and <strong>how competitively Tanzanian products perform domestically and internationally</strong>.
        </p>
        <p class="mb-0 small opacity-75">
            <i class="bi bi-shield-check me-1"></i>Tier 1–2 and Tier 4 reports are computed from Ministry-approved data
            (accepted &amp; published); Tier 3 public reports use <strong>published</strong> data only.
        </p>
    </div>
</div>

@foreach($tiers as $tierNo => $tier)
<div class="d-flex align-items-center gap-2 mb-2 mt-4">
    <span class="badge text-bg-{{ $tier['color'] === 'navy' ? 'navy' : ($tier['color'] === 'teal' ? 'info' : ($tier['color'] === 'gold' ? 'warning' : 'success')) }} px-3 py-2">
        <i class="bi {{ $tier['icon'] }} me-1"></i>{{ $tier['label'] }}
    </span>
</div>
<p class="text-muted small mb-3">{{ $tier['note'] }}</p>

<div class="row g-3 mb-2">
    @foreach($reports as $key => $report)
        @if($report['tier'] !== $tierNo) @continue @endif
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm report-card">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge rounded-pill text-bg-light border fw-bold">{{ $report['key'] }}</span>
                        <span class="badge {{ $report['priority'] === 'High' ? 'text-bg-danger' : 'text-bg-secondary' }}">{{ $report['priority'] }} priority</span>
                    </div>
                    <h6 class="fw-bold">{{ $report['title'] }}</h6>
                    <p class="text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($report['summary'], 150) }}</p>
                    <div class="small mb-3">
                        <div><i class="bi bi-people me-1 text-muted"></i>{{ $report['audience'] }}</div>
                        <div><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $report['frequency'] }}</div>
                    </div>
                    <a href="{{ route('reports.thematic.show', $key) }}" class="btn btn-sm btn-outline-primary mt-auto">
                        <i class="bi bi-bar-chart-line me-1"></i>Open report
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endforeach

<style>
.report-card { transition: transform .2s ease, box-shadow .2s ease; }
.report-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(20,30,60,.14) !important; }
</style>
@endsection
