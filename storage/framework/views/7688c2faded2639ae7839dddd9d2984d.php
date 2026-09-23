<?php $__env->startSection('title', $dataset->exists ? 'Edit Dataset: ' . $dataset->code : 'New Dataset'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('datasets.index')); ?>">Data Catalogue</a></li>
    <li class="breadcrumb-item active"><?php echo e($dataset->exists ? 'Edit ' . $dataset->code : 'New dataset'); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
$defaultFields = $dataset->exists
    ? json_encode($dataset->fields, JSON_PRETTY_PRINT)
    : json_encode([
        ['name' => 'item_name', 'label' => 'Item name', 'type' => 'string', 'required' => true],
        ['name' => 'quantity', 'label' => 'Quantity', 'type' => 'number', 'required' => true],
        ['name' => 'unit', 'label' => 'Unit', 'type' => 'string', 'required' => false],
        ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'text', 'required' => false],
    ], JSON_PRETTY_PRINT);
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-text me-2"></i><?php echo e($dataset->exists ? 'Update catalogue entry' : 'Register a new dataset in the data catalogue'); ?></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e($dataset->exists ? route('datasets.update', $dataset) : route('datasets.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if($dataset->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Dataset Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="<?php echo e(old('code', $dataset->code)); ?>" required maxlength="30" placeholder="e.g. IND-PROD-01">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Dataset Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $dataset->name)); ?>" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo e(old('description', $dataset->description)); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Institution <span class="text-danger">*</span></label>
                            <?php if($lockedInstitution): ?>
                                <input type="hidden" name="institution_id" value="<?php echo e(auth()->user()->institution_id); ?>">
                                <input type="text" class="form-control" value="<?php echo e($institutions->first()->name ?? auth()->user()->institution->name); ?>" disabled>
                                <div class="form-text">Institution administrators can only configure the catalogue of their own institution.</div>
                            <?php else: ?>
                                <select name="institution_id" class="form-select" required>
                                    <option value="">— Select institution —</option>
                                    <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($institution->id); ?>" <?php if((string) old('institution_id', $dataset->institution_id) === (string) $institution->id): echo 'selected'; endif; ?>><?php echo e($institution->code); ?> — <?php echo e($institution->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" class="form-select" required>
                                <?php $__currentLoopData = ['Weekly', 'Monthly', 'Quarterly', 'Annually']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(strtolower($freq)); ?>" <?php if(old('frequency', $dataset->frequency) === strtolower($freq)): echo 'selected'; endif; ?>><?php echo e($freq); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <?php $__currentLoopData = ['low', 'medium', 'high']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($prio); ?>" <?php if(old('priority', $dataset->priority ?: 'medium') === $prio): echo 'selected'; endif; ?>><?php echo e(ucfirst($prio)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Source System</label>
                            <input type="text" name="source_system" class="form-control" value="<?php echo e(old('source_system', $dataset->source_system)); ?>" maxlength="255" placeholder="e.g. MIS, manual register">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Consumers</label>
                            <input type="text" name="consumers" class="form-control" value="<?php echo e(old('consumers', $dataset->consumers)); ?>" maxlength="255" placeholder="e.g. Ministry planning dept.">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Field Definitions (JSON) <span class="text-danger">*</span></label>
                            <textarea name="fields" class="form-control font-monospace" rows="10" required><?php echo e(old('fields', $defaultFields)); ?></textarea>
                            <div class="form-text">Array of fields. Each field: <code>name</code>, <code>label</code>, <code>type</code> (string, number, date, text, options), optional <code>required</code> and <code>options</code> (for type <code>options</code>).</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?php if(old('is_active', $dataset->exists ? $dataset->is_active : true)): echo 'checked'; endif; ?>>
                                <label class="form-check-label" for="isActive">Active — institution officers can prepare submissions against this dataset</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?php echo e($dataset->exists ? 'Update Dataset' : 'Register Dataset'); ?></button>
                        <a href="<?php echo e(route('datasets.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>About the Data Catalogue</div>
            <div class="card-body small text-muted">
                <p>The data catalogue defines what each institution must report. Institution officers use the pre-defined field definitions to prepare data for each reporting period.</p>
                <p class="mb-0">Field types: <code>string</code> (short text), <code>text</code> (long text), <code>number</code>, <code>date</code>, and <code>options</code> (a dropdown — provide an <code>options</code> array).</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/datasets/form.blade.php ENDPATH**/ ?>