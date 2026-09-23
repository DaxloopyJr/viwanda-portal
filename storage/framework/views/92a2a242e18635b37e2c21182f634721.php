<?php $__env->startSection('title', 'Institutions'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Institutions</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bank me-2"></i>Reporting Institutions</span>
        <a href="<?php echo e(route('institutions.create')); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Institution</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="institutionsTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Code</th><th>Institution</th><th>Contact</th><th>Integration</th>
                        <th>Datasets</th><th>Submissions</th><th>Status</th>
                        <th class="no-export" data-priority="1"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
vpDataTable('#institutionsTable', '<?php echo e(route('institutions.datatable')); ?>', [
    { data: 'code', name: 'code' },
    { data: 'name', name: 'name' },
    { data: 'contact', name: 'contact' },
    { data: 'integration', name: 'integration' },
    { data: 'datasets', name: 'datasets' },
    { data: 'submissions', name: 'submissions' },
    { data: 'status', name: 'status' },
    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
]);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/institutions/index.blade.php ENDPATH**/ ?>