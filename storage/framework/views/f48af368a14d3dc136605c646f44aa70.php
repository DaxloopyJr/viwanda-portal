<?php $__env->startSection('title', 'Profile & API Tokens'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Profile & API Tokens</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person-circle me-2"></i>My Account</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8"><?php echo e($user->name); ?></dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?php echo e($user->email); ?></dd>
                    <dt class="col-sm-4">Institution</dt><dd class="col-sm-8"><?php echo e($user->institution->name ?? '— Ministry —'); ?></dd>
                    <dt class="col-sm-4">Roles</dt>
                    <dd class="col-sm-8">
                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge text-bg-primary me-1"><?php echo e($role->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </dd>
                    <dt class="col-sm-4">Member since</dt><dd class="col-sm-8"><?php echo e($user->created_at->format('d M Y')); ?></dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-key me-2"></i>Change Password</div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('profile.password')); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Password</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('api.access')): ?>
        <div class="card">
            <div class="card-header"><i class="bi bi-plug me-2"></i>API Tokens</div>
            <div class="card-body">
                <p class="small text-muted">API tokens allow system-to-system submission of data (ability: <code>submit</code>). Keep tokens secret — they are shown only once when created.</p>

                <?php if(session('api_token')): ?>
                <div class="alert alert-success">
                    <div class="fw-semibold mb-1"><i class="bi bi-check-circle me-1"></i>New token created — copy it now:</div>
                    <code class="user-select-all"><?php echo e(session('api_token')); ?></code>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('profile.tokens.create')); ?>" class="d-flex gap-2 mb-3">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="token_name" class="form-control" placeholder="Token name, e.g. MIS integration" required maxlength="60">
                    <button class="btn btn-primary text-nowrap"><i class="bi bi-plus-lg me-1"></i>Create Token</button>
                </form>

                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Name</th><th>Created</th><th>Last used</th><th></th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $user->tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($token->name); ?></td>
                            <td class="text-muted small"><?php echo e($token->created_at->format('d M Y H:i')); ?></td>
                            <td class="text-muted small"><?php echo e($token->last_used_at?->format('d M Y H:i') ?? 'Never'); ?></td>
                            <td class="text-end">
                                <form method="POST" action="<?php echo e(route('profile.tokens.revoke', $token->id)); ?>" onsubmit="return confirm('Revoke this token?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No API tokens.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body text-muted small">
                <i class="bi bi-info-circle me-1"></i>Your role does not include API access. Contact your administrator if your institution needs system-to-system integration.
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/profile/show.blade.php ENDPATH**/ ?>