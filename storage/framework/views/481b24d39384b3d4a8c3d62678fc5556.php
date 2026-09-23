<?php $__env->startSection('title', 'Submission Compliance'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active">Submission Compliance</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.consolidated')); ?>"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('reports.compliance')); ?>"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.export')): ?>
    <li class="nav-item ms-auto"><a class="nav-link" href="<?php echo e(route('reports.export')); ?>"><i class="bi bi-download me-1"></i>Export CSV</a></li>
    <?php endif; ?>
</ul>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-check2-square me-2"></i>Expected datasets vs. actual submissions</span>
        <div class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0 small text-muted">Period</label>
            <select id="filterPeriod" class="form-select form-select-sm" style="width:auto">
                <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p); ?>" <?php if($p === $period): echo 'selected'; endif; ?>><?php echo e($p); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="complianceTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Institution</th><th>Dataset</th><th>Frequency</th>
                        <th>Submission</th><th>Status</th><th>Compliance</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const complianceTable = vpDataTable('#complianceTable', '<?php echo e(route('reports.compliance.data', ['period' => $period])); ?>', [
    { data: 'institution', name: 'institution' },
    { data: 'dataset', name: 'dataset' },
    { data: 'frequency', name: 'frequency' },
    { data: 'submission', name: 'submission' },
    { data: 'status', name: 'status' },
    { data: 'compliance', name: 'compliance' }
]);
document.getElementById('filterPeriod').addEventListener('change', function () {
    complianceTable.ajax.url('<?php echo e(route('reports.compliance.data')); ?>?period=' + encodeURIComponent(this.value)).load();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/reports/compliance.blade.php ENDPATH**/ ?>