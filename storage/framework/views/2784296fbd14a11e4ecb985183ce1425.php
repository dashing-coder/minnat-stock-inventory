<?php $__env->startSection('title',$role->exists?__('Edit Role'):__('Add Role')); ?> <?php $__env->startSection('heading',$role->exists?__('Edit Role'):__('Add Role')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($role->exists ? route('roles.update',$role) : route('roles.store')); ?>"><?php echo csrf_field(); ?> <?php if($role->exists): ?><?php echo method_field('PUT'); ?><?php endif; ?>
<div class="card mb-3"><div class="card-body"><label class="form-label required"><?php echo e(__('Role Name')); ?></label><input class="form-control" name="name" value="<?php echo e(old('name',$role->name)); ?>" required></div></div>
<?php ($selected=old('permissions',$role->exists?$role->permissions->pluck('name')->all():[])); ?>
<?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module=>$modulePermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="card mb-3"><div class="card-header fw-bold text-capitalize"><?php echo e(__(ucwords(str_replace('_',' ',$module)))); ?></div><div class="card-body"><div class="row">
<?php $__currentLoopData = $modulePermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-3 mb-2"><label class="form-check"><input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo e($permission->name); ?>" <?php if(in_array($permission->name,$selected)): echo 'checked'; endif; ?>><span class="form-check-label"><?php echo e(__(ucfirst(explode('.',$permission->name)[1] ?? $permission->name))); ?></span></label></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div></div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<button class="btn btn-primary"><?php echo e(__('Save Role')); ?></button> <a class="btn btn-outline-secondary" href="<?php echo e(route('roles.index')); ?>"><?php echo e(__('Cancel')); ?></a>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/roles/form.blade.php ENDPATH**/ ?>