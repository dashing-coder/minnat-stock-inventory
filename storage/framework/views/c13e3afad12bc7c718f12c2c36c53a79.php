<?php ($editing = isset($purchaseReturn)); ?>
<?php $__env->startSection('title',$editing ? 'Edit Purchase Return' : 'New Purchase Return'); ?>
<?php $__env->startSection('heading',$editing ? 'Edit Purchase Return' : 'New Purchase Return'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($editing ? route('purchase-returns.update',$purchaseReturn) : route('purchase-returns.store')); ?>" class="card card-body">
<?php echo csrf_field(); ?> <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<div class="row g-3">
<div class="col-md-3"><label>Original Purchase</label><select name="purchase_id" class="form-select"><option value="">Optional</option><?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if(old('purchase_id',$purchaseReturn->purchase_id ?? '')==$p->id): echo 'selected'; endif; ?>><?php echo e($p->purchase_no); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-3"><label class="required">Supplier</label><select name="supplier_id" class="form-select" required><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(old('supplier_id',$purchaseReturn->supplier_id ?? '')==$s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-3"><label class="required">Warehouse</label><select name="warehouse_id" class="form-select" required><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if(old('warehouse_id',$purchaseReturn->warehouse_id ?? '')==$w->id): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="col-md-3"><label class="required">Date</label><input type="date" name="return_date" value="<?php echo e(old('return_date',isset($purchaseReturn)?$purchaseReturn->return_date->toDateString():now()->toDateString())); ?>" class="form-control" required></div>
</div><hr>
<table class="table" id="rt"><thead><tr><th>Product</th><th>Qty</th><th>Unit Cost</th><th></th></tr></thead><tbody></tbody></table>
<button type="button" id="add" class="btn btn-sm btn-outline-primary align-self-start">Add Item</button>
<div class="row g-3 mt-2"><div class="col-md-3"><label>Tax</label><input type="number" step="0.01" min="0" name="tax" value="<?php echo e(old('tax',$purchaseReturn->tax ?? 0)); ?>" class="form-control"></div><div class="col"><label>Reason</label><input name="reason" value="<?php echo e(old('reason',$purchaseReturn->reason ?? '')); ?>" class="form-control"></div></div>
<div class="mt-3"><button class="btn btn-primary"><?php echo e($editing ? 'Update Draft' : 'Save Draft'); ?></button></div>
</form>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
const products=<?php echo json_encode($products->map(fn($p)=>['id'=>$p->id, 'name'=>$p->name, 'price'=>(float)$p->purchase_price])->values()) ?>;
const initial=<?php echo json_encode(old('items', isset($purchaseReturn) ? $purchaseReturn->items->map(fn($x)=>['product_id'=>$x->product_id, 'quantity'=>(float)$x->quantity) ?>;
let i=0;
function add(row={}){let tr=document.createElement('tr');tr.innerHTML=`<td><select class="form-select prod" name="items[${i}][product_id]" required><option value="">Select</option>${products.map(p=>`<option value="${p.id}" ${String(p.id)===String(row.product_id||'')?'selected':''}>${p.name}</option>`).join('')}</select></td><td><input class="form-control" name="items[${i}][quantity]" type="number" step="0.0001" min="0.0001" value="${row.quantity??1}" required></td><td><input class="form-control cost" name="items[${i}][unit_cost]" type="number" step="0.0001" min="0" value="${row.unit_cost??0}" required></td><td><button type="button" class="btn btn-outline-danger btn-sm">×</button></td>`;tr.querySelector('.prod').onchange=e=>{let p=products.find(x=>String(x.id)===e.target.value);if(p&&!row.unit_cost)tr.querySelector('.cost').value=p.price};tr.querySelector('button').onclick=()=>tr.remove();document.querySelector('#rt tbody').appendChild(tr);i++}
document.getElementById('add').onclick=()=>add();(initial.length?initial:[{}]).forEach(add);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home3/intrigueit/erp.intrigueit.co/resources/views/purchase_returns/form.blade.php ENDPATH**/ ?>