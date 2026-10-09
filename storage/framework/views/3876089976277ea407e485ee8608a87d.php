<?php $__env->startSection('title',$item->exists?__('Edit Customer Group'):__('Add Customer Group')); ?> <?php $__env->startSection('heading',$item->exists?__('Edit Customer Group'):__('Add Customer Group')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($item->exists ? route('customer-groups.update',$item) : route('customer-groups.store')); ?>"><?php echo csrf_field(); ?> <?php if($item->exists): ?><?php echo method_field('PUT'); ?><?php endif; ?>
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required"><?php echo e(__('Name')); ?></label><input class="form-control" name="name" value="<?php echo e(old('name',$item->name)); ?>" required></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Code')); ?></label><input class="form-control" name="code" value="<?php echo e(old('code',$item->code)); ?>"></div>
<div class="col-12"><label class="form-label"><?php echo e(__('Description')); ?></label><textarea class="form-control" name="description"><?php echo e(old('description',$item->description)); ?></textarea></div>
<div class="col-md-4"><label class="form-label"><?php echo e(__('Status')); ?></label><select class="form-select" name="is_active"><option value="1" <?php if(old('is_active',$item->exists?$item->is_active:1)==1): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option><option value="0" <?php if(old('is_active',$item->exists?$item->is_active:1)==0): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option></select></div>
</div></div><div class="card-footer bg-white"><button class="btn btn-primary"><?php echo e(__('Save')); ?></button> <a class="btn btn-outline-secondary" href="<?php echo e(route('customer-groups.index')); ?>"><?php echo e(__('Cancel')); ?></a></div></div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/customer_groups/form.blade.php ENDPATH**/ ?>