<?php $__env->startSection('title',__('Quotations')); ?> <?php $__env->startSection('heading',__('Quotations')); ?>
<?php $__env->startSection('actions'); ?><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quotations.create')): ?><a class="btn btn-primary" href="<?php echo e(route('quotations.create')); ?>"><?php echo e(__('New Quotation')); ?></a><?php endif; ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?><div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th><?php echo e(__('Date')); ?></th>
                    <th><?php echo e(__('No')); ?></th>
                    <th><?php echo e(__('Customer')); ?></th>
                    <th><?php echo e(__('Valid Until')); ?></th>
                    <th><?php echo e(__('Status')); ?></th>
                    <th class="money"><?php echo e(__('Total')); ?></th>
                </tr>
            </thead>
            <tbody><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr>
                    <td><?php echo e($r->quotation_date->format('Y-m-d')); ?></td>
                    <td><a href="<?php echo e(route('quotations.show',$r)); ?>"><?php echo e($r->quotation_no); ?></a></td>
                    <td><?php echo e($r->customer?->name ?? 'Walk-in'); ?></td>
                    <td><?php echo e($r->valid_until?->format('Y-m-d')); ?></td>
                    <td><?php echo e(__(ucfirst($r->status))); ?></td>
                    <td class="money"><?php echo e(number_format($r->grand_total,2)); ?></td>
                </tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr>
                    <td colspan="6"><?php echo e(__('No quotations.')); ?></td>
                </tr><?php endif; ?></tbody>
        </table>
    </div>
</div><?php echo e($rows->links()); ?><?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/quotations/index.blade.php ENDPATH**/ ?>