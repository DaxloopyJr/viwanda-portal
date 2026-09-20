<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Viwanda Portal') — Ministry of Industry and Trade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --vp-primary: #1a3c6e; --vp-accent: #1a9e5c; }
        body { background: #f4f6f9; font-family: "Segoe UI", Arial, sans-serif; }
        .sidebar { width: 250px; min-height: 100vh; background: var(--vp-primary); }
        .sidebar .nav-link { color: #c9d6ea; padding: .55rem 1rem; border-radius: .375rem; margin: .1rem .5rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.12); color: #fff; }
        .sidebar .brand { color: #fff; font-weight: 700; }
        .topbar { background: #fff; border-bottom: 1px solid #e3e8ef; }
        .stat-card { border: none; border-left: 4px solid var(--vp-primary); }
        .stat-card.green { border-left-color: var(--vp-accent); }
        .stat-card.orange { border-left-color: #e6a23c; }
        .stat-card.red { border-left-color: #d9534f; }
        .text-bg-orange { background-color: #e6a23c !important; color: #fff !important; }
        .card { border: none; box-shadow: 0 1px 3px rgba(16,42,67,.08); }
        .card-header { background: #fff; font-weight: 600; }
        .table { --bs-table-striped-bg: #f8fafc; }
        .validation-error { color: #d9534f; font-size: .8rem; }
        tr.row-invalid { background: #fdf2f2; }
        main { min-width: 0; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    @auth
    <nav class="sidebar d-flex flex-column p-2">
        <div class="brand px-3 py-3 border-bottom border-secondary">
            <i class="bi bi-building"></i> VIWANDA PORTAL
            <div class="small fw-normal text-white-50">Ministry of Industry and Trade</div>
        </div>
        <div class="nav flex-column mt-2">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            @can('submissions.view')
            <a class="nav-link {{ request()->routeIs('submissions.*') ? 'active' : '' }}" href="{{ route('submissions.index') }}"><i class="bi bi-inbox me-2"></i>Submissions</a>
            @endcan
            <a class="nav-link {{ request()->routeIs('datasets.*') ? 'active' : '' }}" href="{{ route('datasets.index') }}"><i class="bi bi-journal-text me-2"></i>Data Catalogue</a>
            @can('reports.view')
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.consolidated') }}"><i class="bi bi-bar-chart me-2"></i>Reports</a>
            @endcan
            @can('institutions.manage')
            <a class="nav-link {{ request()->routeIs('institutions.*') ? 'active' : '' }}" href="{{ route('institutions.index') }}"><i class="bi bi-bank me-2"></i>Institutions</a>
            @endcan
            @can('users.manage')
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i>Users &amp; Roles</a>
            @endcan
            @can('audit.view')
            <a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}"><i class="bi bi-shield-check me-2"></i>Audit Trail</a>
            @endcan
        </div>
        <div class="mt-auto p-2 text-white-50 small border-top border-secondary">
            <div class="text-white">{{ auth()->user()->name }}</div>
            <div>{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'No role' }}</div>
            @if(auth()->user()->institution)<div>{{ auth()->user()->institution->name }}</div>@endif
        </div>
    </nav>
    @endauth

    <div class="flex-grow-1 d-flex flex-column">
        @auth
        <header class="topbar d-flex justify-content-between align-items-center px-4 py-2">
            <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('profile.show') }}" class="text-decoration-none"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </form>
            </div>
        </header>
        @endauth

        <main class="p-4 flex-grow-1">
            @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
        <footer class="text-center text-muted small py-3">Viwanda Portal &mdash; Ministry of Industry and Trade &copy; {{ date('Y') }}</footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
