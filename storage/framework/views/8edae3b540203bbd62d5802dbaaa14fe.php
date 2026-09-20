<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted small">Active Institutions</div>
            <div class="display-6 fw-bold"><?php echo e($stats['institutions']); ?></div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green"><div class="card-body">
            <div class="text-muted small">Total Submissions</div>
            <div class="display-6 fw-bold"><?php echo e($stats['submissions']); ?></div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card orange"><div class="card-body">
            <div class="text-muted small">Pending Review</div>
            <div class="display-6 fw-bold"><?php echo e($stats['pending_review']); ?></div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card red"><div class="card-body">
            <div class="text-muted small">Acceptance Rate</div>
            <div class="display-6 fw-bold"><?php echo e($stats['acceptance_rate'] !== null ? $stats['acceptance_rate'].'%' : '—'); ?></div>
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
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.view')): ?><a href="<?php echo e(route('submissions.index')); ?>" class="btn btn-sm btn-outline-primary">View all</a><?php endif; ?>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead><tr><th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th><th>Channel</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('submissions.show', $s)); ?>"><?php echo e($s->reference); ?></a></td>
                            <td><?php echo e($s->institution->code); ?></td>
                            <td><?php echo e($s->dataset->code); ?></td>
                            <td><?php echo e($s->reporting_period); ?></td>
                            <td><span class="badge text-bg-light text-uppercase"><?php echo e($s->channel); ?></span></td>
                            <td><span class="badge text-bg-<?php echo e($s->statusBadge()); ?>"><?php echo e(str_replace('_', ' ', ucfirst($s->status))); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No submissions yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Submission Compliance — current period (<?php echo e($period); ?>)</div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped mb-0">
            <thead><tr><th>Institution</th><th>Integration</th><th>Submitted this period</th><th>Status</th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $compliance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($inst->code); ?> — <?php echo e($inst->name); ?></td>
                    <td><span class="badge text-bg-<?php echo e($inst->integration_mode === 'api' ? 'info' : 'light'); ?>"><?php echo e(strtoupper($inst->integration_mode)); ?></span></td>
                    <td><?php echo e($inst->period_submissions); ?></td>
                    <td>
                        <?php if($inst->period_submissions > 0): ?>
                            <span class="badge text-bg-success"><i class="bi bi-check-circle"></i> Compliant</span>
                        <?php else: ?>
                            <span class="badge text-bg-danger"><i class="bi bi-exclamation-circle"></i> Outstanding</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const trendCtx = document.getElementById('trendChart');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthlyLabels, 15, 512) ?>,
        datasets: [{
            label: 'Submissions',
            data: <?php echo json_encode($monthlyData, 15, 512) ?>,
            borderColor: '#1a3c6e', backgroundColor: 'rgba(26,60,110,.15)',
            fill: true, tension: .3, pointRadius: 4,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

const statusColors = { draft:'#6c757d', submitted:'#0dcaf0', under_review:'#ffc107', returned:'#e6a23c', accepted:'#198754', rejected:'#dc3545', published:'#1a3c6e' };
const statusOrder = <?php echo json_encode($statusOrder, 15, 512) ?>;
const statusCounts = <?php echo json_encode($statusCounts, 15, 512) ?>;
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
        labels: <?php echo json_encode($perInstitution->pluck('code'), 15, 512) ?>,
        datasets: [{ label: 'Submissions', data: <?php echo json_encode($perInstitution->pluck('total'), 15, 512) ?>, backgroundColor: '#1a9e5c' }]
    },
    options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/dashboard.blade.php ENDPATH**/ ?>