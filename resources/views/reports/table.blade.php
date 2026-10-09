@extends('layouts.app')
<<<<<<< HEAD
@section('title',$title) @section('heading',$title)
@section('actions')<button class="btn btn-outline-secondary" onclick="window.print()">{{ __('Print') }}</button> <a class="btn btn-outline-success" href="{{ request()->fullUrlWithQuery(['csv'=>1]) }}">{{ __('CSV Export') }}</a>@endsection
@section('content')<form method="GET" class="card card-body mb-3 no-print"><div class="row g-2"><div class="col-md-3"><label>{{ __('From') }}</label><input type="date" name="from" value="{{ request('from',now()->startOfMonth()->toDateString()) }}" class="form-control"></div><div class="col-md-3"><label>{{ __('To') }}</label><input type="date" name="to" value="{{ request('to',now()->toDateString()) }}" class="form-control"></div><div class="col-md-3"><label>{{ __('Party/Product ID') }}</label><input name="entity_id" value="{{ request('entity_id') }}" class="form-control" placeholder="{{ __('Optional generic filter') }}"></div><div class="col-md-3"><label>{{ __('Type / Method') }}</label><input name="filter" value="{{ request('filter') }}" class="form-control" placeholder="{{ __('Optional type/method') }}"></div><div class="col-md-12"><button class="btn btn-secondary">{{ __('Apply Filters') }}</button></div></div></form><div class="card"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr></thead><tbody>@forelse($rows as $row)<tr>@foreach($row as $v)<td>{{ $v }}</td>@endforeach</tr>@empty<tr><td colspan="{{ count($headers) }}">{{ __('No data for selected period.') }}</td></tr>@endforelse</tbody></table></div></div>@if($totals)<div class="card card-body mt-3"><div class="row">@foreach($totals as $k=>$v)<div class="col-md-3"><small class="text-muted">{{ $k }}</small><div class="fw-bold">{{ $v }}</div></div>@endforeach</div></div>@endif
=======

@section('title', $title)
@section('heading', $title)

@section('content')
    <form method="GET" class="card card-body mb-3 no-print">
        <div class="row g-2">
            <div class="col-md-3">
                <label>From</label>
                <input
                    type="date"
                    name="from"
                    value="{{ request('from', now()->startOfMonth()->toDateString()) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-3">
                <label>To</label>
                <input
                    type="date"
                    name="to"
                    value="{{ request('to', now()->toDateString()) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-3">
                <label>Party/Product ID</label>
                <input
                    name="entity_id"
                    value="{{ request('entity_id') }}"
                    class="form-control"
                    placeholder="Optional generic filter"
                >
            </div>

            <div class="col-md-3">
                <label>Type / Method</label>
                <input
                    name="filter"
                    value="{{ request('filter') }}"
                    class="form-control"
                    placeholder="Optional type/method"
                >
            </div>

            <div class="col-md-12">
                <button class="btn btn-secondary">Apply Filters</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        @foreach ($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            @foreach ($row as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}">No data for selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($totals)
        <div class="card card-body mt-3">
            <div class="row">
                @foreach ($totals as $key => $value)
                    <div class="col-md-3">
                        <small class="text-muted">{{ $key }}</small>
                        <div class="fw-bold">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
@endsection
