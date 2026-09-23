<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Viwanda Portal'); ?> — <?php echo e(\App\Models\Setting::siteName()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --vp-primary: #1a3c6e; --vp-accent: #1a9e5c; --vp-teal: #0e8a9c; --vp-header-h: 78px; }
        body { background: #f4f6f9; font-family: "Segoe UI", Arial, sans-serif; }

        /* Sticky header (brand + user) */
        .app-header {
            position: sticky; top: 0; z-index: 1040; height: var(--vp-header-h);
            background: #fff; box-shadow: 0 1px 0 #e6ecf3;
            padding: 0 1.5rem;
        }
        .app-header .navbar-brand { font-weight: 700; color: var(--vp-primary); line-height: 1.1; }
        .app-header .navbar-brand img { height: 54px; width: 54px; object-fit: contain; }
        .app-header .brand-slogan { display: block; font-size: .76rem; color: #8a94a6; letter-spacing: .04em; font-weight: 500; }
        .app-header .navbar-brand .brand-name { font-size: 1.3rem; }

        /* Sticky nav menu bar (below header) */
        .app-menubar {
            position: sticky; top: var(--vp-header-h); z-index: 1035;
            background: var(--vp-primary); box-shadow: 0 4px 14px rgba(16,42,67,.25);
            padding: 0 1.5rem;
        }
        .app-menubar .nav-link { color: rgba(255,255,255,.82); font-weight: 600; padding: .7rem .95rem; border-radius: .4rem; }
        .app-menubar .nav-link:hover { color: #fff; background: rgba(255,255,255,.12); }
        .app-menubar .nav-link.active, .app-menubar .dropdown:has(.dropdown-menu .active) > .nav-link { color: #fff; background: rgba(255,255,255,.16); }
        .app-menubar .dropdown-menu { border: none; border-radius: .75rem; box-shadow: 0 12px 32px rgba(16,42,67,.22); padding: .5rem; }
        .app-menubar .dropdown-item { border-radius: .5rem; padding: .5rem .9rem; font-weight: 500; }
        .app-menubar .dropdown-item:hover { background: #f0f4fa; color: var(--vp-primary); }
        .app-menubar .dropdown-item.active { background: #e8effa; color: var(--vp-primary); }
        .app-menubar .navbar-toggler { border-color: rgba(255,255,255,.4); }
        .app-menubar .navbar-toggler-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e"); }

        .page-title-bar h1 { font-size: 1.25rem; font-weight: 700; color: #22303c; }
        .page-title-bar .breadcrumb { --bs-breadcrumb-divider: '›'; margin-bottom: .35rem; font-size: .8rem; }
        .page-title-bar .breadcrumb-item a { color: #6b7a90; text-decoration: none; }
        .page-title-bar .breadcrumb-item a:hover { color: var(--vp-primary); }
        .page-title-bar .breadcrumb-item.active { color: #22303c; font-weight: 600; }
        .stat-card { border: none; border-radius: .9rem; cursor: pointer; transition: transform .25s ease, box-shadow .25s ease; overflow: hidden; position: relative; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 14px 34px rgba(16,42,67,.16); }
        .stat-card .sa-icon {
            height: 54px; width: 54px; border-radius: 14px; flex: none;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff; transition: transform .3s ease;
        }
        .stat-card:hover .sa-icon { transform: rotate(-8deg) scale(1.12); }
        .stat-card .sa-icon i { animation: saFloat 3s ease-in-out infinite; }
        @keyframes saFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        .stat-card .sa-num { font-size: 1.75rem; font-weight: 800; color: #22303c; line-height: 1.1; }
        .stat-card .sa-lbl { color: #7a8496; font-size: .82rem; font-weight: 600; }
        .stat-card.blue { background: linear-gradient(135deg, #eef2ff, #fff); } .stat-card.blue .sa-icon { background: linear-gradient(135deg, #4a66f0, #6d8bff); }
        .stat-card.green { background: linear-gradient(135deg, #e8f9f0, #fff); } .stat-card.green .sa-icon { background: linear-gradient(135deg, #1a9e5c, #3fc47f); }
        .stat-card.orange { background: linear-gradient(135deg, #fdf3e3, #fff); } .stat-card.orange .sa-icon { background: linear-gradient(135deg, #e6a23c, #f2bc66); }
        .stat-card.red { background: linear-gradient(135deg, #fdecec, #fff); } .stat-card.red .sa-icon { background: linear-gradient(135deg, #d9534f, #ee7a76); }
        .stat-card.purple { background: linear-gradient(135deg, #f1ebfb, #fff); } .stat-card.purple .sa-icon { background: linear-gradient(135deg, #7e57c2, #9d7fd4); }
        .stat-card.teal { background: linear-gradient(135deg, #e3f6f8, #fff); } .stat-card.teal .sa-icon { background: linear-gradient(135deg, #0e8a9c, #2ab0c2); }
        .stat-card.navy { background: linear-gradient(135deg, #e8edf7, #fff); } .stat-card.navy .sa-icon { background: linear-gradient(135deg, #1a3c6e, #34598f); }
        .stat-card.pink { background: linear-gradient(135deg, #fdeef5, #fff); } .stat-card.pink .sa-icon { background: linear-gradient(135deg, #d63384, #e763a5); }
        /* page loader */
        #pageLoader {
            position: fixed; inset: 0; z-index: 2000; background: rgba(255,255,255,.92);
            display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 1rem;
        }
        #pageLoader .loader-ring {
            width: 58px; height: 58px; border-radius: 50%;
            border: 5px solid #e6ecf3; border-top-color: var(--vp-primary); border-right-color: var(--vp-teal);
            animation: vpSpin 0.9s linear infinite;
        }
        #pageLoader .loader-text { color: #45566b; font-weight: 600; font-size: .85rem; letter-spacing: .08em; }
        @keyframes vpSpin { to { transform: rotate(360deg); } }
        .text-bg-orange { background-color: #e6a23c !important; color: #fff !important; }
        .text-bg-purple { background-color: #7e57c2 !important; color: #fff !important; }
        .text-bg-teal { background-color: #0e8a9c !important; color: #fff !important; }
        .text-bg-navy { background-color: #1a3c6e !important; color: #fff !important; }
        .btn-teal { background-color: #0e8a9c; border-color: #0e8a9c; color: #fff; }
        .btn-teal:hover { background-color: #0b7285; border-color: #0b7285; color: #fff; }
        .card { border: none; box-shadow: 0 1px 3px rgba(16,42,67,.08); }
        .card-header { background: #fff; font-weight: 600; }
        .table { --bs-table-striped-bg: #f8fafc; }
        .validation-error { color: #d9534f; font-size: .8rem; }
        tr.row-invalid { background: #fdf2f2; }
        .user-chip { border-radius: 50px; background: #f0f4fa; padding: .35rem .9rem; }

        /* DataTables theming */
        div.dt-container .dt-buttons .btn { border-radius: .4rem; }
        div.dt-container select.dt-input, div.dt-container input.dt-input { border-radius: .4rem; }
        .dt-buttons .btn-outline-secondary { color: #45566b; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div id="pageLoader">
    <div class="loader-ring"></div>
    <div class="loader-text"><?php echo e(\App\Models\Setting::siteName()); ?></div>
</div>
<?php if(auth()->guard()->check()): ?>

<header class="app-header d-flex align-items-center justify-content-between">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo e(route('dashboard')); ?>">
        <?php if(\App\Models\Setting::logoUrl()): ?>
            <img src="<?php echo e(\App\Models\Setting::logoUrl()); ?>" alt="logo">
        <?php else: ?>
            <i class="bi bi-building fs-1"></i>
        <?php endif; ?>
        <span>
            <span class="d-block brand-name"><?php echo e(\App\Models\Setting::siteName()); ?></span>
            <span class="brand-slogan d-none d-md-block"><?php echo e(\App\Models\Setting::tagline()); ?></span>
        </span>
    </a>
    <div class="dropdown">
        <a class="user-chip text-decoration-none d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5 text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline"><?php echo e(auth()->user()->name); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2">
                <div class="fw-semibold"><?php echo e(auth()->user()->name); ?></div>
                <div class="text-muted small"><?php echo e(auth()->user()->roles->pluck('name')->join(', ') ?: 'No role'); ?></div>
                <?php if(auth()->user()->institution): ?><div class="text-muted small"><?php echo e(auth()->user()->institution->name); ?></div><?php endif; ?>
                <?php if(auth()->user()->department): ?><div class="text-muted small">MIT — <?php echo e(auth()->user()->department->name); ?></div><?php endif; ?>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>" href="<?php echo e(route('profile.show')); ?>"><i class="bi bi-person me-2"></i>Profile &amp; API Tokens</a></li>
            <li>
                <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?>
                    <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                </form>
            </li>
        </ul>
    </div>
</header>


<nav class="navbar navbar-expand-lg app-menubar">
    <div class="container-fluid px-0">
        <button class="navbar-toggler ms-auto" data-bs-toggle="collapse" data-bs-target="#topNavMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="topNavMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.view')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-inbox me-1"></i>Submissions</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('submissions.index') ? 'active' : ''); ?>" href="<?php echo e(route('submissions.index')); ?>"><i class="bi bi-list-ul me-2"></i>All Submissions</a></li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.create')): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('submissions.create') ? 'active' : ''); ?>" href="<?php echo e(route('submissions.create')); ?>"><i class="bi bi-plus-lg me-2"></i>New Submission</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-journal-text me-1"></i>Data Catalogue</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('datasets.index') ? 'active' : ''); ?>" href="<?php echo e(route('datasets.index')); ?>"><i class="bi bi-list-ul me-2"></i>All Datasets</a></li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['datasets.manage', 'datasets.manage-own'])): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('datasets.create') ? 'active' : ''); ?>" href="<?php echo e(route('datasets.create')); ?>"><i class="bi bi-plus-lg me-2"></i>New Dataset</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['periods.manage', 'periods.manage-own'])): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('periods.*') ? 'active' : ''); ?>" href="<?php echo e(route('periods.index')); ?>"><i class="bi bi-calendar3 me-2"></i>Submission Periods</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['consumers.manage', 'consumers.manage-own'])): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('consumers.*') ? 'active' : ''); ?>" href="<?php echo e(route('consumers.index')); ?>"><i class="bi bi-people me-2"></i>Data Consumers</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.view')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-bar-chart me-1"></i>Reports</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('reports.thematic*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.thematic')); ?>"><i class="bi bi-journal-richtext me-2"></i>Management Reports (R1–R14)</a></li>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('reports.consolidated') ? 'active' : ''); ?>" href="<?php echo e(route('reports.consolidated')); ?>"><i class="bi bi-pie-chart me-2"></i>Consolidated Report</a></li>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('reports.compliance') ? 'active' : ''); ?>" href="<?php echo e(route('reports.compliance')); ?>"><i class="bi bi-check2-square me-2"></i>Submission Compliance</a></li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.export')): ?>
                        <li><a class="dropdown-item" href="<?php echo e(route('reports.export')); ?>"><i class="bi bi-download me-2"></i>Export CSV</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['institutions.manage', 'departments.manage', 'users.manage', 'users.manage-own', 'roles.manage', 'audit.view', 'settings.manage'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-gear me-1"></i>Administration</a>
                    <ul class="dropdown-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('institutions.manage')): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('institutions.*') ? 'active' : ''); ?>" href="<?php echo e(route('institutions.index')); ?>"><i class="bi bi-bank me-2"></i>Institutions</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.manage')): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('departments.*') ? 'active' : ''); ?>" href="<?php echo e(route('departments.index')); ?>"><i class="bi bi-diagram-3 me-2"></i>Ministry Departments</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['users.manage', 'users.manage-own'])): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-people me-2"></i>Users</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.manage')): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>" href="<?php echo e(route('roles.index')); ?>"><i class="bi bi-shield-lock me-2"></i>Roles &amp; Permissions</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('settings.manage')): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.edit')); ?>"><i class="bi bi-sliders me-2"></i>Portal Settings</a></li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('audit.view')): ?>
                        <li><a class="dropdown-item <?php echo e(request()->routeIs('audit.*') ? 'active' : ''); ?>" href="<?php echo e(route('audit.index')); ?>"><i class="bi bi-shield-check me-2"></i>Audit Trail</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container-fluid px-4">
    <div class="page-title-bar py-3">
        <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bi bi-house-door me-1"></i>Home</a></li>
                <?php echo $__env->yieldContent('breadcrumb'); ?>
            </ol>
        </nav>
        <?php endif; ?>
        <h1 class="mb-0"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
    </div>
    <main class="pb-4">
        <?php if(session('success')): ?><div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if(session('error')): ?><div class="alert alert-danger alert-dismissible fade show"><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if($errors->any()): ?><div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <footer class="text-center text-muted small py-3 border-top"><?php echo e(\App\Models\Setting::siteName()); ?> &copy; <?php echo e(date('Y')); ?></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
<script>
/** Shared DataTable factory with export buttons. Pass ajaxUrl + columns for AJAX mode, or null for a DOM table. */
function vpDataTable(selector, ajaxUrl, columns, options) {
    options = options || {};
    const cfg = Object.assign({
        responsive: true,
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        layout: {
            topStart: {
                buttons: [
                    { extend: 'copyHtml5', className: 'btn btn-sm btn-outline-secondary', text: '<i class="bi bi-clipboard me-1"></i>Copy', exportOptions: { columns: ':visible:not(.no-export)' } },
                    { extend: 'csvHtml5', className: 'btn btn-sm btn-outline-secondary', text: '<i class="bi bi-filetype-csv me-1"></i>CSV', exportOptions: { columns: ':visible:not(.no-export)' } },
                    { extend: 'excelHtml5', className: 'btn btn-sm btn-outline-secondary', text: '<i class="bi bi-filetype-xlsx me-1"></i>Excel', exportOptions: { columns: ':visible:not(.no-export)' } },
                    { extend: 'pdfHtml5', className: 'btn btn-sm btn-outline-secondary', text: '<i class="bi bi-filetype-pdf me-1"></i>PDF', orientation: 'landscape', exportOptions: { columns: ':visible:not(.no-export)' } },
                    { extend: 'print', className: 'btn btn-sm btn-outline-secondary', text: '<i class="bi bi-printer me-1"></i>Print', exportOptions: { columns: ':visible:not(.no-export)' } },
                    { extend: 'colvis', className: 'btn btn-sm btn-outline-secondary', text: '<i class="bi bi-layout-three-columns me-1"></i>Columns' }
                ]
            },
            topEnd: 'search',
            bottomStart: 'pageLength',
            bottomEnd: ['info', 'paging']
        },
        language: { emptyTable: 'No records found.' }
    }, options);
    if (ajaxUrl) {
        cfg.ajax = { url: ajaxUrl, dataSrc: 'data' };
        cfg.columns = columns;
    }
    return new DataTable(selector, cfg);
}
</script>
<script>
(function () {
    const loader = document.getElementById('pageLoader');
    function hideLoader() {
        loader.style.transition = 'opacity .3s ease';
        loader.style.opacity = '0';
        setTimeout(() => loader.style.display = 'none', 320);
    }
    if (document.readyState === 'complete') { hideLoader(); }
    else { window.addEventListener('load', hideLoader); setTimeout(hideLoader, 3500); }
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.tagName === 'FORM' && !e.target.target) {
            loader.style.display = 'flex';
            requestAnimationFrame(() => { loader.style.opacity = '1'; });
        }
    });
})();
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/layouts/app.blade.php ENDPATH**/ ?>