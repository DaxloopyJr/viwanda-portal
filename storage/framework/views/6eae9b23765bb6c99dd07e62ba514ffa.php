<?php $__env->startSection('title', 'Management Reports'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active">Management Reports (R1–R14, N1–N10)</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="<?php echo e(route('reports.thematic')); ?>"><i class="bi bi-journal-richtext me-1"></i>Management Reports</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.consolidated')); ?>"><i class="bi bi-pie-chart me-1"></i>Consolidated</a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('reports.compliance')); ?>"><i class="bi bi-check2-square me-1"></i>Submission Compliance</a></li>
</ul>

<div class="card mb-4 border-0 shadow-sm" style="background:linear-gradient(135deg,#1d2f6f,#16244f);color:#eef1fb">
    <div class="card-body py-4">
        <h5 class="fw-bold mb-2"><i class="bi bi-clipboard2-data me-2"></i>Proposed Management and Public Reports for National Growth Decision-Making</h5>
        <p class="mb-1 opacity-75">
            These reports are generated from the datasets that Ministry departments, units and subordinate institutions
            submit regularly — the FCT and TBS data parameters, and the MIT &amp; Institutions data-requirements
            catalogue for the data warehouse. Processed data tells three stories:
            <strong>how efficiently institutions deliver their mandates</strong>, <strong>how fairly and safely markets operate</strong>,
            and <strong>how competitively Tanzanian products perform domestically and internationally</strong>.
        </p>
        <p class="mb-0 small opacity-75">
            <i class="bi bi-shield-check me-1"></i>Tier 1–2 and Tier 4 reports are computed from Ministry-approved data
            (accepted &amp; published); Tier 3 public reports use <strong>published</strong> data only.
        </p>
    </div>
</div>

<?php $__currentLoopData = $tiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tierNo => $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="d-flex align-items-center gap-2 mb-2 mt-4">
    <span class="badge text-bg-<?php echo e($tier['color'] === 'navy' ? 'navy' : ($tier['color'] === 'teal' ? 'info' : ($tier['color'] === 'gold' ? 'warning' : 'success'))); ?> px-3 py-2">
        <i class="bi <?php echo e($tier['icon']); ?> me-1"></i><?php echo e($tier['label']); ?>

    </span>
</div>
<p class="text-muted small mb-3"><?php echo e($tier['note']); ?></p>

<div class="row g-3 mb-2">
    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($report['tier'] !== $tierNo): ?> <?php continue; ?> <?php endif; ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm report-card">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge rounded-pill text-bg-light border fw-bold"><?php echo e($report['key']); ?></span>
                        <span class="badge <?php echo e($report['priority'] === 'High' ? 'text-bg-danger' : 'text-bg-secondary'); ?>"><?php echo e($report['priority']); ?> priority</span>
                    </div>
                    <h6 class="fw-bold"><?php echo e($report['title']); ?></h6>
                    <p class="text-muted small flex-grow-1"><?php echo e(\Illuminate\Support\Str::limit($report['summary'], 150)); ?></p>
                    <div class="small mb-3">
                        <div><i class="bi bi-people me-1 text-muted"></i><?php echo e($report['audience']); ?></div>
                        <div><i class="bi bi-calendar3 me-1 text-muted"></i><?php echo e($report['frequency']); ?></div>
                    </div>
                    <a href="<?php echo e(route('reports.thematic.show', $key)); ?>" class="btn btn-sm btn-outline-primary mt-auto">
                        <i class="bi bi-bar-chart-line me-1"></i>Open report
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<style>
.report-card { transition: transform .2s ease, box-shadow .2s ease; }
.report-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(20,30,60,.14) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/reports/thematic/index.blade.php ENDPATH**/ ?>