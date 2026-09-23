<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title'); ?> — <?php echo e(\App\Models\Setting::siteName()); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sa-primary: #4a66f0; --sa-primary-dark: #3b53d9; --sa-teal: #0e8a9c; }
        body {
            min-height: 100vh; display: flex; align-items: center;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #0e8a9c 0%, #095b6b 55%, #1a3c6e 100%);
            position: relative; overflow-x: hidden;
        }
        /* soft decorative waves, SmartAngular signin style */
        body::before, body::after {
            content: ""; position: fixed; border-radius: 50%; z-index: 0;
            background: rgba(255,255,255,.06);
        }
        body::before { width: 560px; height: 560px; top: -180px; left: -160px; }
        body::after { width: 680px; height: 680px; bottom: -260px; right: -220px; background: rgba(255,255,255,.05); }

        .auth-wrap { position: relative; z-index: 1; max-width: 440px; width: 100%; margin: auto; padding: 1.5rem 0; }
        .auth-card { border: none; border-radius: 1.1rem; box-shadow: 0 24px 70px rgba(6,40,58,.35); overflow: hidden; }
        .auth-logo { height: 72px; width: 72px; object-fit: contain; }
        .auth-emblem {
            height: 72px; width: 72px; border-radius: 18px; display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; background: linear-gradient(135deg, var(--sa-primary), var(--sa-teal));
            box-shadow: 0 10px 24px rgba(74,102,240,.35);
        }
        .auth-title { color: #22303c; font-size: 1.35rem; }
        .auth-sub { color: #8a94a6; }

        .sa-form-label { font-size: .8rem; font-weight: 600; color: #5b6b7a; letter-spacing: .02em; }
        .sa-input-group .input-group-text {
            background: #fff; border: 1px solid #e2e8f0; border-right: none; border-radius: .6rem 0 0 .6rem; color: #8a94a6;
        }
        .sa-input-group .form-control {
            border: 1px solid #e2e8f0; border-left: none; border-radius: 0 .6rem .6rem 0; padding: .65rem .9rem;
        }
        .sa-input-group .form-control:focus { border-color: var(--sa-primary); box-shadow: none; }
        .sa-input-group:focus-within .input-group-text { border-color: var(--sa-primary); color: var(--sa-primary); }
        .sa-input-group:focus-within { box-shadow: 0 0 0 .2rem rgba(74,102,240,.15); border-radius: .6rem; }

        .btn-sa-primary {
            background: var(--sa-primary); border-color: var(--sa-primary); color: #fff;
            border-radius: .6rem; padding: .7rem 1rem; font-weight: 600; letter-spacing: .02em;
        }
        .btn-sa-primary:hover { background: var(--sa-primary-dark); border-color: var(--sa-primary-dark); color: #fff; }
        .form-check-input:checked { background-color: var(--sa-primary); border-color: var(--sa-primary); }
        .auth-footer-link { color: var(--sa-teal); font-weight: 600; text-decoration: none; }
        .auth-footer-link:hover { color: var(--sa-teal); text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container auth-wrap">
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <?php if(\App\Models\Setting::logoUrl()): ?>
                        <img src="<?php echo e(\App\Models\Setting::logoUrl()); ?>" class="auth-logo mb-3" alt="logo">
                    <?php else: ?>
                        <span class="auth-emblem mb-3"><i class="bi bi-bank"></i></span>
                    <?php endif; ?>
                    <h4 class="fw-bold auth-title mb-1"><?php echo e(\App\Models\Setting::siteName()); ?></h4>
                    <p class="auth-sub small mb-0"><?php echo e(\App\Models\Setting::tagline()); ?></p>
                </div>
                <?php if($errors->any()): ?><div class="alert alert-danger py-2"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
        <p class="text-center small mt-3 mb-0" style="color:rgba(255,255,255,.75)"><?php echo e(__('Authorized users only. All activity is audited.')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/layouts/auth.blade.php ENDPATH**/ ?>