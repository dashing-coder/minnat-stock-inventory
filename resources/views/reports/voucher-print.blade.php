<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $type }} - {{ $journal->journal_no }}</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#eef2f6;color:#172033;font-family:"Noto Sans Bengali","SolaimanLipi",Arial,sans-serif;font-size:12px}.toolbar{width:210mm;margin:14px auto 8px;display:flex;justify-content:space-between}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:7px 13px;border:1px solid #cbd5e1;border-radius:7px;background:#fff;color:#172033;text-decoration:none;font-weight:700;cursor:pointer}.btn-primary{background:#163e79;border-color:#163e79;color:#fff}.page{width:210mm;min-height:148mm;margin:0 auto 24px;padding:12mm;background:#fff;border:1px solid #dce3eb;box-shadow:0 14px 40px rgba(15,23,42,.12)}.header{display:grid;grid-template-columns:1fr 55mm;gap:8mm;align-items:end;border-bottom:3px solid #173e78;padding-bottom:5mm}.company h1{margin:0;color:#173e78;font-size:25px}.company div{margin-top:2mm;color:#475569}.doc{text-align:right}.doc strong{display:block;color:#b42318;font-size:19px}.meta{display:grid;grid-template-columns:1fr 1fr;gap:3mm 8mm;margin:6mm 0}.field{display:grid;grid-template-columns:32mm 1fr;border-bottom:1px solid #cbd5e1;padding:2mm 0}.field b{color:#475569}.full{grid-column:1/-1}.table{width:100%;border-collapse:collapse}.table th,.table td{border:1px solid #94a3b8;padding:2.6mm}.table th{background:#173e78;color:#fff}.right{text-align:right;font-variant-numeric:tabular-nums}.total{font-weight:800}.signatures{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18mm;margin-top:22mm;text-align:center}.signature{border-top:1px solid #64748b;padding-top:2mm;font-weight:700}.alert{border:1px solid #b42318;color:#b42318;background:#fff0f0;padding:3mm;margin:3mm 0;font-weight:700}.footer{margin-top:8mm;text-align:center;color:#64748b;font-size:10px}@page{size:A4 portrait;margin:0}@media print{body{background:#fff}.toolbar{display:none!important}.page{margin:0;border:0;box-shadow:none;width:210mm;min-height:148mm;-webkit-print-color-adjust:exact;print-color-adjust:exact}}@media(max-width:900px){.toolbar{width:calc(100% - 20px)}body{overflow-x:auto}}
</style>
</head>
<body>
@php
    $bn = app()->getLocale()==='bn';
    $companyName = $bn ? ($settings['company_name_bn'] ?? 'মেসার্স আফরিন ট্রেডার্স') : ($settings['company_name'] ?? 'M/S Afrin Traders');
    $address = $bn ? ($settings['company_address_bn'] ?? $settings['company_address'] ?? '') : ($settings['company_address'] ?? $settings['company_address_bn'] ?? '');
    $debitTotal = (float) $journal->lines->sum('debit');
    $creditTotal = (float) $journal->lines->sum('credit');
@endphp
<div class="toolbar"><a class="btn" href="{{ route('reports.vouchers') }}">← {{ __('Back to Vouchers') }}</a><button class="btn btn-primary" onclick="window.print()">{{ __('Print Voucher') }}</button></div>
<section class="page">
<header class="header"><div class="company"><h1>{{ $companyName }}</h1>@if($address)<div>{{ $address }}</div>@endif @if($settings['company_phone'] ?? null)<div>{{ __('Mobile') }}: {{ $settings['company_phone'] }}</div>@endif</div><div class="doc"><strong>{{ $type }}</strong><span>{{ $journal->journal_no }}</span></div></header>
@if(!$voucherBalanced)
<div class="alert">{{ __('AUDIT WARNING: This posted voucher has an incomplete or unbalanced journal. Debit / credit difference:') }} {{ number_format($voucherDifference,2) }}. {{ __('Review the journal before relying on this printout.') }}</div>
@endif
<div class="meta">
<div class="field"><b>{{ __('Date') }}</b><span>{{ $journal->journal_date?->format('Y-m-d') }}</span></div>
<div class="field"><b>{{ __('Reference') }}</b><span>{{ $journal->reference_no ?: '—' }}</span></div>
<div class="field"><b>{{ __('Type') }}</b><span>{{ ucwords(str_replace('_',' ',$journal->reference_type ?? 'journal')) }}</span></div>
<div class="field"><b>{{ __('Prepared By') }}</b><span>{{ $journal->creator?->name ?? '—' }}</span></div>
<div class="field full"><b>{{ __('Particulars') }}</b><span>{{ $journal->description }}</span></div>
</div>
<table class="table"><thead><tr><th>{{ __('Account') }}</th><th>{{ __('Description') }}</th><th class="right">{{ __('Debit') }}</th><th class="right">{{ __('Credit') }}</th></tr></thead><tbody>
@foreach($journal->lines as $line)<tr><td>{{ $line->account?->code }} - {{ $line->account?->name }}</td><td>{{ $line->description ?: $journal->description }}</td><td class="right">{{ number_format((float)$line->debit,2) }}</td><td class="right">{{ number_format((float)$line->credit,2) }}</td></tr>@endforeach
<tr class="total"><td colspan="2">{{ __('Total') }}</td><td class="right">{{ number_format($debitTotal,2) }}</td><td class="right">{{ number_format($creditTotal,2) }}</td></tr>
</tbody></table>
<div class="signatures"><div class="signature">{{ __('Prepared By') }}</div><div class="signature">{{ __('Checked By') }}</div><div class="signature">{{ __('Authorized Signature') }}</div></div>
<div class="footer">{{ __('Computer generated accounting voucher') }} · {{ $journal->journal_no }}</div>
</section>
</body></html>
