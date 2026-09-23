<?php $__env->startSection('title', 'Ministry Departments'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Ministry departments</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-diagram-3 me-2"></i>Ministry Departments</span>
        <a href="<?php echo e(route('departments.create')); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Department</a>
    </div>
    <div class="card-body">
        <p class="text-muted small">Ministry departments submit data through the same approval circle as institutions: a department data officer prepares submissions, the ministry reviewer reviews them, and the final approver approves.</p>
        <div class="table-responsive">
            <table id="departmentsTable" class="table table-hover align-middle w-100">
                <thead><tr><th>Code</th><th>Department</th><th>Users</th><th>Datasets</th><th>Status</th><th class="no-export">Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
vpDataTable('#departmentsTable', '<?php echo e(route('departments.datatable')); ?>', [
    { data: 'code' }, { data: 'name' }, { data: 'users' },
    { data: 'datasets' }, { data: 'status' }, { data: 'actions', orderable: false }
]);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/departments/index.blade.php ENDPATH**/ ?>