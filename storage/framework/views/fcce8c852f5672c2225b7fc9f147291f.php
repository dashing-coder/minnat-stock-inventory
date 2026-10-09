<?php $__env->startSection('title', __('Generate Payroll')); ?>
<?php $__env->startSection('heading', __('Generate Payroll')); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('payrolls.store')); ?>" class="card card-body" id="payroll-generate-form">
    <?php echo csrf_field(); ?>

    <div class="row g-3">
        <div class="col-md-4">
            <label for="payroll-period" class="form-label"><?php echo e(__('Period')); ?></label>
            <input
                id="payroll-period"
                type="month"
                name="period"
                value="<?php echo e(old('period', now()->format('Y-m'))); ?>"
                class="form-control <?php $__errorArgs = ['period'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                required
            >

            <div id="payroll-period-warning" class="invalid-feedback <?php if(!$errors->has('period')): ?> d-none <?php endif; ?>">
                <?php $__errorArgs = ['period'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="col-md-4">
            <label for="payroll-pay-date" class="form-label"><?php echo e(__('Pay Date')); ?></label>
            <input
                id="payroll-pay-date"
                type="date"
                name="pay_date"
                value="<?php echo e(old('pay_date')); ?>"
                class="form-control <?php $__errorArgs = ['pay_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            >
            <?php $__errorArgs = ['pay_date'];
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

        <div class="col-md-4">
            <label for="payroll-notes" class="form-label"><?php echo e(__('Notes')); ?></label>
            <input
                id="payroll-notes"
                name="notes"
                value="<?php echo e(old('notes')); ?>"
                class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            >
            <?php $__errorArgs = ['notes'];
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
    </div>

    <div class="mt-3">
        <button id="payroll-generate-button" class="btn btn-primary" type="submit">
            <?php echo e(__('Generate From Active Employees')); ?>

        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    const existingPeriods = <?php echo json_encode($existingPeriods ?? [], 15, 512) ?>;
    const periodInput = document.getElementById('payroll-period');
    const warning = document.getElementById('payroll-period-warning');
    const submitButton = document.getElementById('payroll-generate-button');
    const duplicateMessage = <?php echo json_encode(__('Payroll already exists for this period.'), 15, 512) ?>;

    if (!periodInput || !warning || !submitButton) {
        return;
    }

    const validatePeriod = () => {
        const isDuplicate = existingPeriods.includes(periodInput.value);

        if (isDuplicate) {
            periodInput.classList.add('is-invalid');
            warning.textContent = duplicateMessage;
            warning.classList.remove('d-none');
            submitButton.disabled = true;
            return;
        }

        periodInput.classList.remove('is-invalid');
        warning.classList.add('d-none');
        submitButton.disabled = false;
    };

    periodInput.addEventListener('change', validatePeriod);
    periodInput.addEventListener('input', validatePeriod);
    validatePeriod();
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/payrolls/form.blade.php ENDPATH**/ ?>