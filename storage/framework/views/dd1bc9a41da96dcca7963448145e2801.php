<?php $__env->startSection('title', $role->exists ? 'Edit Role: ' . $role->name : 'New Role'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($role->exists ? route('roles.update', $role) : route('roles.store')); ?>">
    <?php echo csrf_field(); ?>
    <?php if($role->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-shield-lock me-2"></i>Role Details</div>
                <div class="card-body">
                    <label class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $role->name)); ?>" required maxlength="100"
                           <?php echo e($role->name === 'System Administrator' ? 'readonly' : ''); ?>>
                    <?php if($role->name === 'System Administrator'): ?>
                        <div class="form-text">The protected administrative role — it cannot be renamed, and its core management permissions are always kept.</div>
                    <?php else: ?>
                        <div class="form-text">A descriptive name, e.g. <em>Regional Data Reviewer</em>.</div>
                    <?php endif; ?>
                    <div class="mt-3 small text-muted">
                        Select the permissions this role grants. Users with multiple roles receive the union of all their roles' permissions.
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?php echo e($role->exists ? 'Update Role' : 'Create Role'); ?></button>
                <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-key me-2"></i>Permissions</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="vpSetAllPerms(true)">Select all</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="vpSetAllPerms(false)">Clear</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php $selected = old('permissions', $role->exists ? $role->permissions->pluck('name')->all() : []); ?>
                    <div class="row g-3">
                        <?php $__currentLoopData = $permissionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-capitalize"><i class="bi bi-folder2-open me-1"></i><?php echo e($group); ?></span>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input group-toggle" type="checkbox" data-group="<?php echo e($group); ?>" id="grp_<?php echo e($group); ?>">
                                        <label class="form-check-label small text-muted" for="grp_<?php echo e($group); ?>">all</label>
                                    </div>
                                </div>
                                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check">
                                    <input class="form-check-input perm-check perm-<?php echo e($group); ?>" type="checkbox"
                                           name="permissions[]" value="<?php echo e($permission->name); ?>"
                                           id="perm_<?php echo e($permission->id); ?>" <?php if(in_array($permission->name, $selected)): echo 'checked'; endif; ?>>
                                    <label class="form-check-label" for="perm_<?php echo e($permission->id); ?>"><?php echo e($permission->name); ?></label>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function vpSetAllPerms(state) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = state);
}
document.querySelectorAll('.group-toggle').forEach(function (toggle) {
    toggle.addEventListener('change', function () {
        document.querySelectorAll('.perm-' + this.dataset.group).forEach(cb => cb.checked = this.checked);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/roles/form.blade.php ENDPATH**/ ?>