@extends('layouts.app')
@section('title',$product->exists?'Edit Product':'Add Product') @section('heading',$product->exists?'Edit Product':'Add Product')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('products.update',$product) : route('products.store') }}">
@csrf @if($product->exists)@method('PUT')@endif
<div class="card mb-3"><div class="card-header">Basic Information</div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label required">Product Name</label><input class="form-control" name="name" value="{{ old('name',$product->name) }}" required></div>
<div class="col-md-3"><label class="form-label required">SKU</label><input class="form-control" name="sku" value="{{ old('sku',$product->sku) }}" required></div>
<div class="col-md-3"><label class="form-label">Barcode</label><input class="form-control" name="barcode" value="{{ old('barcode',$product->barcode) }}"></div>
<div class="col-md-4"><label class="form-label">Category</label><select class="form-select" name="category_id"><option value="">None</option>@foreach($categories as $x)<option value="{{ $x->id }}" @selected(old('category_id',$product->category_id)==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Brand</label><select class="form-select" name="brand_id"><option value="">None</option>@foreach($brands as $x)<option value="{{ $x->id }}" @selected(old('brand_id',$product->brand_id)==$x->id)>{{ $x->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label required">Unit</label><select class="form-select" name="unit_id" required>@foreach($units as $x)<option value="{{ $x->id }}" @selected(old('unit_id',$product->unit_id)==$x->id)>{{ $x->name }} ({{ $x->short_name }})</option>@endforeach</select></div>
</div></div></div>

<div class="card mb-3"><div class="card-header">Pricing</div><div class="card-body"><div class="row g-3">
@foreach([
['purchase_price','Purchase Price'],['selling_price','Selling Price'],['wholesale_price','Wholesale Price'],['minimum_selling_price','Minimum Selling Price']
] as $f)
<div class="col-md-3"><label class="form-label {{ in_array($f[0],['purchase_price','selling_price'])?'required':'' }}">{{ $f[1] }}</label><input class="form-control" type="number" step=".01" min="0" name="{{ $f[0] }}" value="{{ old($f[0],$product->{$f[0]} ?? 0) }}" {{ in_array($f[0],['purchase_price','selling_price'])?'required':'' }}></div>
@endforeach
<div class="col-md-3"><label class="form-label">Tax Rate %</label><input class="form-control" type="number" step=".0001" min="0" max="100" name="tax_rate" value="{{ old('tax_rate',$product->tax_rate??0) }}"></div>
</div></div></div>

<div class="card mb-3"><div class="card-header">Inventory Configuration</div><div class="card-body"><div class="row g-3">
<div class="col-md-3"><label class="form-label">Reorder Level</label><input class="form-control" type="number" step=".0001" min="0" name="reorder_level" value="{{ old('reorder_level',$product->reorder_level??0) }}"></div>
<div class="col-md-3"><label class="form-label">Minimum Stock</label><input class="form-control" type="number" step=".0001" min="0" name="minimum_stock_level" value="{{ old('minimum_stock_level',$product->minimum_stock_level??0) }}"></div>
<div class="col-md-3"><label class="form-label">Maximum Stock</label><input class="form-control" type="number" step=".0001" min="0" name="maximum_stock_level" value="{{ old('maximum_stock_level',$product->maximum_stock_level) }}"></div>
<div class="col-md-3"><label class="form-label">Track Stock</label><select class="form-select" name="track_stock"><option value="1" @selected(old('track_stock',$product->exists?$product->track_stock:1)==1)>Yes</option><option value="0" @selected(old('track_stock',$product->exists?$product->track_stock:1)==0)>No</option></select></div>
<div class="col-md-3"><label class="form-label">Allow Negative Stock</label><select class="form-select" name="allow_negative_stock"><option value="0" @selected(old('allow_negative_stock',$product->allow_negative_stock??0)==0)>No</option><option value="1" @selected(old('allow_negative_stock',$product->allow_negative_stock??0)==1)>Yes</option></select></div>
<div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active',$product->exists?$product->is_active:1)==1)>Active</option><option value="0" @selected(old('is_active',$product->exists?$product->is_active:1)==0)>Inactive</option></select></div>
<div class="col-md-6"><label class="form-label">Image</label><input class="form-control" type="file" name="image" accept="image/*"></div>
<div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description">{{ old('description',$product->description) }}</textarea></div>
</div></div></div>
<button class="btn btn-primary">Save Product</button> <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Cancel</a>
</form>
@endsection
