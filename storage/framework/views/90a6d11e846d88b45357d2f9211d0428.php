<?php $__env->startSection('title', $dataset->code.' — '.$dataset->name); ?>
<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <span>Dataset definition</span>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('datasets.manage')): ?><a href="<?php echo e(route('datasets.edit', $dataset)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a><?php endif; ?>
            </div>
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-4">Code</dt><dd class="col-8"><?php echo e($dataset->code); ?></dd>
                    <dt class="col-4">Name</dt><dd class="col-8"><?php echo e($dataset->name); ?></dd>
                    <dt class="col-4">Description</dt><dd class="col-8"><?php echo e($dataset->description); ?></dd>
                    <dt class="col-4">Institution</dt><dd class="col-8"><?php echo e($dataset->institution->name); ?></dd>
                    <dt class="col-4">Frequency</dt><dd class="col-8"><?php echo e($dataset->frequency); ?></dd>
                    <dt class="col-4">Priority</dt><dd class="col-8"><?php echo e(ucfirst($dataset->priority)); ?></dd>
                    <dt class="col-4">Source system</dt><dd class="col-8"><?php echo e($dataset->source_system ?? 'None (manual entry)'); ?></dd>
                    <dt class="col-4">Consumers</dt><dd class="col-8"><?php echo e($dataset->consumers ?? '—'); ?></dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Data dictionary (<?php echo e(count($dataset->fields ?? [])); ?> fields)</span>
                <a href="<?php echo e(route('datasets.template', $dataset)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> CSV template</a>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Required</th><th>Allowed values</th></tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $dataset->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><code><?php echo e($f['name']); ?></code></td>
                            <td><?php echo e($f['label']); ?></td>
                            <td><?php echo e($f['type'] ?? 'string'); ?></td>
                            <td><?php echo ($f['required'] ?? false) ? '<span class="badge text-bg-danger">Mandatory</span>' : '<span class="badge text-bg-secondary">Optional</span>'; ?></td>
                            <td class="small"><?php echo e(!empty($f['options']) ? implode(', ', $f['options']) : '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Recent submissions (<?php echo e($dataset->submissions_count); ?>)</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Reference</th><th>Period</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('submissions.show', $s)); ?>"><?php echo e($s->reference); ?></a></td>
                            <td><?php echo e($s->reporting_period); ?></td>
                            <td><span class="badge text-bg-<?php echo e($s->statusBadge()); ?>"><?php echo e(str_replace('_',' ',ucfirst($s->status))); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="text-center text-muted py-3">No submissions yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/datasets/show.blade.php ENDPATH**/ ?>