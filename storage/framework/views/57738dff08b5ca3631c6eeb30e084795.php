<?php $__env->startSection('title',$item->exists?__('Edit Unit'):__('Add Unit')); ?> <?php $__env->startSection('heading',$item->exists?__('Edit Unit'):__('Add Unit')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($item->exists ? route('units.update',$item) : route('units.store')); ?>"><?php echo csrf_field(); ?> <?php if($item->exists): ?><?php echo method_field('PUT'); ?><?php endif; ?>
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required"><?php echo e(__('Name')); ?></label><input class="form-control" name="name" value="<?php echo e(old('name',$item->name)); ?>" required></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Short Name')); ?></label><input class="form-control" name="short_name" value="<?php echo e(old('short_name',$item->short_name)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Allow Decimal Quantity')); ?></label><select class="form-select" name="allow_decimal"><option value="0" <?php if(old('allow_decimal',$item->allow_decimal??0)==0): echo 'selected'; endif; ?>><?php echo e(__('No')); ?></option><option value="1" <?php if(old('allow_decimal',$item->allow_decimal??0)==1): echo 'selected'; endif; ?>><?php echo e(__('Yes')); ?></option></select></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Status')); ?></label><select class="form-select" name="is_active"><option value="1" <?php if(old('is_active',$item->exists?$item->is_active:1)==1): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option><option value="0" <?php if(old('is_active',$item->exists?$item->is_active:1)==0): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary"><?php echo e(__('Save')); ?></button> <a class="btn btn-outline-secondary" href="<?php echo e(route('units.index')); ?>"><?php echo e(__('Cancel')); ?></a></div></div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/units/form.blade.php ENDPATH**/ ?>