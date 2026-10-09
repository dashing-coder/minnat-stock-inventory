<?php $__env->startSection('title', __('Seller Accounts')); ?>
<?php $__env->startSection('heading', __('Seller Accounts')); ?>
<?php $__env->startSection('subheading', __('Admin view of seller daily sales, cash and outstanding amounts.')); ?>

<?php $__env->startSection('content'); ?>
<?php ($currency = '৳'); ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h6 mb-0"><?php echo e(__('Seller Accounts')); ?></h2>
            <div class="small text-muted"><?php echo e(__('Today')); ?>: <?php echo e(\Carbon\Carbon::parse($today)->format('d M Y')); ?></div>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.create')): ?>
            <a class="btn btn-primary btn-sm" href="<?php echo e(route('users.create')); ?>"><?php echo e(__('Add Seller')); ?></a>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th><?php echo e(__('Seller ID')); ?></th>
                    <th><?php echo e(__('Seller')); ?></th>
                    <th class="text-end"><?php echo e(__('Today Sales')); ?></th>
                    <th class="text-end"><?php echo e(__('Today Cash')); ?></th>
                    <th class="text-end"><?php echo e(__('Today Due')); ?></th>
                    <th class="text-end"><?php echo e(__('Outstanding Due')); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $sellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><span class="badge text-bg-light"><?php echo e($seller->seller_code ?? ('SEL-' . str_pad($seller->id, 5, '0', STR_PAD_LEFT))); ?></span></td>
                    <td>
                        <div class="fw-semibold"><?php echo e($seller->name); ?></div>
                        <div class="small text-muted"><?php echo e($seller->username); ?></div>
                    </td>
                    <td class="text-end"><?php echo e($currency); ?> <?php echo e(number_format($seller->daily_totals['sales'], 2)); ?></td>
                    <td class="text-end"><?php echo e($currency); ?> <?php echo e(number_format($seller->daily_totals['cash'], 2)); ?></td>
                    <td class="text-end"><?php echo e($currency); ?> <?php echo e(number_format($seller->daily_totals['due'], 2)); ?></td>
                    <td class="text-end fw-semibold"><?php echo e($currency); ?> <?php echo e(number_format($seller->daily_totals['outstanding_due'], 2)); ?></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('seller-accounts.show', $seller)); ?>"><?php echo e(__('View Account')); ?></a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center text-muted py-5"><?php echo e(__('No Seller accounts have been created yet.')); ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/seller_accounts/index.blade.php ENDPATH**/ ?>