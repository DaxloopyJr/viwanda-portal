<?php $__env->startSection('title', 'Consolidated Report'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active">Consolidated</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('reports.consolidated')); ?>"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.compliance')); ?>"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.export')): ?>
    <li class="nav-item ms-auto"><a class="nav-link" href="<?php echo e(route('reports.export', array_filter(request()->only(['institution', 'period', 'dataset', 'status']), fn ($v) => $v !== 'all'))); ?>"><i class="bi bi-download me-1"></i>Export CSV</a></li>
    <?php endif; ?>
</ul>

<p class="text-muted">Reliable Industry &amp; Trade Data for a Growing Tanzania — consolidated statistics across institutions and ministry departments.</p>


<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('reports.consolidated')); ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Institution</label>
                <select name="institution" class="form-select form-select-sm" <?php if(auth()->user()->isInstitutionUser()): echo 'disabled'; endif; ?>>
                    <option value="all">All institutions &amp; ministry</option>
                    <option value="MIT" <?php if($filters['institution'] === 'MIT'): echo 'selected'; endif; ?>>Ministry departments</option>
                    <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($inst->code); ?>" <?php if($filters['institution'] === $inst->code): echo 'selected'; endif; ?>><?php echo e($inst->code); ?> — <?php echo e($inst->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Reporting Period</label>
                <select name="period" class="form-select form-select-sm">
                    <option value="all">All periods</option>
                    <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($period); ?>" <?php if($filters['period'] === $period): echo 'selected'; endif; ?>><?php echo e($period); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Dataset</label>
                <select name="dataset" class="form-select form-select-sm">
                    <option value="all">All datasets</option>
                    <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ds->code); ?>" <?php if($filters['dataset'] === $ds->code): echo 'selected'; endif; ?>><?php echo e($ds->code); ?> — <?php echo e(\Illuminate\Support\Str::limit($ds->name, 35)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all">All statuses</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($st); ?>" <?php if($filters['status'] === $st): echo 'selected'; endif; ?>><?php echo e(\App\Models\Submission::STATUS_LABELS[$st] ?? ucfirst($st)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-sm btn-primary flex-fill" title="Apply filters"><i class="bi bi-funnel"></i></button>
                <a href="<?php echo e(route('reports.consolidated')); ?>" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>


<div class="row g-3 mb-3">
    <div class="col-6 col-md-2"><div class="stat-card blue p-3"><div class="sa-num"><?php echo e($summary['submissions']); ?></div><div class="sa-lbl">Submissions</div></div></div>
    <div class="col-6 col-md-2"><div class="stat-card teal p-3"><div class="sa-num"><?php echo e(number_format($summary['records'])); ?></div><div class="sa-lbl">Data Records</div></div></div>
    <div class="col-6 col-md-2"><div class="stat-card navy p-3"><div class="sa-num"><?php echo e($summary['published']); ?></div><div class="sa-lbl">Published</div></div></div>
    <div class="col-6 col-md-2"><div class="stat-card green p-3"><div class="sa-num"><?php echo e($summary['acceptance_rate'] !== null ? $summary['acceptance_rate'].'%' : '—'); ?></div><div class="sa-lbl">Acceptance Rate</div></div></div>
    <div class="col-6 col-md-2"><div class="stat-card purple p-3"><div class="sa-num"><?php echo e($summary['datasets_covered']); ?></div><div class="sa-lbl">Datasets Covered</div></div></div>
    <div class="col-6 col-md-2"><div class="stat-card orange p-3"><div class="sa-num"><?php echo e($summary['reporters']); ?></div><div class="sa-lbl">Reporting Sources</div></div></div>
</div>


<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Submissions by Institution / Department</div>
            <div class="card-body"><canvas id="instChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Status Distribution</div>
            <div class="card-body"><canvas id="statusDonut" height="220"></canvas></div>
        </div>
    </div>
</div>


<div class="card mb-3">
    <div class="card-header"><i class="bi bi-bank me-2"></i>Statistical Summary by Institution</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="consolidatedTable" class="table table-hover align-middle w-100">
                <thead><tr><th>Code</th><th>Institution / Department</th><th>Submissions</th><th>Records</th><th>Published</th><th>Accepted</th><th>In Pipeline</th><th>Rejected / Returned</th></tr></thead>
            </table>
        </div>
    </div>
</div>


<div class="card mb-3">
    <div class="card-header"><i class="bi bi-journal-check me-2"></i>Dataset Coverage <?php echo e($filters['period'] !== 'all' ? '— '.$filters['period'] : '(all periods)'); ?></div>
    <div class="table-responsive">
        <table id="coverageTable" class="table table-striped align-middle w-100">
            <thead><tr><th>Dataset</th><th>Owner</th><th>Frequency</th><th>Priority</th><th>Submissions</th><th>Latest Status</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $coverage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><span class="fw-semibold"><?php echo e($row['dataset']->code); ?></span> <span class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($row['dataset']->name, 45)); ?></span></td>
                    <td><?php echo e($row['dataset']->ownerLabel()); ?></td>
                    <td><?php echo e(ucfirst($row['dataset']->frequency)); ?></td>
                    <td><span class="badge text-bg-<?php echo e($row['dataset']->priority === 'high' ? 'danger' : ($row['dataset']->priority === 'medium' ? 'warning' : 'secondary')); ?>"><?php echo e(ucfirst($row['dataset']->priority)); ?></span></td>
                    <td><?php echo e($row['count']); ?></td>
                    <td>
                        <?php if($row['latest']): ?>
                            <a href="<?php echo e(route('submissions.show', $row['latest'])); ?>" class="text-decoration-none"><span class="badge text-bg-<?php echo e($row['latest']->statusBadge()); ?>"><?php echo e($row['latest']->statusLabel()); ?></span></a>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">No submission</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>


<div class="card">
    <div class="card-header"><i class="bi bi-table me-2"></i>Detailed Submission Report</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="detailTable" class="table table-hover align-middle w-100">
                <thead><tr><th>Reference</th><th>Owner</th><th>Dataset</th><th>Period</th><th>Consumers</th><th>Records</th><th>Status</th><th>Submitted</th></tr></thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const filterQuery = '<?php echo e(http_build_query(array_filter($filters, fn ($v) => $v !== 'all'))); ?>';
const suffix = filterQuery ? '?' + filterQuery : '';

vpDataTable('#consolidatedTable', '<?php echo e(route('reports.consolidated.data')); ?>' + suffix, [
    { data: 'code' }, { data: 'name' }, { data: 'total' }, { data: 'records' },
    { data: 'published' }, { data: 'accepted' }, { data: 'pending' }, { data: 'rejected_returned' }
], { order: [[2, 'desc']] });

vpDataTable('#detailTable', '<?php echo e(route('reports.consolidated.detail')); ?>' + suffix, [
    { data: 'reference' }, { data: 'owner' }, { data: 'dataset' }, { data: 'period' },
    { data: 'consumers' }, { data: 'records' }, { data: 'status' }, { data: 'submitted' }
], { pageLength: 10 });

vpDataTable('#coverageTable', null, null, { pageLength: 10, order: [[0, 'asc']] });

let instChart = null, donut = null;
$('#consolidatedTable').on('xhr.dt', function (e, settings, json) {
    const strip = h => parseInt(String(h).replace(/<[^>]*>/g, '')) || 0;
    const labels = [], published = [], accepted = [], pending = [], rejected = [];
    let tP = 0, tA = 0, tPe = 0, tR = 0;
    (json.data || []).forEach(function (r) {
        labels.push(String(r.code).replace(/<[^>]*>/g, ''));
        const p = strip(r.published), a = strip(r.accepted), pe = strip(r.pending), rr = strip(r.rejected_returned);
        published.push(p); accepted.push(a); pending.push(pe); rejected.push(rr);
        tP += p; tA += a; tPe += pe; tR += rr;
    });
    if (instChart) instChart.destroy();
    instChart = new Chart(document.getElementById('instChart'), {
        type: 'bar',
        data: { labels, datasets: [
            { label: 'Published', data: published, backgroundColor: '#1a3c6e' },
            { label: 'Accepted', data: accepted, backgroundColor: '#198754' },
            { label: 'In pipeline', data: pending, backgroundColor: '#e6a23c' },
            { label: 'Rejected/Returned', data: rejected, backgroundColor: '#d9534f' }
        ]},
        options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } }
    });
    if (donut) donut.destroy();
    donut = new Chart(document.getElementById('statusDonut'), {
        type: 'doughnut',
        data: { labels: ['Published', 'Accepted', 'In pipeline', 'Rejected / Returned'],
            datasets: [{ data: [tP, tA, tPe, tR], backgroundColor: ['#1a3c6e', '#198754', '#e6a23c', '#d9534f'] }] },
        options: { plugins: { legend: { position: 'bottom' } }, cutout: '60%' }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/reports/consolidated.blade.php ENDPATH**/ ?>