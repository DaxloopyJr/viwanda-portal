<?php $__env->startSection('title', 'Submissions'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Submissions</a></li>
    <li class="breadcrumb-item active">All submissions</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2">
        <select id="filterStatus" class="form-select">
            <option value="">All statuses</option>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($st); ?>"><?php echo e(\App\Models\Submission::STATUS_LABELS[$st] ?? ucfirst(str_replace('_',' ',$st))); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select id="filterPeriod" class="form-select">
            <option value="">All periods</option>
            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>"><?php echo e($p); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.create')): ?>
    <a href="<?php echo e(route('submissions.create')); ?>" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Submission</a>
    <?php endif; ?>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table id="submissionsTable" class="table table-striped table-hover w-100">
            <thead><tr>
                <th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th>
                <th>Channel</th><th>Status</th><th>Records</th><th>Submitted By</th><th>Created</th>
            </tr></thead>
        </table>
    </div>
</div></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const subsTable = vpDataTable('#submissionsTable', '<?php echo e(route('submissions.datatable')); ?>', [
    { data: 'reference', name: 'reference' },
    { data: 'institution', name: 'institution' },
    { data: 'dataset', name: 'dataset' },
    { data: 'period', name: 'period' },
    { data: 'channel', name: 'channel' },
    { data: 'status', name: 'status' },
    { data: 'records', name: 'records' },
    { data: 'submitter', name: 'submitter' },
    { data: 'created', name: 'created' }
], { order: [[8, 'desc']] });

function reloadWithFilters() {
    const params = new URLSearchParams();
    const st = document.getElementById('filterStatus').value;
    const pe = document.getElementById('filterPeriod').value;
    if (st) params.set('status', st);
    if (pe) params.set('period', pe);
    subsTable.ajax.url('<?php echo e(route('submissions.datatable')); ?>' + (params.toString() ? '?' + params.toString() : '')).load();
}
document.getElementById('filterStatus').addEventListener('change', reloadWithFilters);
document.getElementById('filterPeriod').addEventListener('change', reloadWithFilters);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/submissions/index.blade.php ENDPATH**/ ?>