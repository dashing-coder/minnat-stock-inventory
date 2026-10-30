<!doctype html>
<html lang="{{ app()->getLocale() }}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ __('Barcode Labels') }}</title>
<style>
*{box-sizing:border-box}body{font-family:Arial,sans-serif;margin:12px;color:#000}.toolbar{margin-bottom:14px;display:flex;gap:8px}.labels{display:flex;flex-wrap:wrap;align-items:flex-start}.label{width:{{ $labelWidth }}mm;height:{{ $labelHeight }}mm;border:1px dashed #bbb;padding:2mm;overflow:hidden;display:flex;flex-direction:column;justify-content:center;page-break-inside:avoid}.name{font-size:10px;font-weight:700;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.price{font-size:10px;text-align:center;font-weight:700}.barcode{height:15mm;width:100%}.barcode svg{width:100%;height:100%;display:block}@media print{body{margin:0}.toolbar{display:none}.label{border:0}}
</style></head><body>
<div class="toolbar"><button onclick="window.print()">{{ __('Print') }}</button><button onclick="window.close()">{{ __('Close') }}</button></div>
<div class="labels">
@foreach($labels as $label)
<div class="label">
@if($showName)<div class="name">{{ $label['product']->name }}</div>@endif
<div class="barcode">{!! $label['svg'] !!}</div>
@if($showPrice)<div class="price">{{ __('Price') }}: {{ number_format((float)$label['product']->selling_price,2) }}</div>@endif
</div>
@endforeach
</div>
</body></html>
