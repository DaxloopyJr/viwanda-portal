<?php $__env->startSection('title', 'Submission Compliance Report'); ?>
<?php $__env->startSection('content'); ?>
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.consolidated')); ?>">Consolidated</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('reports.compliance')); ?>">Submission Compliance</a></li>
</ul>

<form method="GET" class="d-flex gap-2 mb-3">
    <select name="period" class="form-select" style="width:auto">
        <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>" <?php if($period === $p): echo 'selected'; endif; ?>><?php echo e($p); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="btn btn-primary">Apply</button>
</form>

<div class="card"><div class="card-body table-responsive p-0">
    <table class="table table-striped mb-0">
        <thead><tr><th>Institution</th><th>Dataset</th><th>Frequency</th><th>Submission</th><th>Status</th><th>Compliance</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($row['institution']->code); ?></td>
                <td><?php echo e($row['dataset']->code); ?> — <?php echo e($row['dataset']->name); ?></td>
                <td><?php echo e($row['dataset']->frequency); ?></td>
                <td><?php if($row['submission']): ?><a href="<?php echo e(route('submissions.show', $row['submission'])); ?>"><?php echo e($row['submission']->reference); ?></a><?php else: ?> — <?php endif; ?></td>
                <td><?php if($row['submission']): ?><span class="badge text-bg-<?php echo e($row['submission']->statusBadge()); ?>"><?php echo e(str_replace('_',' ',ucfirst($row['submission']->status))); ?></span><?php else: ?> <span class="text-muted">No submission</span> <?php endif; ?></td>
                <td><?php if($row['compliant']): ?><span class="badge text-bg-success">Compliant</span><?php else: ?><span class="badge text-bg-danger">Outstanding</span><?php endif; ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/reports/compliance.blade.php ENDPATH**/ ?>