<?php $__env->startSection('title',__('Stock Ledger')); ?> <?php $__env->startSection('heading',__('Stock Ledger')); ?>
<?php $__env->startSection('content'); ?>
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-3"><select class="form-select" name="product"><option value=""><?php echo e(__('All Products')); ?></option><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $x): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($x->id); ?>" <?php if(request('product')==$x->id): echo 'selected'; endif; ?>><?php echo e($x->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-2"><select class="form-select" name="warehouse"><option value=""><?php echo e(__('All Warehouses')); ?></option><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $x): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($x->id); ?>" <?php if(request('warehouse')==$x->id): echo 'selected'; endif; ?>><?php echo e($x->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-2"><input type="date" class="form-control" name="from_date" value="<?php echo e(request('from_date')); ?>"></div>
<div class="col-md-2"><input type="date" class="form-control" name="to_date" value="<?php echo e(request('to_date')); ?>"></div>
<div class="col-md-2"><input class="form-control" name="type" value="<?php echo e(request('type')); ?>" placeholder="<?php echo e(__('Movement type')); ?>"></div>
<div class="col"><button class="btn btn-outline-primary"><?php echo e(__('Filter')); ?></button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th><?php echo e(__('Date')); ?></th><th><?php echo e(__('Warehouse')); ?></th><th><?php echo e(__('Product')); ?></th><th><?php echo e(__('Reference')); ?></th><th><?php echo e(__('Type')); ?></th><th class="text-end"><?php echo e(__('IN')); ?></th><th class="text-end"><?php echo e(__('OUT')); ?></th><th class="text-end"><?php echo e(__('Balance')); ?></th></tr></thead><tbody>
<?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><?php echo e($m->transaction_date->format('Y-m-d')); ?></td><td><?php echo e($m->warehouse->name); ?></td><td><?php echo e($m->product->name); ?></td><td><?php echo e($m->reference_no); ?></td><td><?php echo e(__(ucwords(str_replace('_',' ',$m->movement_type)))); ?></td><td class="text-end"><?php echo e(number_format((float)$m->quantity_in,4)); ?></td><td class="text-end"><?php echo e(number_format((float)$m->quantity_out,4)); ?></td><td class="text-end"><?php echo e(number_format((float)$m->balance_after,4)); ?></td></tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="8"><?php echo e(__('No stock movements.')); ?></td></tr><?php endif; ?></tbody></table></div><?php echo e($movements->links()); ?>

</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/inventory/ledger.blade.php ENDPATH**/ ?>