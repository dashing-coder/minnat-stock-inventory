<?php $__env->startSection('title','Suppliers'); ?> <?php $__env->startSection('heading','Suppliers'); ?>
<?php $__env->startSection('actions'); ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('suppliers.create')): ?><a class="btn btn-primary" href="<?php echo e(route('suppliers.create')); ?>">Add Supplier</a><?php endif; ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card"><div class="card-body">
<form class="row g-2 mb-3">
<div class="col-md-4"><input class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Name, code, phone, company"></div>
<div class="col-md-3"><select class="form-select" name="group"><option value="">All Groups</option><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>" <?php if(request('group')==$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">All Status</option><option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Active</option><option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>>Inactive</option></select></div>
<div class="col"><button class="btn btn-outline-primary">Filter</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Code</th><th>Supplier</th><th>Group</th><th>Phone</th><th>Status</th><th></th></tr></thead><tbody>
<?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr><td><?php echo e($supplier->code); ?></td><td><a href="<?php echo e(route('suppliers.show',$supplier)); ?>"><?php echo e($supplier->name); ?></a><div class="small text-muted"><?php echo e($supplier->company_name); ?></div></td><td><?php echo e($supplier->group?->name); ?></td><td><?php echo e($supplier->phone); ?></td><td><?php echo e($supplier->is_active?'Active':'Inactive'); ?></td>
<td class="text-end"><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('suppliers.ledger')): ?><a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('suppliers.ledger',$supplier)); ?>">Ledger</a><?php endif; ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('suppliers.edit')): ?><a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('suppliers.edit',$supplier)); ?>">Edit</a><?php endif; ?></td></tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6">No suppliers.</td></tr><?php endif; ?></tbody></table></div><?php echo e($suppliers->links()); ?>

</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\erp-system\resources\views/suppliers/index.blade.php ENDPATH**/ ?>