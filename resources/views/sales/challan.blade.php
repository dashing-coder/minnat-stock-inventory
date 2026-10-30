<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Delivery Challan') }} - {{ $challanNo }}</title>
    <style>
        *{box-sizing:border-box}html,body{margin:0;padding:0}body{background:#eef2f6;color:#172033;font-family:"Noto Sans Bengali","SolaimanLipi","Kalpurush",Arial,sans-serif;font-size:12px}.toolbar{width:210mm;margin:14px auto 8px;display:flex;justify-content:space-between;gap:8px}.toolbar>div{display:flex;gap:8px}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:7px 13px;border:1px solid #cbd5e1;border-radius:7px;background:#fff;color:#172033;text-decoration:none;font-weight:700;cursor:pointer}.btn-primary{background:#163e79;border-color:#163e79;color:#fff}.btn.active{background:#16884a;border-color:#16884a;color:#fff}.page{position:relative;width:210mm;min-height:297mm;margin:0 auto 24px;padding:12mm;background:#fff;border:1px solid #dce3eb;box-shadow:0 14px 40px rgba(15,23,42,.14)}.header{display:grid;grid-template-columns:24mm 1fr 42mm;gap:5mm;align-items:center;border-bottom:3px solid #173e78;padding-bottom:5mm}.logo{width:20mm;height:20mm;border-radius:5mm 2mm 5mm 2mm;background:linear-gradient(145deg,#14799a,#173e78);color:#fff;font-size:22px;font-weight:900;display:flex;align-items:center;justify-content:center}.company{text-align:center}.company h1{margin:0;color:#173e78;font-size:27px;line-height:1.1}.company .owner{margin-top:3px;font-weight:700;color:#475569}.doc-title{text-align:right}.doc-title strong{display:block;color:#b42318;font-size:18px}.contact{text-align:center;line-height:1.55;margin:4mm 0 5mm;color:#475569}.meta{display:grid;grid-template-columns:1fr 1fr;gap:3mm 7mm;margin-bottom:5mm}.field{display:grid;grid-template-columns:31mm 1fr;border:1px solid #cbd5e1;min-height:9mm}.field b{padding:2.3mm;background:#f1f5f9;border-right:1px solid #cbd5e1}.field span{padding:2.3mm;word-break:break-word}.full{grid-column:1/-1}.table{width:100%;border-collapse:collapse;table-layout:fixed}.table th,.table td{border:1px solid #94a3b8;padding:2.5mm 1.8mm}.table th{background:#173e78;color:#fff;text-align:center}.center{text-align:center}.right{text-align:right;font-variant-numeric:tabular-nums}.notes{margin-top:5mm;padding:3mm;border:1px solid #cbd5e1;min-height:20mm}.signatures{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14mm;margin-top:24mm;text-align:center}.signature{padding-top:2mm;border-top:1px solid #64748b;font-weight:700}.footer{margin-top:10mm;text-align:center;color:#64748b;font-size:10px}.draft{position:absolute;top:48%;left:50%;transform:translate(-50%,-50%) rotate(-20deg);font-size:70px;font-weight:900;color:rgba(185,28,28,.08);letter-spacing:8px}.status{display:inline-block;margin-top:2mm;padding:1mm 3mm;border-radius:999px;background:#e2e8f0;font-size:10px;font-weight:800}@page{size:A4 portrait;margin:0}@media print{body{background:#fff}.toolbar{display:none!important}.page{margin:0;border:0;box-shadow:none;width:210mm;min-height:297mm;-webkit-print-color-adjust:exact;print-color-adjust:exact}}@media screen and (max-width:900px){.toolbar{width:calc(100% - 20px)}body{overflow-x:auto}}
    </style>
</head>
<body>
@php
    $bn = app()->getLocale() === 'bn';
    $companyName = $bn ? ($settings['company_name_bn'] ?? 'মেসার্স আফরিন ট্রেডার্স') : ($settings['company_name'] ?? 'M/S Afrin Traders');
    $owner = $bn ? ($settings['company_owner_bn'] ?? '') : ($settings['company_owner'] ?? '');
    $address = $bn ? ($settings['company_address_bn'] ?? $settings['company_address'] ?? '') : ($settings['company_address'] ?? $settings['company_address_bn'] ?? '');
    $sellerCode = $sale->creator?->seller_code ?: ($sale->creator ? 'USR-' . str_pad((string) $sale->creator->id, 5, '0', STR_PAD_LEFT) : '—');
@endphp
<div class="toolbar">
    <div><a class="btn" href="{{ route('sales.show',$sale) }}">← {{ __('Back to Sale') }}</a><button class="btn btn-primary" type="button" onclick="window.print()">{{ __('Print Challan') }}</button></div>
    <div><a class="btn {{ app()->getLocale()==='en'?'active':'' }}" href="{{ route('language.switch','en') }}">EN</a><a class="btn {{ app()->getLocale()==='bn'?'active':'' }}" href="{{ route('language.switch','bn') }}">বাংলা</a></div>
</div>
<section class="page">
    @if(!$hasPostedDelivery)<div class="draft">{{ __('NOT DELIVERED') }}</div>@endif
    @if(!$hasPostedDelivery)<p style="border:2px solid #b45309;background:#fffbeb;padding:10px;text-align:center;font-weight:800">{{ __('ORDER / PICKING COPY — no posted physical delivery exists. NOT proof of dispatch or receipt.') }}</p>@else<p style="border:1px solid #15803d;background:#f0fdf4;padding:8px;font-weight:700">{{ __('Quantities below are from POSTED physical-delivery records only.') }}: {{ $postedDeliveries->pluck('delivery_no')->implode(', ') }}</p>@endif
    <header class="header">
        <div class="logo">AT</div>
        <div class="company"><h1>{{ $companyName }}</h1>@if($owner)<div class="owner">{{ __('Proprietor') }}: {{ $owner }}</div>@endif</div>
        <div class="doc-title"><strong>{{ __('Delivery Challan') }}</strong><span class="status">{{ $hasPostedDelivery ? __('Delivery Recorded') : __('Delivery Pending') }}</span></div>
    </header>
    <div class="contact">@if($address)<div>{{ $address }}</div>@endif @if($settings['company_phone'] ?? null)<div>{{ __('Mobile') }}: {{ $settings['company_phone'] }}</div>@endif</div>
    <div class="meta">
        <div class="field"><b>{{ __('Challan No.') }}</b><span>{{ $challanNo }}</span></div>
        <div class="field"><b>{{ __('Invoice Date') }}</b><span>{{ $sale->sale_date?->format('Y-m-d') ?? '—' }}</span></div>
        <div class="field"><b>{{ __('Invoice Ref.') }}</b><span>{{ $sale->invoice_no }}</span></div>
        @if($hasPostedDelivery)<div class="field full"><b>{{ __('Posted Delivery Dates') }}</b><span>{{ $postedDeliveries->pluck('delivery_date')->map(fn($date) => $date?->format('Y-m-d'))->filter()->unique()->implode(', ') }}</span></div>@endif
        <div class="field"><b>{{ __('Warehouse') }}</b><span>{{ $sale->warehouse?->name ?? '—' }}</span></div>
        <div class="field full"><b>{{ __('Customer') }}</b><span>{{ $sale->customer?->name ?? __('Walk-in Customer') }}</span></div>
        <div class="field full"><b>{{ __('Delivery Address') }}</b><span>{{ collect([$sale->customer?->address,$sale->customer?->city,$sale->customer?->district])->filter()->implode(', ') ?: '—' }}</span></div>
        <div class="field"><b>{{ __('Mobile') }}</b><span>{{ $sale->customer?->phone ?? '—' }}</span></div>
        <div class="field"><b>{{ __('Seller ID') }}</b><span>{{ $sellerCode }}</span></div>
    </div>
    <table class="table">
        <colgroup><col style="width:7%"><col style="width:40%"><col style="width:13%"><col style="width:14%"><col style="width:11%"><col style="width:15%"></colgroup>
        <thead><tr><th>{{ __('SL') }}</th><th>{{ __('Product Description') }}</th><th>{{ __('Code') }}</th><th>{{ __('Bag / Bundle') }}</th><th>{{ __('Unit') }}</th><th>{{ __('Quantity') }}</th></tr></thead>
        <tbody>
            @foreach($sale->items as $item)
                @php $printedQty = $hasPostedDelivery ? (float)($deliveryTotals[$item->id] ?? 0) : (float)$item->quantity + (float)($item->free_quantity ?? 0); @endphp
                @if($printedQty > 0)
                <tr><td class="center">{{ $loop->iteration }}</td><td>{{ $item->product?->display_name ?? __('Deleted product') }}</td><td class="center">{{ $item->product?->code ?? '—' }}</td><td class="center">{{ $item->bundle_quantity !== null ? rtrim(rtrim(number_format((float)$item->bundle_quantity,4,'.',''),'0'),'.') : '—' }}</td><td class="center">{{ $item->product?->unit?->short_name ?? $item->product?->unit?->name ?? '—' }}</td><td class="right">{{ rtrim(rtrim(number_format($printedQty,4,'.',''),'0'),'.') }}</td></tr>
                @endif
            @endforeach
            @for($i=$sale->items->count();$i<10;$i++)<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>@endfor
        </tbody>
    </table>
    <div class="notes"><strong>{{ __('Notes') }}:</strong> {{ $sale->notes ?: '—' }}</div>
    <div class="signatures"><div class="signature">{{ __('Prepared By') }}</div><div class="signature">{{ $hasPostedDelivery ? __('Received By') : __('To Be Received By') }}</div><div class="signature">{{ __('Authorized Signature') }}</div></div>
    <div class="footer">{{ $hasPostedDelivery ? __('Actual posted delivery quantities only. Refer to individual delivery notes for date, driver and receiver details. Financial amounts are on the sales invoice.') : __('No delivery has been recorded. This is an order copy for preparation only and is not proof of delivery.') }}</div>
</section>
</body>
</html>
