@extends('layouts.app')
@section('title', __('Data Backups'))
@section('heading', __('Data Backups'))
@section('actions')
@can('settings.edit')
<form method="POST" action="{{ route('backups.store') }}">@csrf
    <button class="btn btn-primary"><i class="bi bi-database-add"></i> {{ __('Create Encrypted Backup') }}</button>
</form>
@endcan
@endsection
@section('content')
<div class="alert alert-info">{{ __('New backups are encrypted with the server APP_KEY. Keep the APP_KEY separately and securely; without it, encrypted backups cannot be restored. The daily schedule requires a working server cron.') }}</div>
@if(!$webRestoreEnabled)
<div class="alert alert-warning">{{ __('Browser restore is disabled for safety. Restore on a verified staging copy using the documented administrator procedure.') }}</div>
@endif
<div class="alert {{ $offsiteStatus['configured'] ? 'alert-success' : 'alert-warning' }}">
<strong>{{ __('Off-site encrypted backups:') }}</strong>
@if($offsiteStatus['configured'])
{{ __('Configured') }}.
@if($offsiteStatus['last_verified'])
{{ __('Last verified remotely:') }} {{ $offsiteStatus['last_verified']['verified_at'] ?? '-' }}
<small class="d-block">{{ __('Latest verified archive:') }} {{ $offsiteStatus['last_verified']['name'] ?? '-' }}</small>
@else
<strong>{{ __('No successful remote verification recorded yet.') }}</strong>
@endif
@else
<strong>{{ __('Not configured — backups on this server do not provide disaster recovery.') }}</strong>
@endif
<div class="small">{{ __('A verified archive is not a verified restoration. Run the isolated recovery drill using the administrator instructions.') }}</div>
</div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr>
<th>{{ __('Backup File') }}</th><th>{{ __('Protection') }}</th><th>{{ __('Created') }}</th><th class="text-end">{{ __('Size') }}</th><th class="text-end">{{ __('Actions') }}</th>
</tr></thead><tbody>
@forelse($backups as $b)
<tr><td class="font-monospace">{{ $b['name'] }}</td>
<td>
@if($b['encrypted'])<span class="badge text-bg-success">{{ __('Encrypted') }}</span>
@else<span class="badge text-bg-danger">{{ __('Legacy — unencrypted') }}</span>@endif
</td>
<td>{{ $b['created_at'] }}</td><td class="text-end">{{ number_format($b['size']/1024,1) }} KB</td>
<td class="text-end">
@can('settings.edit')
<a class="btn btn-sm btn-outline-primary" href="{{ route('backups.download',$b['name']) }}">{{ __('Download') }}</a>
@if($b['encrypted'])
<form class="d-inline" method="POST" action="{{ route('backups.verify',$b['name']) }}">@csrf
<button class="btn btn-sm btn-outline-secondary">{{ __('Verify') }}</button></form>
@if($offsiteStatus['configured'])
<form class="d-inline" method="POST" action="{{ route('backups.verify-offsite',$b['name']) }}">@csrf
<button class="btn btn-sm btn-outline-success">{{ __('Verify Off-site') }}</button></form>
@endif
@endif
@if($webRestoreEnabled)
<form class="d-inline" method="POST" action="{{ route('backups.restore',$b['name']) }}" onsubmit="return confirm('{{ __('This replaces the current database and uploaded files. A tested off-site backup is mandatory. Continue?') }}')">
@csrf<input type="hidden" name="confirm" value="RESTORE">
<input type="password" name="password" required autocomplete="current-password" aria-label="{{ __('Confirm your account password') }}" placeholder="{{ __('Your password') }}" class="form-control form-control-sm d-inline-block" style="width:150px">
<button class="btn btn-sm btn-outline-warning">{{ __('Restore') }}</button></form>
@endif
<form class="d-inline" method="POST" action="{{ route('backups.destroy',$b['name']) }}" onsubmit="return confirm('{{ __('Delete this backup?') }}')">
@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button></form>
@endcan
</td></tr>
@empty<tr><td colspan="5">{{ __('No backups created yet.') }}</td></tr>@endforelse
</tbody></table></div></div>
@endsection
