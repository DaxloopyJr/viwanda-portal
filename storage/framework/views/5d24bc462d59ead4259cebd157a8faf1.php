<?php $__env->startSection('title', 'Sign in'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button class="btn btn-primary w-100" style="background:#1a3c6e">Sign in</button>
    <p class="text-center small mt-3 mb-0">Institution officer? <a href="<?php echo e(route('register')); ?>">Register an account</a></p>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/auth/login.blade.php ENDPATH**/ ?>