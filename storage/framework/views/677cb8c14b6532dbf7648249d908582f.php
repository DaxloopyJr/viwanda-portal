<?php $__env->startSection('title', 'Data Catalogue'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="#">Data Catalogue</a></li>
    <li class="breadcrumb-item active">All datasets</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text me-2"></i>Registered Datasets</span>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['datasets.manage', 'datasets.manage-own'])): ?>
        <a href="<?php echo e(route('datasets.create')); ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Dataset</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datasetsTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Code</th><th>Dataset</th><th>Institution</th><th>Frequency</th>
                        <th>Priority</th><th>Fields</th><th>Submissions</th><th>Status</th>
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
vpDataTable('#datasetsTable', '<?php echo e(route('datasets.datatable')); ?>', [
    { data: 'code', name: 'code' },
    { data: 'name', name: 'name' },
    { data: 'institution', name: 'institution' },
    { data: 'frequency', name: 'frequency' },
    { data: 'priority', name: 'priority' },
    { data: 'fields', name: 'fields' },
    { data: 'submissions', name: 'submissions' },
    { data: 'status', name: 'status' },
    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
]);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\viwanda-portal\resources\views/datasets/index.blade.php ENDPATH**/ ?>