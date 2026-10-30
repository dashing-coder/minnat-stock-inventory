@extends('layouts.app')
@section('title',__('Product Setup'))
@section('heading',__('Product Setup'))
@section('content')
<p class="text-muted">{{ __('Central setup for product masters, pricing/inventory configuration, variants and warehouse structure.') }}</p>
<div class="row g-3">
@php
$cards = [
 ['products.index','products.view','bi-box-seam','Products',$counts['products'],'Manage product master, pricing, barcode, stock rules and costing method.'],
 ['categories.index','categories.view','bi-tags','Product Category',$counts['categories'],'Manage product categories and parent-child category structure.'],
 ['brands.index','brands.view','bi-award','Brands',$counts['brands'],'Manage brands used by products.'],
 ['units.index','units.view','bi-rulers','Units',$counts['units'],'Manage product measurement units.'],
 ['warehouses.index','warehouses.view','bi-building','Warehouses',$counts['warehouses'],'Manage warehouse master and stock locations.'],
];
@endphp
@foreach($cards as $c) @can($c[1])<div class="col-md-6 col-xl-4"><a class="text-decoration-none" href="{{ route($c[0]) }}"><div class="card h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-start"><i class="bi {{ $c[2] }} fs-2"></i><span class="badge text-bg-light fs-6">{{ $c[4] }}</span></div><h5 class="mt-3 text-body">{{ __($c[3]) }}</h5><p class="text-muted mb-0">{{ __($c[5]) }}</p></div></div></a></div>@endcan @endforeach
@can('products.view')<div class="col-md-6 col-xl-4"><a class="text-decoration-none" href="{{ route('products.index') }}"><div class="card h-100"><div class="card-body"><div class="d-flex justify-content-between"><i class="bi bi-diagram-2 fs-2"></i><span class="badge text-bg-light fs-6">{{ $counts['variants'] }}</span></div><h5 class="mt-3 text-body">{{ __('Product Variants') }}</h5><p class="text-muted mb-0">{{ __('Open a product to manage size, color or other variant child products.') }}</p></div></div></a></div>@endcan
</div>
@endsection
