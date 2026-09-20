<?php $__env->startSection('title', 'Submissions'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <select name="status" class="form-select form-select-sm" style="width:auto">
            <option value="">All statuses</option>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($st); ?>" <?php if(request('status') === $st): echo 'selected'; endif; ?>><?php echo e(str_replace('_',' ',ucfirst($st))); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="period" class="form-select form-select-sm" style="width:auto">
            <option value="">All periods</option>
            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>" <?php if(request('period') === $p): echo 'selected'; endif; ?>><?php echo e($p); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="btn btn-sm btn-primary">Filter</button>
    </form>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.create')): ?>
    <a href="<?php echo e(route('submissions.create')); ?>" class="btn btn-success"><i class="bi bi-plus-lg"></i> New Submission</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-hover mb-0">
            <thead><tr><th>Reference</th><th>Institution</th><th>Dataset</th><th>Period</th><th>Channel</th><th>Records</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="fw-semibold"><?php echo e($s->reference); ?></td>
                    <td><?php echo e($s->institution->code); ?></td>
                    <td><?php echo e($s->dataset->code); ?> — <?php echo e($s->dataset->name); ?></td>
                    <td><?php echo e($s->reporting_period); ?></td>
                    <td><span class="badge text-bg-light text-uppercase"><?php echo e($s->channel); ?></span></td>
                    <td><?php echo e($s->records_count); ?></td>
                    <td><span class="badge text-bg-<?php echo e($s->statusBadge()); ?>"><?php echo e(str_replace('_',' ',ucfirst($s->status))); ?></span></td>
                    <td><?php echo e($s->submitted_at?->format('d M Y H:i') ?? '—'); ?></td>
                    <td><a href="<?php echo e(route('submissions.show', $s)); ?>" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">No submissions found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($submissions->hasPages()): ?><div class="card-footer"><?php echo e($submissions->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/submissions/index.blade.php ENDPATH**/ ?>