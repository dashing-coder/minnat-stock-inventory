@extends('layouts.app')
@section('title',__('Quotations')) @section('heading',__('Quotations'))
@section('actions')@can('quotations.create')<a class="btn btn-primary" href="{{ route('quotations.create') }}">{{ __('New Quotation') }}</a>@endcan @endsection
@section('content')<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('No') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Valid Until') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="money">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>@forelse($rows as $r)<tr>
                    <td>{{ $r->quotation_date->format('Y-m-d') }}</td>
                    <td><a href="{{ route('quotations.show',$r) }}">{{ $r->quotation_no }}</a></td>
                    <td>{{ $r->customer?->name ?? 'Walk-in' }}</td>
                    <td>{{ $r->valid_until?->format('Y-m-d') }}</td>
                    <td>{{ __(ucfirst($r->status)) }}</td>
                    <td class="money">{{ number_format($r->grand_total,2) }}</td>
                </tr>@empty<tr>
                    <td colspan="6">{{ __('No quotations.') }}</td>
                </tr>@endforelse</tbody>
        </table>
    </div>
</div>{{ $rows->links() }}@endsection