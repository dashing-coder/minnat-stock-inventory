<?php $__env->startSection('title',$supplier->exists?__('Edit Supplier'):__('Add Supplier')); ?> <?php $__env->startSection('heading',$supplier->exists?__('Edit Supplier'):__('Add Supplier')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($supplier->exists ? route('suppliers.update',$supplier) : route('suppliers.store')); ?>"><?php echo csrf_field(); ?> <?php if($supplier->exists): ?><?php echo method_field('PUT'); ?><?php endif; ?>
<div class="card mb-3"><div class="card-header"><?php echo e(__('Basic Information')); ?></div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required"><?php echo e(__('Supplier Name')); ?></label><input class="form-control" name="name" value="<?php echo e(old('name',$supplier->name)); ?>" required></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Group')); ?></label><select class="form-select" name="supplier_group_id"><option value=""><?php echo e(__('None')); ?></option><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>" <?php if(old('supplier_group_id',$supplier->supplier_group_id)==$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Company')); ?></label><input class="form-control" name="company_name" value="<?php echo e(old('company_name',$supplier->company_name)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Contact Person')); ?></label><input class="form-control" name="contact_person" value="<?php echo e(old('contact_person',$supplier->contact_person)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Phone')); ?></label><input class="form-control" name="phone" value="<?php echo e(old('phone',$supplier->phone)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Alternate Phone')); ?></label><input class="form-control" name="alternate_phone" value="<?php echo e(old('alternate_phone',$supplier->alternate_phone)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Email')); ?></label><input class="form-control" type="email" name="email" value="<?php echo e(old('email',$supplier->email)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Tax Number')); ?></label><input class="form-control" name="tax_number" value="<?php echo e(old('tax_number',$supplier->tax_number)); ?>"></div>
<div class="col-12"><label class="form-label"><?php echo e(__('Address')); ?></label><textarea class="form-control" name="address"><?php echo e(old('address',$supplier->address)); ?></textarea></div>
<div class="col-md-4"><label class="form-label"><?php echo e(__('City')); ?></label><input class="form-control" name="city" value="<?php echo e(old('city',$supplier->city)); ?>"></div>
<div class="col-md-4"><label class="form-label"><?php echo e(__('District')); ?></label><input class="form-control" name="district" value="<?php echo e(old('district',$supplier->district)); ?>"></div>
<div class="col-md-4"><label class="form-label"><?php echo e(__('Country')); ?></label><input class="form-control" name="country" value="<?php echo e(old('country',$supplier->country)); ?>"></div>
</div></div></div>
<div class="card mb-3"><div class="card-header"><?php echo e(__('Payment & Status')); ?></div><div class="card-body"><div class="row g-3">
<?php if(!$supplier->exists): ?>
<div class="col-md-4"><label class="form-label"><?php echo e(__('Opening Balance')); ?></label><input class="form-control" type="number" step=".01" min="0" name="opening_balance" value="<?php echo e(old('opening_balance',0)); ?>"></div>
<div class="col-md-4"><label class="form-label"><?php echo e(__('Opening Balance Type')); ?></label><select class="form-select" name="opening_balance_type"><option value="payable" <?php if(old('opening_balance_type')==='payable'): echo 'selected'; endif; ?>><?php echo e(__('Payable')); ?></option><option value="advance" <?php if(old('opening_balance_type')==='advance'): echo 'selected'; endif; ?>><?php echo e(__('Advance')); ?></option></select></div>
<?php endif; ?>
<div class="col-md-4"><label class="form-label"><?php echo e(__('Payment Terms (Days)')); ?></label><input class="form-control" type="number" min="0" name="payment_terms_days" value="<?php echo e(old('payment_terms_days',$supplier->payment_terms_days??0)); ?>"></div>
<div class="col-md-4"><label class="form-label"><?php echo e(__('Status')); ?></label><select class="form-select" name="is_active"><option value="1" <?php if(old('is_active',$supplier->exists?$supplier->is_active:1)==1): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option><option value="0" <?php if(old('is_active',$supplier->exists?$supplier->is_active:1)==0): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option></select></div>
<div class="col-12"><label class="form-label"><?php echo e(__('Notes')); ?></label><textarea class="form-control" name="notes"><?php echo e(old('notes',$supplier->notes)); ?></textarea></div>
</div></div></div>
<button class="btn btn-primary"><?php echo e(__('Save Supplier')); ?></button> <a class="btn btn-outline-secondary" href="<?php echo e(route('suppliers.index')); ?>"><?php echo e(__('Cancel')); ?></a>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/suppliers/form.blade.php ENDPATH**/ ?>