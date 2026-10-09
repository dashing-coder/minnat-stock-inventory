@extends('layouts.app')
@section('title','Journals') @section('heading','Journal Entries')
@section('actions')@can('journals.create')<a href="{{ route('journals.create') }}" class="btn btn-primary">Manual Journal</a>@endcan@endsection
@section('content')<div class="card"><table class="table mb-0"><thead><tr><th>Date</th><th>Journal</th><th>Type</th><th>Reference</th><th>Description</th></tr></thead><tbody>@forelse($rows as $r)<tr><td>{{ $r->journal_date->format('Y-m-d') }}</td><td><a href="{{ route('journals.show',$r) }}">{{ $r->journal_no }}</a></td><td>{{ $r->reference_type }}</td><td>{{ $r->reference_no }}</td><td>{{ $r->description }}</td></tr>@empty<tr><td colspan="5">No journals.</td></tr>@endforelse</tbody></table></div>{{ $rows->links() }}@endsection
