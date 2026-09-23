<?php $__env->startSection('title', $dataset->code . ' — ' . $dataset->name); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('datasets.index')); ?>">Data Catalogue</a></li>
    <li class="breadcrumb-item active"><?php echo e($dataset->code); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-text me-2"></i>Dataset Definition</span>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['datasets.manage', 'datasets.manage-own'])): ?>
                <a href="<?php echo e(route('datasets.edit', $dataset)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Code</dt><dd class="col-sm-8"><code><?php echo e($dataset->code); ?></code></dd>
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8"><?php echo e($dataset->name); ?></dd>
                    <dt class="col-sm-4">Institution</dt><dd class="col-sm-8"><?php echo e($dataset->institution->name ?? '—'); ?> (<?php echo e($dataset->institution->code ?? ''); ?>)</dd>
                    <dt class="col-sm-4">Frequency</dt><dd class="col-sm-8"><?php echo e(ucfirst($dataset->frequency)); ?></dd>
                    <dt class="col-sm-4">Priority</dt><dd class="col-sm-8"><span class="badge text-bg-<?php echo e($dataset->priority === 'high' ? 'danger' : ($dataset->priority === 'medium' ? 'warning' : 'secondary')); ?>"><?php echo e(ucfirst($dataset->priority)); ?></span></dd>
                    <dt class="col-sm-4">Source system</dt><dd class="col-sm-8"><?php echo e($dataset->source_system ?: '—'); ?></dd>
                    <dt class="col-sm-4">Consumers</dt><dd class="col-sm-8"><?php echo e($dataset->consumers ?: '—'); ?></dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><?php echo e($dataset->is_active ? 'Active' : 'Inactive'); ?></dd>
                    <dt class="col-sm-4">Description</dt><dd class="col-sm-8"><?php echo e($dataset->description ?: '—'); ?></dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-table me-2"></i>Data Fields (<?php echo e(count($dataset->fields ?? [])); ?>)</div>
            <div class="table-responsive">
                <table id="fieldsTable" class="table table-sm table-striped w-100">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Required</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $dataset->fields ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><code><?php echo e($field['name'] ?? ''); ?></code></td>
                            <td><?php echo e($field['label'] ?? ''); ?></td>
                            <td><?php echo e($field['type'] ?? 'string'); ?></td>
                            <td><?php echo !empty($field['required']) ? '<span class="badge text-bg-danger">Yes</span>' : '<span class="text-muted">No</span>'; ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox me-2"></i>Recent Submissions (<?php echo e($dataset->submissions_count); ?> total)</span>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.create')): ?>
                <a href="<?php echo e(route('submissions.create', ['dataset' => $dataset->code])); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Submission</a>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table id="recentTable" class="table table-hover align-middle w-100">
                    <thead><tr><th>Reference</th><th>Period</th><th>Status</th><th>Records</th><th>Submitted</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('submissions.show', $s)); ?>" class="text-decoration-none"><code><?php echo e($s->reference); ?></code></a></td>
                            <td><?php echo e($s->reporting_period); ?></td>
                            <td><span class="badge text-bg-<?php echo e($s->statusBadge()); ?>"><?php echo e($s->statusLabel()); ?></span></td>
                            <td><?php echo e($s->records_count); ?></td>
                            <td class="text-muted small"><?php echo e($s->created_at->format('d M Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No submissions for this dataset yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
vpDataTable('#recentTable', null, null, { pageLength: 10, order: [] });
vpDataTable('#fieldsTable', null, null, { pageLength: 10, order: [] });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/datasets/show.blade.php ENDPATH**/ ?>