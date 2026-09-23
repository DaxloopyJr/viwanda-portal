<?php $__env->startSection('title', 'Audit Trail'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Administration</a></li>
    <li class="breadcrumb-item active">Audit Trail</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header"><i class="bi bi-shield-check me-2"></i>System Audit Log</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="auditTable" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>When</th><th>User</th><th>Action</th><th>Subject</th><th>Details</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
vpDataTable('#auditTable', '<?php echo e(route('audit.datatable')); ?>', [
    { data: 'when', name: 'when', className: 'text-nowrap small text-muted' },
    { data: 'user', name: 'user' },
    { data: 'action', name: 'action' },
    { data: 'subject', name: 'subject', orderable: false },
    { data: 'details', name: 'details', orderable: false }
], { order: [[0, 'desc']] });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/audit/index.blade.php ENDPATH**/ ?>