<?php $__env->startSection('title',$item->exists?__('Edit Stock Adjustment'):__('New Stock Adjustment')); ?> <?php $__env->startSection('heading',$item->exists?__('Edit Stock Adjustment'):__('New Stock Adjustment')); ?>
<?php $__env->startSection('content'); ?>
<?php
    $rows = old(
        'items',
        $item->exists
            ? $item->items->map(fn ($row) => [
                'product_id' => $row->product_id,
                'quantity' => $row->quantity,
                'unit_cost' => $row->unit_cost,
                'remarks' => $row->remarks,
            ])->all()
            : [['product_id' => '', 'quantity' => '', 'unit_cost' => '', 'remarks' => '']]
    );

    $productOptions = $products->map(fn ($product) => [
        'id' => $product->id,
        'label' => $product->name.' ('.$product->sku.')',
    ])->values()->all();
?>
<form method="POST" action="<?php echo e($item->exists ? route('stock-adjustments.update',$item) : route('stock-adjustments.store')); ?>"><?php echo csrf_field(); ?> <?php if($item->exists): ?><?php echo method_field('PUT'); ?><?php endif; ?>
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label required"><?php echo e(__('Warehouse')); ?></label><select class="form-select" name="warehouse_id" required><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $x): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($x->id); ?>" <?php if(old('warehouse_id',$item->warehouse_id)==$x->id): echo 'selected'; endif; ?>><?php echo e($x->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-3"><label class="form-label required"><?php echo e(__('Date')); ?></label><input class="form-control" type="date" name="adjustment_date" value="<?php echo e(old('adjustment_date',$item->adjustment_date?->format('Y-m-d'))); ?>" required></div>
<div class="col-md-5"><label class="form-label required"><?php echo e(__('Type')); ?></label><select class="form-select" name="adjustment_type"><?php $__currentLoopData = ['opening','increase','found','decrease','damage','expired','lost','internal_use']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($type); ?>" <?php if(old('adjustment_type',$item->adjustment_type)===$type): echo 'selected'; endif; ?>><?php echo e(__(ucfirst(str_replace('_',' ',$type)))); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Reason')); ?></label><input class="form-control" name="reason" value="<?php echo e(old('reason',$item->reason)); ?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo e(__('Notes')); ?></label><input class="form-control" name="notes" value="<?php echo e(old('notes',$item->notes)); ?>"></div>
</div></div></div>

<div class="card mb-3"><div class="card-header d-flex justify-content-between"><span><?php echo e(__('Items')); ?></span><button type="button" class="btn btn-sm btn-outline-primary" id="add-row"><?php echo e(__('Add Row')); ?></button></div><div class="card-body">
<div class="table-responsive"><table class="table" id="items-table"><thead><tr><th style="min-width:260px"><?php echo e(__('Product')); ?></th><th><?php echo e(__('Quantity')); ?></th><th><?php echo e(__('Unit Cost')); ?></th><th><?php echo e(__('Remarks')); ?></th><th></th></tr></thead><tbody>
<?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr>
<td><select class="form-select" name="items[<?php echo e($i); ?>][product_id]" required><option value=""><?php echo e(__('Select Product')); ?></option><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if(($row['product_id']??'')==$p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?> (<?php echo e($p->sku); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
<td><input class="form-control" type="number" step=".0001" min=".0001" name="items[<?php echo e($i); ?>][quantity]" value="<?php echo e($row['quantity']??''); ?>" required></td>
<td><input class="form-control" type="number" step=".0001" min="0" name="items[<?php echo e($i); ?>][unit_cost]" value="<?php echo e($row['unit_cost']??''); ?>"></td>
<td><input class="form-control" name="items[<?php echo e($i); ?>][remarks]" value="<?php echo e($row['remarks']??''); ?>"></td>
<td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>
</tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody></table></div></div></div>
<button class="btn btn-primary"><?php echo e(__('Save Draft')); ?></button> <a class="btn btn-outline-secondary" href="<?php echo e(route('stock-adjustments.index')); ?>"><?php echo e(__('Cancel')); ?></a>
</form>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
const productOptions = <?php echo json_encode($productOptions, 15, 512) ?>;
let rowIndex = document.querySelectorAll('#items-table tbody tr').length;
document.getElementById('add-row').addEventListener('click',()=>{
  const opts='<option value=""><?php echo e(__('Select Product')); ?></option>'+productOptions.map(p=>`<option value="${p.id}">${p.label}</option>`).join('');
  document.querySelector('#items-table tbody').insertAdjacentHTML('beforeend',`<tr>
  <td><select class="form-select" name="items[${rowIndex}][product_id]" required>${opts}</select></td>
  <td><input class="form-control" type="number" step=".0001" min=".0001" name="items[${rowIndex}][quantity]" required></td>
  <td><input class="form-control" type="number" step=".0001" min="0" name="items[${rowIndex}][unit_cost]"></td>
  <td><input class="form-control" name="items[${rowIndex}][remarks]"></td>
  <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td></tr>`);
  rowIndex++;
});
document.addEventListener('click',e=>{if(e.target.classList.contains('remove-row') && document.querySelectorAll('#items-table tbody tr').length>1)e.target.closest('tr').remove();});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/stock_adjustments/form.blade.php ENDPATH**/ ?>