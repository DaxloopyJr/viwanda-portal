<?php $__env->startSection('title', $user->exists ? 'Edit User: ' . $user->name : 'New User'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('users.index')); ?>">Users</a></li>
    <li class="breadcrumb-item active"><?php echo e($user->exists ? 'Edit ' . $user->name : 'New user'); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($user->exists ? route('users.update', $user) : route('users.store')); ?>">
    <?php echo csrf_field(); ?>
    <?php if($user->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><i class="bi bi-person me-2"></i>Account Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $user->name)); ?>" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <?php echo e($user->exists ? '(leave blank to keep current)' : ''); ?> <?php echo $user->exists ? '' : '<span class="text-danger">*</span>'; ?></label>
                            <input type="password" name="password" class="form-control" <?php echo e($user->exists ? '' : 'required'); ?> autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" <?php echo e($user->exists ? '' : 'required'); ?> autocomplete="new-password">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Institution</label>
                            <?php if($lockedInstitution): ?>
                                <input type="hidden" name="institution_id" value="<?php echo e(auth()->user()->institution_id); ?>">
                                <input type="text" class="form-control" value="<?php echo e($institutions->first()->name ?? auth()->user()->institution->name); ?>" disabled>
                                <div class="form-text">Institution administrators can only manage users of their own institution.</div>
                            <?php else: ?>
                                <select name="institution_id" class="form-select">
                                    <option value="">— Ministry (no institution) —</option>
                                    <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($institution->id); ?>" <?php if((string) old('institution_id', $user->institution_id) === (string) $institution->id): echo 'selected'; endif; ?>><?php echo e($institution->code); ?> — <?php echo e($institution->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?php if(old('is_active', $user->exists ? $user->is_active : true)): echo 'checked'; endif; ?>>
                                <label class="form-check-label" for="isActive">Active account</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-shield-lock me-2"></i>Roles <span class="text-danger">*</span></div>
                <div class="card-body" style="max-height: 480px; overflow-y: auto;">
                    <?php $selectedRoles = old('roles', $user->roles->pluck('name')->all()); ?>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded p-2 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo e($role->name); ?>" id="role_<?php echo e($role->id); ?>" <?php if(in_array($role->name, $selectedRoles)): echo 'checked'; endif; ?>>
                            <label class="form-check-label fw-semibold" for="role_<?php echo e($role->id); ?>"><?php echo e($role->name); ?></label>
                        </div>
                        <div class="small text-muted mt-1">
                            <?php $__currentLoopData = $role->permissions->pluck('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge text-bg-light border me-1 mb-1"><?php echo e($perm); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($lockedInstitution): ?>
                    <div class="form-text">Only institutional roles can be assigned.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?php echo e($user->exists ? 'Update User' : 'Create User'); ?></button>
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/users/form.blade.php ENDPATH**/ ?>