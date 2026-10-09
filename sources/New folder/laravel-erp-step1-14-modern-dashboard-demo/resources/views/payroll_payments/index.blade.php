@extends('layouts.app')
@section('title','Salary Payments') @section('heading','Salary Payments')
@section('actions')<a href="{{ route('payroll-payments.create') }}" class="btn btn-primary">Pay Salary</a>@endsection
@section('content')<div class="card"><table class="table mb-0"><thead><tr><th>Date</th><th>No</th><th>Employee</th><th>Account</th><th>Method</th><th class="money">Amount</th></tr></thead><tbody>@forelse($rows as $r)<tr><td>{{ $r->payment_date->format('Y-m-d') }}</td><td>{{ $r->payment_no }}</td><td>{{ $r->item->employee->name }}</td><td>{{ $r->paymentAccount->name }}</td><td>{{ $r->method }}</td><td class="money">{{ number_format($r->amount,2) }}</td></tr>@empty<tr><td colspan="6">No salary payments.</td></tr>@endforelse</tbody></table></div>{{ $rows->links() }}@endsection
