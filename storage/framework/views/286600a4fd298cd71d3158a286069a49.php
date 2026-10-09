<?php $__env->startSection('title',$item->exists?__('Edit Warehouse'):__('Add Warehouse')); ?> <?php $__env->startSection('heading',$item->exists?__('Edit Warehouse'):__('Add Warehouse')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($item->exists ? route('warehouses.update',$item) : route('warehouses.store')); ?>"><?php echo csrf_field(); ?> <?php if($item->exists): ?><?php echo method_field('PUT'); ?><?php endif; ?>
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required"><?php echo e(__('Code')); ?></label><input class="form-control" name="code" value="<?php echo e(old('code',$item->code)); ?>" required></div>
<div class="col-md-6"><label class="form-label required"><?php echo e(__('Name')); ?></label><input class="form-control" name="name" value="<?php echo e(old('name',$item->name)); ?>" required></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Phone')); ?></label><input class="form-control" name="phone" value="<?php echo e(old('phone',$item->phone)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Email')); ?></label><input class="form-control" type="email" name="email" value="<?php echo e(old('email',$item->email)); ?>"></div>
<div class="col-12"><label class="form-label"><?php echo e(__('Address')); ?></label><textarea class="form-control" name="address"><?php echo e(old('address',$item->address)); ?></textarea></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Default')); ?></label><select class="form-select" name="is_default"><option value="0" <?php if(old('is_default',$item->is_default??0)==0): echo 'selected'; endif; ?>><?php echo e(__('No')); ?></option><option value="1" <?php if(old('is_default',$item->is_default??0)==1): echo 'selected'; endif; ?>><?php echo e(__('Yes')); ?></option></select></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Status')); ?></label><select class="form-select" name="is_active"><option value="1" <?php if(old('is_active',$item->exists?$item->is_active:1)==1): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option><option value="0" <?php if(old('is_active',$item->exists?$item->is_active:1)==0): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary"><?php echo e(__('Save')); ?></button> <a class="btn btn-outline-secondary" href="<?php echo e(route('warehouses.index')); ?>"><?php echo e(__('Cancel')); ?></a></div></div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/warehouses/form.blade.php ENDPATH**/ ?>