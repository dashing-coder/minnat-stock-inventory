<?php $__env->startSection('title',__('Roles')); ?> <?php $__env->startSection('heading',__('Roles & Permissions')); ?>
<?php $__env->startSection('actions'); ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.create')): ?><a class="btn btn-primary" href="<?php echo e(route('roles.create')); ?>"><?php echo e(__('Add Role')); ?></a><?php endif; ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table align-middle">
<thead><tr><th><?php echo e(__('Role')); ?></th><th><?php echo e(__('Permissions')); ?></th><th></th></tr></thead><tbody>
<?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($role->name); ?></td><td><?php echo e($role->permissions_count); ?></td><td class="text-end">
<?php if($role->name!=='Super Admin'): ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.edit')): ?><a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('roles.edit',$role)); ?>"><?php echo e(__('Edit')); ?></a><?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.delete')): ?><form class="d-inline" method="POST" action="<?php echo e(route('roles.destroy',$role)); ?>" onsubmit="return confirm('Delete role?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><?php echo e(__('Delete')); ?></button></form><?php endif; ?> <?php endif; ?>
</td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody></table></div><?php echo e($roles->links()); ?></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/roles/index.blade.php ENDPATH**/ ?>