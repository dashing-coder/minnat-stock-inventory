@extends('layouts.app')
<<<<<<< HEAD
@section('title',__('Settings')) @section('heading',__('System Settings'))
@section('content')<form method="POST" action="{{ route('settings.update') }}">@csrf @method('PUT')@foreach($settings as $group=>$rows)<div class="card card-body mb-3"><h5>{{ __(ucfirst($group)) }}</h5><div class="row g-3">@foreach($rows as $s)<div class="col-md-4"><label>{{ __(ucwords(str_replace('_',' ',$s->key))) }}</label><input name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="form-control"></div>@endforeach</div></div>@endforeach @can('settings.edit')<button class="btn btn-primary">{{ __('Save Settings') }}</button>@endcan</form>@endsection
=======
@section('title','Settings')
@section('heading','System Settings')

@section('content')
<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    @method('PUT')

    @foreach($settings as $group => $rows)
        <div class="card card-body mb-3">
            <h5>{{ ucfirst($group) }}</h5>
            <div class="row g-3">
                @foreach($rows as $s)
                    <div class="col-md-4">
                        <label>{{ ucwords(str_replace('_',' ',$s->key)) }}</label>
                        @if($s->type === 'boolean')
                            <select name="settings[{{ $s->key }}]" class="form-select" @cannot('settings.edit') disabled @endcannot>
                                <option value="1" @selected((string)$s->value === '1')>Enabled</option>
                                <option value="0" @selected((string)$s->value !== '1')>Disabled</option>
                            </select>
                        @else
                            <input name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="form-control" @cannot('settings.edit') readonly @endcannot>
                        @endif
                        @if($s->key === 'timezone')
                            <div class="form-text">Use a PHP timezone such as America/Chicago.</div>
                        @elseif(in_array($s->key, ['invoice_prefix','purchase_prefix']))
                            <div class="form-text">Applied to newly created documents.</div>
                        @endif
                        @error('settings.'.$s->key)<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @can('settings.edit')
        <button class="btn btn-primary">Save Settings</button>
    @endcan
</form>
@endsection
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
