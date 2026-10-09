@extends('layouts.app')
<<<<<<< HEAD
@section('title',$payroll->payroll_no) @section('heading',__('Payroll ').$payroll->payroll_no.' - '.$payroll->period)
@section('actions')@if($payroll->status==='draft')@can('payroll.post')<form class="d-inline" method="POST" action="{{ route('payrolls.post',$payroll) }}">@csrf<button class="btn btn-success">{{ __('Post Payroll') }}</button></form>@endcan @endif @endsection
@section('content')<form method="POST" action="{{ route('payrolls.update',$payroll) }}">@csrf @method('PUT')<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>{{ __('Employee') }}</th><th class="money">{{ __('Basic') }}</th><th class="money">{{ __('Allowance') }}</th><th>{{ __('Bonus') }}</th><th>{{ __('Overtime') }}</th><th>{{ __('Deduction') }}</th><th class="money">{{ __('Net') }}</th><th class="money">{{ __('Paid') }}</th></tr></thead><tbody>@foreach($payroll->items as $i)<tr><td>{{ $i->employee->name }}</td><td class="money">{{ number_format($i->basic_salary,2) }}</td><td class="money">{{ number_format($i->allowance,2) }}</td><td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[{{ $i->id }}][bonus]" value="{{ $i->bonus }}" @disabled($payroll->status!=='draft')></td><td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[{{ $i->id }}][overtime]" value="{{ $i->overtime }}" @disabled($payroll->status!=='draft')></td><td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[{{ $i->id }}][deduction]" value="{{ $i->deduction }}" @disabled($payroll->status!=='draft')></td><td class="money">{{ number_format($i->net_salary,2) }}</td><td class="money">{{ number_format($i->paid_amount,2) }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="6" class="text-end">{{ __('Net Total') }}</th><th class="money">{{ number_format($payroll->net_total,2) }}</th><th></th></tr></tfoot></table></div></div>@if($payroll->status==='draft')<div class="mt-3"><button class="btn btn-primary">{{ __('Recalculate') }}</button></div>@endif</form>@endsection
=======
@section('title', $payroll->payroll_no)
@section('heading', 'Payroll ' . $payroll->payroll_no . ' - ' . $payroll->period)

@section('actions')
    @if ($payroll->status === 'draft')
        @can('payroll.post')
            <form class="d-inline" method="POST" action="{{ route('payrolls.post', $payroll) }}">
                @csrf
                <button class="btn btn-success">Post Payroll</button>
            </form>
        @endcan
    @endif
@endsection
@section('content')<form method="POST" action="{{ route('payrolls.update',$payroll) }}">@csrf @method('PUT')<div class="card"><table class="table mb-0"><thead><tr><th>Employee</th><th class="money">Basic</th><th class="money">Allowance</th><th>Bonus</th><th>Overtime</th><th>Deduction</th><th class="money">Net</th><th class="money">Paid</th></tr></thead><tbody>@foreach($payroll->items as $i)<tr><td>{{ $i->employee->name }}</td><td class="money">{{ number_format($i->basic_salary,2) }}</td><td class="money">{{ number_format($i->allowance,2) }}</td><td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[{{ $i->id }}][bonus]" value="{{ $i->bonus }}" @disabled($payroll->status!=='draft')></td><td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[{{ $i->id }}][overtime]" value="{{ $i->overtime }}" @disabled($payroll->status!=='draft')></td><td><input class="form-control form-control-sm" type="number" step="0.01" min="0" name="items[{{ $i->id }}][deduction]" value="{{ $i->deduction }}" @disabled($payroll->status!=='draft')></td><td class="money">{{ number_format($i->net_salary,2) }}</td><td class="money">{{ number_format($i->paid_amount,2) }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="6" class="text-end">Net Total</th><th class="money">{{ number_format($payroll->net_total,2) }}</th><th></th></tr></tfoot></table></div>@if($payroll->status==='draft')<div class="mt-3"><button class="btn btn-primary">Recalculate</button></div>@endif</form>@endsection
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
