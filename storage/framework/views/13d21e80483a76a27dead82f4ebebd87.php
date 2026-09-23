<?php $__env->startSection('title', $institution->exists ? 'Edit Institution: ' . $institution->code : 'New Institution'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('institutions.index')); ?>">Institutions</a></li>
    <li class="breadcrumb-item active"><?php echo e($institution->exists ? 'Edit ' . $institution->code : 'New institution'); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-bank me-2"></i><?php echo e($institution->exists ? 'Update institution' : 'Register a reporting institution'); ?></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e($institution->exists ? route('institutions.update', $institution) : route('institutions.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if($institution->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="<?php echo e(old('code', $institution->code)); ?>" required maxlength="20" placeholder="e.g. FCC">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $institution->name)); ?>" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="<?php echo e(old('contact_email', $institution->contact_email)); ?>" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="<?php echo e(old('contact_phone', $institution->contact_phone)); ?>" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Integration Mode <span class="text-danger">*</span></label>
                            <select name="integration_mode" class="form-select" required>
                                <option value="manual" <?php if(old('integration_mode', $institution->integration_mode ?: 'manual') === 'manual'): echo 'selected'; endif; ?>>Manual (portal / upload)</option>
                                <option value="api" <?php if(old('integration_mode', $institution->integration_mode) === 'api'): echo 'selected'; endif; ?>>API (system-to-system)</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?php if(old('is_active', $institution->exists ? $institution->is_active : true)): echo 'checked'; endif; ?>>
                                <label class="form-check-label" for="isActive">Active — the institution can report data</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?php echo e($institution->exists ? 'Update Institution' : 'Register Institution'); ?></button>
                        <a href="<?php echo e(route('institutions.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Note</div>
            <div class="card-body small text-muted">
                <p>Each institution gets its own administrator who manages its users and its section of the data catalogue.</p>
                <p class="mb-0">Institutions with <strong>API</strong> integration submit data system-to-system using an API token created under <em>Profile &amp; API Tokens</em>. Manual institutions use the portal forms or CSV upload.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/institutions/form.blade.php ENDPATH**/ ?>