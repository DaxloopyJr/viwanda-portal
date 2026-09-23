<?php $__env->startSection('title', 'Portal Settings'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Portal Settings</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Branding &amp; Identity</div>
            <div class="card-body">
                <p class="text-muted small">These settings control the portal name and logo shown on the landing page, the floating header, and the navigation bar after login.</p>
                <form method="POST" action="<?php echo e(route('settings.update')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="mb-3">
                        <label class="form-label">Portal name *</label>
                        <input name="site_name" class="form-control" value="<?php echo e(old('site_name', $siteName)); ?>" required maxlength="120">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input name="site_tagline" class="form-control" value="<?php echo e(old('site_tagline', $tagline)); ?>" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <?php if($logoUrl): ?>
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 border rounded">
                                <img src="<?php echo e($logoUrl); ?>" alt="logo" style="height:56px;object-fit:contain">
                                <div class="form-check">
                                    <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="removeLogo">
                                    <label class="form-check-label" for="removeLogo">Remove current logo</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                        <div class="form-text">PNG, JPG, SVG or WebP, max 2 MB. Leave empty to keep the current logo.</div>
                    </div>
                    <button class="btn btn-success"><i class="bi bi-save"></i> Save settings</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Preview</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 p-2 rounded-pill shadow-sm border">
                    <?php if($logoUrl): ?>
                        <img src="<?php echo e($logoUrl); ?>" style="height:34px;width:34px;object-fit:contain" alt="">
                    <?php else: ?>
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="height:34px;width:34px;background:linear-gradient(135deg,#0e8a9c,#1a73e8)"><i class="bi bi-building"></i></span>
                    <?php endif; ?>
                    <span class="fw-bold" style="color:#0b7285"><?php echo e($siteName); ?></span>
                </div>
                <p class="text-muted small mt-3 mb-0">Header preview as it appears on the landing page.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/settings/edit.blade.php ENDPATH**/ ?>