<?php $__env->startSection('title', 'Consolidated Report'); ?>
<?php $__env->startSection('content'); ?>
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('reports.consolidated')); ?>">Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.compliance')); ?>">Submission Compliance</a></li>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.export')): ?><li class="nav-item ms-auto"><a class="btn btn-sm btn-outline-success" href="<?php echo e(route('reports.export')); ?>"><i class="bi bi-download"></i> Export CSV</a></li><?php endif; ?>
</ul>

<div class="row g-3">
    <div class="col-lg-7"><div class="card h-100">
        <div class="card-header">Submissions by Institution</div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped mb-0">
                <thead><tr><th>Institution</th><th>Total</th><th>Published</th><th>Accepted</th><th>Pending</th><th>Returned/Rejected</th></tr></thead>
                <tbody>
                <?php $__currentLoopData = $byInstitution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($row->code); ?> — <?php echo e($row->name); ?></td>
                        <td><?php echo e($row->total); ?></td>
                        <td><span class="badge text-bg-primary"><?php echo e($row->published); ?></span></td>
                        <td><span class="badge text-bg-success"><?php echo e($row->accepted); ?></span></td>
                        <td><span class="badge text-bg-warning"><?php echo e($row->pending); ?></span></td>
                        <td><span class="badge text-bg-danger"><?php echo e($row->rejected_returned); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <tbody><?php $__currentLoopData = $byPeriod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($row->reporting_period); ?></td><td><?php echo e($row->total); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const rows = <?php echo json_encode($byInstitution, 15, 512) ?>;
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/reports/consolidated.blade.php ENDPATH**/ ?>