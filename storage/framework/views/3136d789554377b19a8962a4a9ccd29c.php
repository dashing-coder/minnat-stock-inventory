<?php $__env->startSection('title', __('New Supplier Payment')); ?>
<?php $__env->startSection('heading', __('Pay Supplier')); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('supplier-payments.store')); ?>" class="card card-body">
    <?php echo csrf_field(); ?>

    <div class="row g-3">
        <div class="col-md-4">
            <label><?php echo e(__('Supplier')); ?></label>
            <select name="supplier_id" class="form-select" required>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->id); ?>" <?php if(old('supplier_id') == $supplier->id): echo 'selected'; endif; ?>>
                        <?php echo e($supplier->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-4">
            <label><?php echo e(__('Purchase (optional)')); ?></label>
            <select name="purchase_id" class="form-select">
                <option value=""><?php echo e(__('General / Advance')); ?></option>
                <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($purchase->id); ?>" <?php if(old('purchase_id') == $purchase->id): echo 'selected'; endif; ?>>
                        <?php echo e($purchase->purchase_no); ?> - <?php echo e(__('Due')); ?> <?php echo e(number_format($purchase->due_amount, 2)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-2">
            <label><?php echo e(__('Date')); ?></label>
            <input
                type="date"
                name="payment_date"
                value="<?php echo e(old('payment_date', now()->toDateString())); ?>"
                class="form-control"
                required
            >
        </div>

        <div class="col-md-2">
            <label><?php echo e(__('Amount')); ?></label>
            <input
                type="number"
                name="amount"
                value="<?php echo e(old('amount')); ?>"
                step="0.01"
                min="0.01"
                class="form-control"
                required
            >
        </div>

        <div class="col-md-3">
            <label><?php echo e(__('Method')); ?></label>
            <select name="method" class="form-select" required>
                <?php $__currentLoopData = ['cash', 'bank', 'cheque', 'card', 'mobile_banking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($method); ?>" <?php if(old('method', 'cash') === $method): echo 'selected'; endif; ?>>
                        <?php echo e(__(ucfirst(str_replace('_', ' ', $method)))); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-9">
            <label><?php echo e(__('Reference')); ?></label>
            <input name="reference" value="<?php echo e(old('reference')); ?>" class="form-control">
        </div>

        <div class="col-12">
            <label><?php echo e(__('Notes')); ?></label>
            <textarea name="notes" class="form-control"><?php echo e(old('notes')); ?></textarea>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-success"><?php echo e(__('Post Payment')); ?></button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/supplier_payments/form.blade.php ENDPATH**/ ?>