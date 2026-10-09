<?php $__env->startSection('title',__('Supplier Groups')); ?> <?php $__env->startSection('heading',__('Supplier Groups')); ?>
<?php $__env->startSection('actions'); ?><a class="btn btn-primary" href="<?php echo e(route('supplier-groups.create')); ?>"><?php echo e(__('Add New')); ?></a><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th><?php echo e(__('Code')); ?></th><th><?php echo e(__('Name')); ?></th><th><?php echo e(__('Status')); ?></th><th></th></tr></thead><tbody>
<?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><?php echo e($item->code); ?></td><td><?php echo e($item->name); ?></td><td><?php echo e($item->is_active  ? __('Active') : __('Inactive')); ?></td>
<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('supplier-groups.edit',$item)); ?>"><?php echo e(__('Edit')); ?></a>
<form method="POST" action="<?php echo e(route('supplier-groups.destroy',$item)); ?>" class="d-inline" onsubmit="return confirm('Delete this record?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><?php echo e(__('Delete')); ?></button></form></td>
</tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="4" class="text-muted"><?php echo e(__('No records.')); ?></td></tr><?php endif; ?></tbody></table></div><?php echo e($items->links()); ?></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/supplier_groups/index.blade.php ENDPATH**/ ?>