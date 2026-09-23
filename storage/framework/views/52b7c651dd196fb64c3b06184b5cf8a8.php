<?php $__env->startSection('title', $def['key'].' — '.$def['title']); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('reports.thematic')); ?>">Management Reports</a></li>
    <li class="breadcrumb-item active"><?php echo e($def['key']); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
window.vpChart = function (id, config) {
    const el = document.getElementById(id);
    if (el && window.Chart) { new Chart(el.getContext('2d'), config); }
};
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('reports.thematic')); ?>"><i class="bi bi-journal-richtext me-1"></i>Management Reports</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.consolidated')); ?>"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.compliance')); ?>"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
</ul>


<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="badge text-bg-navy px-3 py-2 fs-6"><?php echo e($def['key']); ?></span>
            <span class="badge <?php echo e($def['priority'] === 'High' ? 'text-bg-danger' : 'text-bg-secondary'); ?>"><?php echo e($def['priority']); ?> priority</span>
            <span class="badge text-bg-light border"><i class="bi bi-people me-1"></i><?php echo e($def['audience']); ?></span>
            <span class="badge text-bg-light border"><i class="bi bi-calendar3 me-1"></i><?php echo e($def['frequency']); ?></span>
            <span class="badge text-bg-light border"><i class="bi bi-layers me-1"></i>Tier <?php echo e($def['tier']); ?> — <?php echo e($def['tier'] === 3 ? 'published data only' : 'Ministry-approved data'); ?></span>
        </div>
        <h4 class="fw-bold mb-2"><?php echo e($def['title']); ?></h4>
        <p class="text-muted mb-2"><?php echo e($def['summary']); ?></p>
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:#f1f5fb;border-left:3px solid #1d2f6f">
                    <div class="small fw-bold text-uppercase text-muted mb-1"><i class="bi bi-database me-1"></i>Source data parameters</div>
                    <div class="small"><?php echo e($def['parameters']); ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:#f2faf4;border-left:3px solid #16a34a">
                    <div class="small fw-bold text-uppercase text-muted mb-1"><i class="bi bi-signpost-2 me-1"></i>Decisions it supports</div>
                    <div class="small"><?php echo e($def['decisions']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card mb-4 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Reporting period</label>
                <select name="period" class="form-select form-select-sm">
                    <option value="all" <?php if($filters['period'] === 'all'): echo 'selected'; endif; ?>>All periods</option>
                    <?php $__currentLoopData = $periods['years']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($year); ?>" <?php if($filters['period'] === $year): echo 'selected'; endif; ?>>Year <?php echo e($year); ?> (annual roll-up)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $periods['periods']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p); ?>" <?php if($filters['period'] === $p): echo 'selected'; endif; ?>><?php echo e($p); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php if (! (auth()->user()->isInstitutionUser() || auth()->user()->isMinistryDepartmentUser())): ?>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Institution</label>
                <select name="institution" class="form-select form-select-sm">
                    <option value="all" <?php if($filters['institution'] === 'all'): echo 'selected'; endif; ?>>All institutions</option>
                    <option value="MIT" <?php if($filters['institution'] === 'MIT'): echo 'selected'; endif; ?>>Ministry departments</option>
                    <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inst->code); ?>" <?php if($filters['institution'] === $inst->code): echo 'selected'; endif; ?>><?php echo e($inst->code); ?> — <?php echo e($inst->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
                <a href="<?php echo e(route('reports.thematic.show', strtolower($def['key']))); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.export')): ?>
            <div class="col-auto ms-auto">
                <a href="<?php echo e(route('reports.thematic.export', array_merge([strtolower($def['key'])], array_filter(request()->only('period', 'institution'))))); ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php $chartIdx = 0; ?>

<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php if($section['type'] === 'kpis'): ?>
        
        <div class="row g-3 mb-3">
            <?php $__currentLoopData = collect($section['items'])->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100 text-center kpi-card">
                    <div class="card-body py-3 px-2">
                        <div class="fs-4 fw-bold text-primary"><?php echo e($item['display']); ?></div>
                        <div class="small text-muted"><?php echo e($item['label']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-list-ol me-2"></i><?php echo e($section['title']); ?></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Indicator</th><th class="text-end">Value</th><th class="text-muted">Basis</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item['label']); ?></td>
                            <td class="text-end fw-bold"><?php echo e($item['display']); ?></td>
                            <td class="text-muted small"><?php echo e($item['basis'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if($section['type'] === 'breakdown'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-pie-chart me-2"></i><?php echo e($section['title']); ?></div>
            <div class="card-body">
                <?php if(empty($section['rows'])): ?>
                    <div class="text-muted small">No approved data for this breakdown in the selected scope.</div>
                <?php else: ?>
                <div class="row align-items-center g-3">
                    <div class="col-md-7">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light"><tr><th>Category</th><th class="text-end">Records</th><th class="text-end">Share</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $section['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($row['label']); ?></td>
                                    <td class="text-end"><?php echo e(number_format($row['count'])); ?></td>
                                    <td class="text-end text-muted"><?php echo e($section['total'] > 0 ? number_format($row['count'] / $section['total'] * 100, 1) : 0); ?>%</td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-5"><canvas id="chart<?php echo e($chartIdx); ?>" height="220"></canvas></div>
                </div>
                <?php $__env->startPush('scripts'); ?>
                <script>
                vpChart('chart<?php echo e($chartIdx); ?>', {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode(collect($section['rows'])->pluck('label'), 15, 512) ?>,
                        datasets: [{ data: <?php echo json_encode(collect($section['rows'])->pluck('count'), 15, 512) ?>,
                            backgroundColor: ['#1d2f6f','#2f5fb3','#16a34a','#ffc928','#e05c2a','#7c3aed','#0ea5b7','#94a3b8','#cbd5e1'] }]
                    },
                    options: { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } }, cutout: '58%' }
                });
                </script>
                <?php $__env->stopPush(); ?>
                <?php $chartIdx++; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($section['type'] === 'trend'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-graph-up me-2"></i><?php echo e($section['title']); ?></span>
                <?php if($section['delta'] !== null): ?>
                <span class="badge <?php echo e($section['delta'] >= 0 ? 'text-bg-success' : 'text-bg-danger'); ?>">
                    <i class="bi bi-arrow-<?php echo e($section['delta'] >= 0 ? 'up' : 'down'); ?>-right me-1"></i><?php echo e($section['delta'] > 0 ? '+' : ''); ?><?php echo e($section['delta']); ?>% vs previous period
                </span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if(empty($section['points'])): ?>
                    <div class="text-muted small">No approved data for this trend in the selected scope.</div>
                <?php else: ?>
                <canvas id="chart<?php echo e($chartIdx); ?>" height="80"></canvas>
                <?php $__env->startPush('scripts'); ?>
                <script>
                vpChart('chart<?php echo e($chartIdx); ?>', {
                    type: '<?php echo e(($section['measure']['type'] ?? 'count') === 'count' ? 'bar' : 'line'); ?>',
                    data: {
                        labels: <?php echo json_encode(collect($section['points'])->pluck('period'), 15, 512) ?>,
                        datasets: [{
                            label: <?php echo json_encode($section['title'], 15, 512) ?>,
                            data: <?php echo json_encode(collect($section['points'])->pluck('value'), 15, 512) ?>,
                            backgroundColor: 'rgba(29,47,111,.75)', borderColor: '#1d2f6f',
                            borderWidth: 2, fill: <?php echo e(($section['measure']['type'] ?? 'count') === 'count' ? 'true' : "'origin'"); ?>,
                            tension: .3, pointRadius: 4
                        }]
                    },
                    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
                </script>
                <?php $__env->stopPush(); ?>
                <?php $chartIdx++; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($section['type'] === 'submission_compliance'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-check2-square me-2"></i><?php echo e($section['title']); ?></span>
                <span class="badge text-bg-navy">Period: <?php echo e($section['period_used']); ?></span>
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
                        <?php $__empty_1 = true; $__currentLoopData = $section['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code><?php echo e($row['owner']); ?></code> <span class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($row['name'], 38)); ?></span></td>
                            <td class="text-end"><?php echo e($row['expected']); ?></td>
                            <td class="text-end"><?php echo e($row['received']); ?></td>
                            <td class="text-end">
                                <?php if($row['coverage'] !== null): ?>
                                    <span class="badge <?php echo e($row['coverage'] >= 80 ? 'text-bg-success' : ($row['coverage'] >= 50 ? 'text-bg-warning' : 'text-bg-danger')); ?>"><?php echo e($row['coverage']); ?>%</span>
                                <?php else: ?> — <?php endif; ?>
                            </td>
                            <td class="text-end"><span class="badge text-bg-success"><?php echo e($row['approved']); ?></span></td>
                            <td class="text-end"><span class="badge text-bg-warning"><?php echo e($row['pipeline']); ?></span></td>
                            <td class="text-end"><span class="badge text-bg-danger"><?php echo e($row['returned']); ?></span></td>
                            <td class="text-end fw-semibold"><?php echo e($row['first_pass'] !== null ? $row['first_pass'].'%' : '—'); ?></td>
                            <td class="text-end"><?php echo e($row['returns']); ?></td>
                            <td class="text-end"><?php echo e($row['timeliness'] !== null ? $row['timeliness'].'%' : '—'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="10" class="text-muted">No reporting activity in the selected scope.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                First-pass rate = submissions never returned for rectification ÷ received (from the audit trail).
                On-time = submitted before the configured period deadline, where one is configured.
            </div>
        </div>
    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/reports/thematic/show.blade.php ENDPATH**/ ?>