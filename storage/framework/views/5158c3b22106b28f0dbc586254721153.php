<?php $__env->startSection('title', 'Roles & Permissions'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Roles & Permissions</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-lock me-2"></i>Roles and their permissions</span>
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Role</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th data-priority="2"># Perms</th>
                        <th data-priority="2">Users</th>
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
vpDataTable('#rolesTable', '<?php echo e(route('roles.datatable')); ?>', [
    { data: 'name', name: 'name' },
    { data: 'permissions', name: 'permissions', orderable: false },
    { data: 'permissions_count', name: 'permissions_count' },
    { data: 'users_count', name: 'users_count' },
    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
]);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/roles/index.blade.php ENDPATH**/ ?>