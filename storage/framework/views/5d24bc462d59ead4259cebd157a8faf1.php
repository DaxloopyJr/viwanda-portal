<?php $__env->startSection('title', __('Sign in')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label class="sa-form-label mb-1"><?php echo e(__('Email address')); ?></label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('Enter email address')); ?>" required autofocus>
        </div>
    </div>
    <div class="mb-3">
        <label class="sa-form-label mb-1"><?php echo e(__('Password')); ?></label>
        <div class="input-group sa-input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="<?php echo e(__('Enter password')); ?>" required>
        </div>
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label small" for="remember"><?php echo e(__('Remember me')); ?></label>
    </div>
    <button class="btn btn-sa-primary w-100"><?php echo e(__('Sign in')); ?> <i class="bi bi-arrow-right ms-1"></i></button>
    <p class="text-center small mt-3 mb-0 text-muted"><?php echo e(__('Accounts are issued by your institution administrator.')); ?></p>
    <p class="text-center small mt-2 mb-0"><a class="auth-footer-link" href="<?php echo e(route('landing')); ?>">&larr; <?php echo e(__('Back to portal home')); ?></a></p>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/auth/login.blade.php ENDPATH**/ ?>