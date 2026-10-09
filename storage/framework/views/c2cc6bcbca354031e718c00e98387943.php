<?php $__env->startSection('title', __('Receive Customer Payment')); ?>
<?php $__env->startSection('heading', __('Receive Customer Payment')); ?>

<?php $__env->startSection('content'); ?>
<?php ($isSeller = auth()->user()->hasRole('Seller')); ?>

<?php if($isSeller): ?>
    <div class="alert alert-info border-0 shadow-sm">
        <strong><?php echo e(__('Seller Receipt')); ?></strong> — <?php echo e(__('You can only receive payment against your own posted sales for today.')); ?>

    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('customer-payments.store')); ?>" class="card card-body">
    <?php echo csrf_field(); ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required"><?php echo e(__('Customer')); ?></label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($customer->id); ?>" <?php if(old('customer_id') == $customer->id): echo 'selected'; endif; ?>><?php echo e($customer->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label <?php echo e($isSeller ? 'required' : ''); ?>"><?php echo e($isSeller ? __('Sales Reference') : __('Invoice (optional)')); ?></label>
            <select name="sale_id" id="sale_id" class="form-select" <?php echo e($isSeller ? 'required' : ''); ?>>
                <?php if (! ($isSeller)): ?>
                    <option value=""><?php echo e(__('General Receipt / Advance')); ?></option>
                <?php endif; ?>
                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($sale->id); ?>" data-customer-id="<?php echo e($sale->customer_id); ?>" data-due="<?php echo e(number_format((float) $sale->due_amount, 2, '.', '')); ?>" <?php if(old('sale_id') == $sale->id): echo 'selected'; endif; ?>>
                        <?php echo e($sale->invoice_no); ?> — <?php echo e(__('Due')); ?> <?php echo e(number_format($sale->due_amount, 2)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>


        <div class="col-md-3">
            <label class="form-label required"><?php echo e(__('Date')); ?></label>
            <input type="date" name="payment_date" value="<?php echo e(old('payment_date', now()->toDateString())); ?>" class="form-control" <?php if($isSeller): echo 'readonly'; endif; ?> required>
        </div>

        <div class="col-md-3">
            <label class="form-label required"><?php echo e(__('Amount')); ?></label>
            <input type="number" name="amount" id="payment_amount" value="<?php echo e(old('amount')); ?>" step="0.01" min="0.01" class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <div id="amount_due_hint" class="form-text d-none"></div>
            <div id="amount_due_warning" class="alert alert-warning py-2 px-3 mt-2 mb-0 d-none" role="alert"></div>
            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="col-md-3">
            <label class="form-label required"><?php echo e(__('Method')); ?></label>
            <select name="method" class="form-select" required>
                <?php $__currentLoopData = ['cash','bank','cheque','card','mobile_banking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($method); ?>" <?php if(old('method', 'cash') === $method): echo 'selected'; endif; ?>><?php echo e(__(ucfirst(str_replace('_', ' ', $method)))); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('Reference')); ?></label>
            <input name="reference" value="<?php echo e(old('reference')); ?>" class="form-control">
        </div>

        <div class="col-12">
            <label class="form-label"><?php echo e(__('Notes')); ?></label>
            <textarea name="notes" class="form-control"><?php echo e(old('notes')); ?></textarea>
        </div>
    </div>

    <div class="mt-3"><button id="post_receipt_button" class="btn btn-success"><?php echo e(__('Post Receipt')); ?></button></div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerSelect = document.getElementById('customer_id');
    const saleSelect = document.getElementById('sale_id');
    const amountInput = document.getElementById('payment_amount');
    const dueHint = document.getElementById('amount_due_hint');
    const warning = document.getElementById('amount_due_warning');
    const submitButton = document.getElementById('post_receipt_button');

    if (!saleSelect || !amountInput || !warning || !submitButton) {
        return;
    }

    const dueLabel = <?php echo json_encode(__('Outstanding due: :amount', ['amount' => '__AMOUNT__']), 512) ?>;
    const warningTemplate = <?php echo json_encode(__('Payment amount cannot exceed the outstanding balance. Outstanding due: :amount', ['amount' => '__AMOUNT__']), 512) ?>;

    function selectedSaleOption() {
        const option = saleSelect.options[saleSelect.selectedIndex];
        return option && option.value ? option : null;
    }

    function money(value) {
        return Number(value || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function syncCustomerFromSale() {
        const option = selectedSaleOption();
        if (!option || !customerSelect) {
            return;
        }

        const customerId = option.dataset.customerId;
        if (customerId && customerSelect.value !== customerId) {
            customerSelect.value = customerId;
        }
    }

    function validateOutstandingBalance() {
        const option = selectedSaleOption();
        const amount = parseFloat(amountInput.value || '0');

        amountInput.removeAttribute('max');
        dueHint.classList.add('d-none');
        warning.classList.add('d-none');
        amountInput.classList.remove('is-invalid');
        submitButton.disabled = false;

        if (!option) {
            return true;
        }

        const due = parseFloat(option.dataset.due || '0');
        const dueText = money(due);

        amountInput.max = due.toFixed(2);
        dueHint.textContent = dueLabel.replace('__AMOUNT__', dueText);
        dueHint.classList.remove('d-none');

        if (amount > due + 0.000001) {
            warning.textContent = warningTemplate.replace('__AMOUNT__', dueText);
            warning.classList.remove('d-none');
            amountInput.classList.add('is-invalid');
            submitButton.disabled = true;
            return false;
        }

        return true;
    }

    saleSelect.addEventListener('change', function () {
        syncCustomerFromSale();
        validateOutstandingBalance();
    });

    amountInput.addEventListener('input', validateOutstandingBalance);
    amountInput.addEventListener('change', validateOutstandingBalance);

    const form = amountInput.closest('form');
    if (form) {
        form.addEventListener('submit', function (event) {
            if (!validateOutstandingBalance()) {
                event.preventDefault();
                amountInput.focus();
            }
        });
    }

    syncCustomerFromSale();
    validateOutstandingBalance();
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/customer_payments/form.blade.php ENDPATH**/ ?>