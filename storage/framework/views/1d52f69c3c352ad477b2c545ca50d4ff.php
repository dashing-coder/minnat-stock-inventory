<?php
    $editing = isset($saleReturn);
    $productOptions = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) $product->selling_price,
    ])->values()->all();

    $initialItems = old(
        'items',
        $editing
            ? $saleReturn->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ])->values()->all()
            : []
    );
?>
<?php $__env->startSection('title',$editing ? __('Edit Sales Return') : __('New Sales Return')); ?>
<?php $__env->startSection('heading',$editing ? __('Edit Sales Return') : __('New Sales Return')); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($editing ? route('sale-returns.update',$saleReturn) : route('sale-returns.store')); ?>" class="card card-body">
<?php echo csrf_field(); ?> <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<div class="row g-3"><div class="col-md-3"><label><?php echo e(__('Original Sale')); ?></label><select name="sale_id" class="form-select"><option value=""><?php echo e(__('Optional')); ?></option><?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(old('sale_id',$saleReturn->sale_id ?? '')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->invoice_no); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div><div class="col-md-3"><label><?php echo e(__('Customer')); ?></label><select name="customer_id" class="form-select"><option value=""><?php echo e(__('Walk-in')); ?></option><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(old('customer_id',$saleReturn->customer_id ?? '')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div><div class="col-md-3"><label><?php echo e(__('Warehouse')); ?></label><select name="warehouse_id" class="form-select"><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if(old('warehouse_id',$saleReturn->warehouse_id ?? '')==$w->id): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div><div class="col-md-3"><label><?php echo e(__('Date')); ?></label><input type="date" name="return_date" value="<?php echo e(old('return_date',isset($saleReturn)?$saleReturn->return_date->toDateString():now()->toDateString())); ?>" class="form-control"></div></div><hr>
<div class="table-responsive"><table class="table" id="rt"><thead><tr><th><?php echo e(__('Product')); ?></th><th><?php echo e(__('Qty')); ?></th><th><?php echo e(__('Unit Price')); ?></th><th></th></tr></thead><tbody></tbody></table></div><button type="button" id="add" class="btn btn-sm btn-outline-primary align-self-start"><?php echo e(__('Add Item')); ?></button>
<div class="row g-3 mt-2"><div class="col-md-3"><label><?php echo e(__('Tax')); ?></label><input type="number" step="0.01" min="0" name="tax" value="<?php echo e(old('tax',$saleReturn->tax ?? 0)); ?>" class="form-control"></div><div class="col"><label><?php echo e(__('Reason')); ?></label><input name="reason" value="<?php echo e(old('reason',$saleReturn->reason ?? '')); ?>" class="form-control"></div></div><div class="mt-3"><button class="btn btn-primary"><?php echo e($editing ? 'Update Draft' : 'Save Draft'); ?></button></div></form>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?><script>
const p=<?php echo json_encode($productOptions, 15, 512) ?>;
const initial=<?php echo json_encode($initialItems, 15, 512) ?>;
let i=0;function add(row={}){let tr=document.createElement('tr');tr.innerHTML=`<td><select class="form-select prod" name="items[${i}][product_id]" required><option value=""><?php echo e(__('Select')); ?></option>${p.map(x=>`<option value="${x.id}" ${String(x.id)===String(row.product_id||'')?'selected':''}>${x.name}</option>`).join('')}</select></td><td><input class="form-control" name="items[${i}][quantity]" type="number" step="0.0001" min="0.0001" value="${row.quantity??1}"></td><td><input class="form-control price" name="items[${i}][unit_price]" type="number" step="0.0001" min="0" value="${row.unit_price??0}"></td><td><button type="button" class="btn btn-outline-danger btn-sm">×</button></td>`;tr.querySelector('.prod').onchange=e=>{let x=p.find(y=>String(y.id)===e.target.value);if(x&&!row.unit_price)tr.querySelector('.price').value=x.price};tr.querySelector('button').onclick=()=>tr.remove();document.querySelector('#rt tbody').appendChild(tr);i++}document.getElementById('add').onclick=()=>add();(initial.length?initial:[{}]).forEach(add);
</script><?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\afrintraders\resources\views/sale_returns/form.blade.php ENDPATH**/ ?>