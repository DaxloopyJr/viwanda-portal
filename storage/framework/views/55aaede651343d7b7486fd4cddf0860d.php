<?php $__env->startSection('title', 'Submission '.$submission->reference); ?>
<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><?php echo e($submission->reference); ?> — <?php echo e($submission->dataset->name); ?></span>
                <span class="badge text-bg-<?php echo e($submission->statusBadge()); ?> fs-6"><?php echo e(str_replace('_',' ',ucfirst($submission->status))); ?></span>
            </div>
            <div class="card-body">
                <div class="row small">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5">Institution</dt><dd class="col-7"><?php echo e($submission->institution->name); ?></dd>
                            <dt class="col-5">Dataset</dt><dd class="col-7"><?php echo e($submission->dataset->code); ?> — <?php echo e($submission->dataset->name); ?></dd>
                            <dt class="col-5">Period</dt><dd class="col-7"><?php echo e($submission->reporting_period); ?></dd>
                            <dt class="col-5">Channel</dt><dd class="col-7 text-uppercase"><?php echo e($submission->channel); ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5">Submitted by</dt><dd class="col-7"><?php echo e($submission->submitter?->name ?? '—'); ?></dd>
                            <dt class="col-5">Submitted at</dt><dd class="col-7"><?php echo e($submission->submitted_at?->format('d M Y H:i') ?? '—'); ?></dd>
                            <dt class="col-5">Reviewed by</dt><dd class="col-7"><?php echo e($submission->reviewer?->name ?? '—'); ?></dd>
                            <dt class="col-5">Txn reference</dt><dd class="col-7"><?php echo e($submission->transaction_reference ?? '—'); ?></dd>
                        </dl>
                    </div>
                </div>
                <?php if($submission->review_comments): ?>
                    <div class="alert alert-warning mt-3 mb-0"><strong>Review comments:</strong> <?php echo e($submission->review_comments); ?></div>
                <?php endif; ?>
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                <?php if($submission->isEditable()): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.edit-own')): ?>
                    <a href="<?php echo e(route('submissions.edit', $submission)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit records</a>
                    <form method="POST" action="<?php echo e(route('submissions.submit', $submission)); ?>" onsubmit="return confirm('Submit for Ministry review?')"><?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-success"><i class="bi bi-send"></i> Submit for review</button>
                    </form>
                    <?php endif; ?>
                    <?php if($submission->status === 'draft'): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.delete-own')): ?>
                    <form method="POST" action="<?php echo e(route('submissions.destroy', $submission)); ?>" onsubmit="return confirm('Delete this draft?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete draft</button>
                    </form>
                    <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.review')): ?>
                    <?php if($submission->status === 'submitted'): ?>
                    <form method="POST" action="<?php echo e(route('submissions.review', $submission)); ?>"><?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-warning"><i class="bi bi-eye"></i> Start review</button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.accept')): ?>
                    <?php if(in_array($submission->status, ['submitted','under_review'])): ?>
                    <form method="POST" action="<?php echo e(route('submissions.accept', $submission)); ?>" onsubmit="return confirm('Accept this submission?')"><?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Accept</button>
                    </form>
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#returnModal"><i class="bi bi-arrow-counterclockwise"></i> Return</button>
                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-lg"></i> Reject</button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('submissions.publish')): ?>
                    <?php if($submission->status === 'accepted'): ?>
                    <form method="POST" action="<?php echo e(route('submissions.publish', $submission)); ?>" onsubmit="return confirm('Publish to the central repository?')"><?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-primary"><i class="bi bi-globe"></i> Publish</button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Records (<?php echo e($submission->records_count); ?>)</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>#</th><?php $__currentLoopData = $submission->dataset->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th><?php echo e($f['label']); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $submission->records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($record->errors ? 'row-invalid' : ''); ?>">
                            <td><?php echo e($record->row_number); ?></td>
                            <?php $__currentLoopData = $submission->dataset->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <?php echo e($record->data[$f['name']] ?? ''); ?>

                                    <?php if(isset($record->errors[$f['name']])): ?>
                                        <div class="validation-error"><i class="bi bi-exclamation-triangle"></i> <?php echo e(implode(' ', $record->errors[$f['name']])); ?></div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Audit Trail</div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                <?php $__empty_1 = true; $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="mb-3">
                        <div class="fw-semibold small"><?php echo e(str_replace('.', ' — ', $log->action)); ?></div>
                        <div class="text-muted small"><?php echo e($log->user?->name ?? 'System'); ?> · <?php echo e($log->created_at->format('d M Y H:i')); ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="text-muted small">No audit events recorded yet.</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="returnModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="<?php echo e(route('submissions.return', $submission)); ?>"><?php echo csrf_field(); ?>
    <div class="modal-header"><h5 class="modal-title">Return for correction</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Comments for the institution <span class="text-danger">*</span></label>
        <textarea name="review_comments" class="form-control" rows="4" required></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-warning">Return submission</button></div>
    </form>
</div></div></div>


<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="<?php echo e(route('submissions.reject', $submission)); ?>"><?php echo csrf_field(); ?>
    <div class="modal-header"><h5 class="modal-title">Reject submission</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
        <textarea name="review_comments" class="form-control" rows="4" required></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-danger">Reject submission</button></div>
    </form>
</div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/submissions/show.blade.php ENDPATH**/ ?>