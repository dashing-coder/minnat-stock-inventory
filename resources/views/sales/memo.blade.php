<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Sales Memo') }} - {{ $sale->invoice_no }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background: #edf1f5;
            color: #1f2937;
            font-family: "Noto Sans Bengali", "SolaimanLipi", "Kalpurush", Arial, sans-serif;
            font-size: 12px;
        }
        .memo-toolbar {
            width: 210mm;
            margin: 14px auto 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 8px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 7px 13px;
            border-radius: 7px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #1f2937;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-primary { background: #1557a5; color: #fff; border-color: #1557a5; }
        .btn-lang.active { background: #159447; color: #fff; border-color: #159447; }
        .memo-page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            padding: 9mm 10mm 11mm;
            overflow: hidden;
            background:
                radial-gradient(circle at 9% 5%, rgba(33, 156, 85, .26), transparent 19%),
                radial-gradient(circle at 90% 8%, rgba(247, 210, 46, .55), transparent 25%),
                linear-gradient(180deg, #fff78b 0, #fff9d0 20%, #ffffff 42%, #fbfff0 78%, #f5df38 100%);
            box-shadow: 0 14px 40px rgba(15, 23, 42, .16);
            border: 1px solid #d7dfb1;
        }
        .memo-page::before,
        .memo-page::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            opacity: .22;
            pointer-events: none;
        }
        .memo-page::before { width: 170mm; height: 48mm; left: -70mm; top: -23mm; background: #25a65a; transform: rotate(-8deg); }
        .memo-page::after { width: 150mm; height: 45mm; right: -55mm; bottom: -23mm; background: #62bd3c; transform: rotate(8deg); }
        .memo-content { position: relative; z-index: 2; }
        .memo-header {
            display: grid;
            grid-template-columns: 25mm 1fr 31mm;
            gap: 4mm;
            align-items: center;
            text-align: center;
        }
        .brand-mark {
            width: 22mm;
            height: 22mm;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6mm 2mm 6mm 2mm;
            background: linear-gradient(145deg, #106d9e, #153f79);
            color: #fff;
            font-size: 23px;
            font-weight: 900;
            font-style: italic;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.25);
        }
        .company-name {
            margin: 0;
            color: #d92323;
            font-size: 29px;
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -.5px;
            text-shadow: 0 1px 0 #fff;
        }
        .owner-line {
            display: inline-block;
            margin-top: 5px;
            padding: 2px 14px 3px;
            border-radius: 999px;
            background: #1557a5;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }
        .brand-side {
            color: #1b3f70;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.35;
        }
        .business-description {
            max-width: 176mm;
            margin: 4mm auto 2mm;
            text-align: center;
            line-height: 1.45;
            font-weight: 700;
            color: #334155;
        }
        .contact-line {
            margin: 1.5mm auto 4mm;
            text-align: center;
            font-size: 12px;
            line-height: 1.5;
            font-weight: 800;
            color: #173d78;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 58mm;
            gap: 2.5mm 4mm;
            margin-bottom: 3mm;
        }
        .field-row { display: grid; grid-template-columns: 24mm 1fr; min-height: 9mm; }
        .field-label {
            padding: 2.1mm 2.5mm;
            background: linear-gradient(90deg, #7d279c, #244b9c);
            color: #fff;
            font-weight: 800;
            border: 1px solid #1f4f88;
        }
        .field-value {
            padding: 2mm 2.5mm;
            background: linear-gradient(180deg, #dff9ff, #fff 50%, #ffe0f0);
            border: 1px solid #50a7c9;
            min-width: 0;
            word-break: break-word;
        }
        .full-width { grid-column: 1 / -1; }
        .customer-contact { display: grid; grid-template-columns: 1fr 58mm; gap: 4mm; }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 2mm;
            background: rgba(255,255,255,.78);
        }
        .items-table th {
            padding: 2.6mm 1.5mm;
            background: #e92424;
            color: #fff84a;
            border: 1px solid #9f2222;
            font-weight: 900;
            text-align: center;
            line-height: 1.25;
        }
        .items-table td {
            padding: 2.4mm 1.6mm;
            border: 1px solid #79808b;
            vertical-align: top;
            height: 10mm;
            background: rgba(255,255,255,.66);
        }
        .items-table .center { text-align: center; }
        .items-table .right { text-align: right; font-variant-numeric: tabular-nums; }
        .items-table .desc { font-weight: 700; }
        .items-table .muted { color: #64748b; font-size: 10px; margin-top: 2px; }
        .memo-lower {
            display: grid;
            grid-template-columns: 1fr 61mm;
            gap: 4mm;
            align-items: start;
            margin-top: 3mm;
        }
        .notes-box {
            min-height: 46mm;
            padding: 3mm;
            border: 1px solid #7e8890;
            background: rgba(255,255,255,.58);
        }
        .notes-title { font-weight: 900; color: #1f4e88; margin-bottom: 2mm; }
        .amount-words {
            margin-top: 3mm;
            padding-top: 2.5mm;
            border-top: 1px dashed #64748b;
            font-weight: 800;
            line-height: 1.55;
        }
        .totals { width: 100%; border-collapse: collapse; background: #fff9ca; }
        .totals th, .totals td { border: 1px solid #6f7780; padding: 2.35mm 2mm; }
        .totals th {
            width: 57%;
            text-align: left;
            color: #213f7d;
            font-weight: 900;
            background: linear-gradient(90deg, #f8f3b7, #fffbe6);
        }
        .totals td { text-align: right; font-weight: 900; font-variant-numeric: tabular-nums; }
        .totals tr.grand th, .totals tr.grand td { background: #dff4ff; color: #123d7a; }
        .totals tr.balance th, .totals tr.balance td { background: #fff2a2; color: #a01414; font-size: 13px; }
        .seller-band {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5mm;
            margin-top: 4mm;
            font-size: 11px;
        }
        .seller-card {
            padding: 2.5mm 3mm;
            border: 1px solid #7a8793;
            background: rgba(255,255,255,.65);
        }
        .memo-footer {
            margin-top: 4mm;
            text-align: center;
            font-size: 13px;
            font-weight: 900;
            color: #263b6d;
        }
        .signature-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28mm;
            margin-top: 13mm;
            text-align: center;
        }
        .signature-line { padding-top: 2mm; border-top: 1px solid #64748b; font-weight: 800; }
        .draft-stamp {
            position: absolute;
            z-index: 1;
            top: 47%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-22deg);
            color: rgba(185, 28, 28, .10);
            font-size: 72px;
            font-weight: 900;
            letter-spacing: 7px;
            pointer-events: none;
        }
        .money { font-variant-numeric: tabular-nums; }
        @page { size: A4 portrait; margin: 0; }
        @media print {
            body { background: #fff; }
            .memo-toolbar { display: none !important; }
            .memo-page {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                border: 0;
                box-shadow: none;
                page-break-after: always;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        @media screen and (max-width: 900px) {
            .memo-toolbar { width: calc(100% - 20px); }
            .memo-page { transform-origin: top left; width: 210mm; }
            body { overflow-x: auto; }
        }
    </style>
</head>
<body>
@php
    $bn = app()->getLocale() === 'bn';
    $companyName = $bn
        ? ($settings['company_name_bn'] ?? 'মেসার্স আফরিন ট্রেডার্স')
        : ($settings['company_name'] ?? 'M/S Afrin Traders');
    $owner = $bn
        ? ($settings['company_owner_bn'] ?? 'শামছুল মোল্লা')
        : ($settings['company_owner'] ?? 'Shamsul Molla');
    $address = $bn
        ? ($settings['company_address_bn'] ?? $settings['company_address'] ?? '')
        : ($settings['company_address'] ?? $settings['company_address_bn'] ?? '');
    $businessDescription = $bn
        ? ($settings['memo_business_description_bn'] ?? '')
        : ($settings['memo_business_description_en'] ?? '');
    $footerText = $bn
        ? ($settings['memo_footer_bn'] ?? '')
        : ($settings['memo_footer_en'] ?? '');
    $sellerCode = $sale->creator?->seller_code ?: ($sale->creator ? 'USR-' . str_pad((string) $sale->creator->id, 5, '0', STR_PAD_LEFT) : '—');
@endphp

<div class="memo-toolbar">
    <div class="toolbar-left">
        <a class="btn" href="{{ route('sales.show', $sale) }}">← {{ __('Back to Sale') }}</a>
        <button class="btn btn-primary" type="button" onclick="window.print()">{{ __('Print Memo') }}</button>
    </div>
    <div class="toolbar-right">
        <a class="btn btn-lang {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">EN</a>
        <a class="btn btn-lang {{ app()->getLocale() === 'bn' ? 'active' : '' }}" href="{{ route('language.switch', 'bn') }}">বাংলা</a>
    </div>
</div>

<section class="memo-page">
    @if($sale->status !== 'posted')
        <div class="draft-stamp">{{ __('DRAFT') }}</div>
    @endif

    <div class="memo-content">
        <header class="memo-header">
            <div class="brand-mark">AT</div>
            <div>
                <div class="brand-side">{{ __('Bismillahir Rahmanir Rahim') }}</div>
                <h1 class="company-name">{{ $companyName }}</h1>
                <div class="owner-line">{{ __('Proprietor') }}: {{ $owner }}</div>
            </div>
            <div class="brand-side">
                {{ __('Sales Memo') }}<br>
                {{ __('TMT Rod • Cement • Construction Materials') }}
            </div>
        </header>

        @if($businessDescription)
            <div class="business-description">{{ $businessDescription }}</div>
        @endif

        <div class="contact-line">
            @if($address)<div>{{ $address }}</div>@endif
            @if($settings['company_phone'] ?? null)<div>{{ __('Mobile') }}: {{ $settings['company_phone'] }}</div>@endif
        </div>

        <div class="info-grid">
            <div class="field-row">
                <div class="field-label">{{ __('Memo No.') }}</div>
                <div class="field-value">{{ $sale->invoice_no }} ({{ $sale->currency_code ?? 'BDT' }})</div>
            </div>
            <div class="field-row">
                <div class="field-label">{{ __('Date') }}</div>
                <div class="field-value">{{ $sale->sale_date?->format('Y-m-d') ?? '—' }}</div>
            </div>

            <div class="field-row full-width">
                <div class="field-label">{{ __('Name') }}</div>
                <div class="field-value">{{ $sale->customer?->name ?? __('Walk-in Customer') }}</div>
            </div>

            <div class="customer-contact full-width">
                <div class="field-row">
                    <div class="field-label">{{ __('Address') }}</div>
                    <div class="field-value">
                        {{ collect([$sale->customer?->address, $sale->customer?->city, $sale->customer?->district])->filter()->implode(', ') ?: '—' }}
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-label">{{ __('Mobile') }}</div>
                    <div class="field-value">{{ $sale->customer?->phone ?? '—' }}</div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <colgroup>
                <col style="width:8%">
                <col style="width:34%">
                <col style="width:13%">
                <col style="width:10%">
                <col style="width:12%">
                <col style="width:11%">
                <col style="width:12%">
            </colgroup>
            <thead>
                <tr>
                    <th>{{ __('SL') }}</th>
                    <th>{{ __('Product Description') }}</th>
                    <th>{{ __('Bag / Bundle') }}</th>
                    <th>{{ __('Unit') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Rate') }}</th>
                    <th>{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="desc">
                            {{ $item->product?->display_name ?? __('Deleted product') }}
                            @if((float)($item->free_quantity ?? 0)>0)<div class="muted">{{ __('Free Qty') }}: {{ rtrim(rtrim(number_format((float)$item->free_quantity,4,'.',''),'0'),'.') }}</div>@endif
                            @if($item->product?->code)
                                <div class="muted">{{ __('Code') }}: {{ $item->product->code }}</div>
                            @endif
                        </td>
                        <td class="center">
                            {{ $item->bundle_quantity !== null ? rtrim(rtrim(number_format((float) $item->bundle_quantity, 4, '.', ''), '0'), '.') : '—' }}
                        </td>
                        <td class="center">{{ $item->product?->unit?->short_name ?? $item->product?->unit?->name ?? '—' }}</td>
                        <td class="right">{{ rtrim(rtrim(number_format((float) $item->quantity, 4, '.', ''), '0'), '.') }}</td>
                        <td class="right">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="right">{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach

                @for($i = $sale->items->count(); $i < 8; $i++)
                    <tr>
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="memo-lower">
            <div>
                <div class="notes-box">
                    <div class="notes-title">{{ __('Notes') }}</div>
                    <div>{{ $sale->notes ?: '—' }}</div>
                    @if($memo['amount_in_words'])
                    <div class="amount-words">{{ __('Current Invoice In Words') }}: {{ $memo['amount_in_words'] }}</div>
                    @endif
                </div>

                <div class="seller-band">
                    <div class="seller-card">
                        <strong>{{ __('Seller ID') }}:</strong> {{ $sellerCode }}<br>
                        <strong>{{ __('Seller') }}:</strong> {{ $sale->creator?->name ?? '—' }}
                    </div>
                    <div class="seller-card">
                        <strong>{{ __('Warehouse') }}:</strong> {{ $sale->warehouse?->name ?? '—' }}<br>
                        <strong>{{ __('Status') }}:</strong> {{ __(ucfirst($sale->status)) }}
                    </div>
                </div>
            </div>

            <table class="totals">
                <tr><th>{{ __('Item Subtotal') }}</th><td>{{ number_format((float)$sale->subtotal,2) }}</td></tr>
                <tr><th>{{ __('Invoice Discount') }}</th><td>−{{ number_format((float)$sale->discount,2) }}</td></tr>
                <tr><th>{{ __('Invoice Tax') }}</th><td>{{ number_format((float)$sale->tax,2) }}</td></tr>
                <tr>
                    <th>{{ __('Transport Charge') }}</th>
                    <td>{{ number_format((float) $sale->shipping, 2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Labor Charge') }}</th>
                    <td>{{ number_format((float) $sale->labor_charge, 2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Total Bill') }}</th>
                    <td>{{ number_format($memo['current_bill'], 2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Previous Due') }}</th>
                    <td>{{ number_format($memo['previous_due'], 2) }}</td>
                </tr>
                <tr class="grand">
                    <th>{{ __('Grand Total') }}</th>
                    <td>{{ number_format($memo['grand_payable'], 2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Advance / Deposit') }}</th>
                    <td>{{ number_format($memo['advance'], 2) }}</td>
                </tr>
                <tr class="balance">
                    <th>{{ __('Balance') }}</th>
                    <td>{{ number_format($memo['balance'], 2) }}</td>
                </tr>
            </table>
        </div>

        @if($footerText)
            <div class="memo-footer">{{ $footerText }}</div>
        @endif

        <div class="signature-row">
            <div class="signature-line">{{ __('Seller Signature') }}</div>
            <div class="signature-line">{{ __('For M/S Afrin Traders') }}</div>
        </div>
    </div>
</section>
</body>
</html>
