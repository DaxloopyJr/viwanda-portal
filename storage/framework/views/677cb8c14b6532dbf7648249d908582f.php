<?php $__env->startSection('title', 'Data Catalogue'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Approved datasets and their data dictionary definitions.</p>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('datasets.manage')): ?><a href="<?php echo e(route('datasets.create')); ?>" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Dataset</a><?php endif; ?>
</div>
<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped table-hover mb-0">
        <thead><tr><th>Code</th><th>Name</th><th>Institution</th><th>Frequency</th><th>Priority</th><th>Fields</th><th>Submissions</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="fw-semibold"><?php echo e($ds->code); ?></td>
                <td><?php echo e($ds->name); ?></td>
                <td><?php echo e($ds->institution->code); ?></td>
                <td><?php echo e($ds->frequency); ?></td>
                <td><span class="badge text-bg-<?php echo e($ds->priority === 'high' ? 'danger' : ($ds->priority === 'medium' ? 'warning' : 'secondary')); ?>"><?php echo e(ucfirst($ds->priority)); ?></span></td>
                <td><?php echo e(count($ds->fields ?? [])); ?></td>
                <td><?php echo e($ds->submissions_count); ?></td>
                <td><span class="badge text-bg-<?php echo e($ds->is_active ? 'success' : 'secondary'); ?>"><?php echo e($ds->is_active ? 'Active' : 'Inactive'); ?></span></td>
                <td><a href="<?php echo e(route('datasets.show', $ds)); ?>" class="btn btn-sm btn-outline-primary">Open</a></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php if($datasets->hasPages()): ?><div class="card-footer"><?php echo e($datasets->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/datasets/index.blade.php ENDPATH**/ ?>