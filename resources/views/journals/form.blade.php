@extends('layouts.app')
@section('title',__('Manual Journal')) @section('heading',__('Manual Journal Entry'))
@section('content')<form method="POST" action="{{ route('journals.store') }}" class="card card-body">@csrf<div class="row g-3"><div class="col-md-3"><label>{{ __('Date') }}</label><input type="date" name="journal_date" value="{{ now()->toDateString() }}" class="form-control"></div><div class="col"><label>{{ __('Description') }}</label><input name="description" class="form-control" required></div></div><hr><div class="table-responsive"><table class="table" id="jt"><thead><tr><th>{{ __('Account') }}</th><th>{{ __('Debit') }}</th><th>{{ __('Credit') }}</th><th>{{ __('Description') }}</th><th></th></tr></thead><tbody></tbody></table></div><button type="button" id="add" class="btn btn-sm btn-outline-primary align-self-start">{{ __('Add Line') }}</button><div class="mt-3"><button class="btn btn-success">{{ __('Post Journal') }}</button></div></form>@endsection
@php
    $accountOptions = $accounts->map(fn ($a) => [
        'id' => $a->id,
        'name' => $a->code.' - '.$a->name,
    ])->values()->all();
@endphp
@push('scripts')<script>const accounts=@json($accountOptions);let i=0;function add(){let tr=document.createElement('tr');tr.innerHTML=`<td><select class="form-select" name="lines[${i}][account_id]">${accounts.map(a=>`<option value="${a.id}">${a.name}</option>`).join('')}</select></td><td><input type="number" step="0.01" min="0" name="lines[${i}][debit]" value="0" class="form-control"></td><td><input type="number" step="0.01" min="0" name="lines[${i}][credit]" value="0" class="form-control"></td><td><input name="lines[${i}][description]" class="form-control"></td><td><button type="button" class="btn btn-outline-danger btn-sm">×</button></td>`;tr.querySelector('button').onclick=()=>tr.remove();document.querySelector('#jt tbody').appendChild(tr);i++}document.getElementById('add').onclick=add;add();add();</script>@endpush
