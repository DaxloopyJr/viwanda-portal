<?php $__env->startSection('title', 'Register'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('register')); ?>">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label">Full name</label>
        <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Institutional email</label>
        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Institution</label>
        <select name="institution_id" class="form-select" required>
            <option value="">Select institution…</option>
            <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($institution->id); ?>" <?php if(old('institution_id') == $institution->id): echo 'selected'; endif; ?>><?php echo e($institution->name); ?> (<?php echo e($institution->code); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100" style="background:#1a3c6e">Register</button>
    <p class="text-center small mt-3 mb-0">Already registered? <a href="<?php echo e(route('login')); ?>">Sign in</a></p>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/auth/register.blade.php ENDPATH**/ ?>