<?php $__env->startSection('title', $dataset->exists ? 'Edit Dataset' : 'New Dataset'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($dataset->exists ? route('datasets.update', $dataset) : route('datasets.store')); ?>">
    <?php echo csrf_field(); ?>
    <?php if($dataset->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
    <div class="card"><div class="card-body row g-3">
        <div class="col-md-3"><label class="form-label">Code *</label><input name="code" class="form-control" value="<?php echo e(old('code', $dataset->code)); ?>" required></div>
        <div class="col-md-5"><label class="form-label">Name *</label><input name="name" class="form-control" value="<?php echo e(old('name', $dataset->name)); ?>" required></div>
        <div class="col-md-4"><label class="form-label">Institution *</label>
            <select name="institution_id" class="form-select" required>
                <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($inst->id); ?>" <?php if(old('institution_id', $dataset->institution_id) == $inst->id): echo 'selected'; endif; ?>><?php echo e($inst->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"><?php echo e(old('description', $dataset->description)); ?></textarea></div>
        <div class="col-md-3"><label class="form-label">Frequency *</label>
            <select name="frequency" class="form-select"><?php $__currentLoopData = ['Weekly','Monthly','Quarterly','Annually']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option <?php if(old('frequency', $dataset->frequency) === $f): echo 'selected'; endif; ?>><?php echo e($f); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
        </div>
        <div class="col-md-3"><label class="form-label">Priority *</label>
            <select name="priority" class="form-select"><?php $__currentLoopData = ['low','medium','high']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p); ?>" <?php if(old('priority', $dataset->priority) === $p): echo 'selected'; endif; ?>><?php echo e(ucfirst($p)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
        </div>
        <div class="col-md-3"><label class="form-label">Source system</label><input name="source_system" class="form-control" value="<?php echo e(old('source_system', $dataset->source_system)); ?>"></div>
        <div class="col-md-3"><label class="form-label">Consumers</label><input name="consumers" class="form-control" value="<?php echo e(old('consumers', $dataset->consumers)); ?>"></div>
        <div class="col-12">
            <label class="form-label">Data dictionary fields (JSON) *</label>
            <textarea name="fields" class="form-control font-monospace" rows="8" required><?php echo e(old('fields', json_encode($dataset->fields ?? [['name'=>'','label'=>'','type'=>'string','required'=>true]], JSON_PRETTY_PRINT))); ?></textarea>
            <div class="form-text">Array of fields: name, label, type (string/date/integer/number/text), required (bool), options (array, optional).</div>
        </div>
        <div class="col-12 form-check ms-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" <?php if(old('is_active', $dataset->is_active ?? true)): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="active">Active</label>
        </div>
    </div>
    <div class="card-footer text-end"><button class="btn btn-success"><i class="bi bi-save"></i> Save dataset</button></div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/datasets/form.blade.php ENDPATH**/ ?>