<?php $__env->startSection('title',__('Products')); ?> <?php $__env->startSection('heading',__('Products')); ?>
<?php $__env->startSection('actions'); ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.create')): ?><a class="btn btn-primary" href="<?php echo e(route('products.create')); ?>"><?php echo e(__('Add Product')); ?></a><?php endif; ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(__('Name, code, SKU, barcode')); ?>"></div>
<div class="col-md-2"><select class="form-select" name="category"><option value=""><?php echo e(__('Category')); ?></option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $x): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($x->id); ?>" <?php if(request('category')==$x->id): echo 'selected'; endif; ?>><?php echo e($x->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-2"><select class="form-select" name="brand"><option value=""><?php echo e(__('Brand')); ?></option><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $x): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($x->id); ?>" <?php if(request('brand')==$x->id): echo 'selected'; endif; ?>><?php echo e($x->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value=""><?php echo e(__('Status')); ?></option><option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option><option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option></select></div>
<div class="col"><button class="btn btn-outline-primary"><?php echo e(__('Filter')); ?></button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th><?php echo e(__('Code')); ?></th><th><?php echo e(__('SKU')); ?></th><th><?php echo e(__('Product')); ?></th><th><?php echo e(__('Category')); ?></th><th><?php echo e(__('Brand')); ?></th><th><?php echo e(__('Unit')); ?></th><th class="text-end"><?php echo e(__('Sell Price')); ?></th><th></th></tr></thead><tbody>
<?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr>
<td><?php echo e($product->code); ?></td><td><?php echo e($product->sku); ?></td><td><a href="<?php echo e(route('products.show',$product)); ?>"><?php echo e($product->name); ?></a></td>
<td><?php echo e($product->category?->name); ?></td><td><?php echo e($product->brand?->name); ?></td><td><?php echo e($product->unit?->short_name); ?></td><td class="text-end"><?php echo e(number_format((float)$product->selling_price,2)); ?></td>
<td class="text-end"><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.edit')): ?><a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('products.edit',$product)); ?>"><?php echo e(__('Edit')); ?></a><?php endif; ?></td>
</tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="8"><?php echo e(__('No products.')); ?></td></tr><?php endif; ?></tbody></table></div><?php echo e($products->links()); ?>

</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/products/index.blade.php ENDPATH**/ ?>