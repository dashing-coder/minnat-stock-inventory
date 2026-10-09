<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
<<<<<<< HEAD
    public function index(){return view('settings.index',['settings'=>Setting::orderBy('group')->orderBy('key')->get()->groupBy('group')]);}
    public function update(Request $r){$values=$r->input('settings',[]);foreach($values as $key=>$value){Setting::where('key',$key)->update(['value'=>$value]);}return back()->with('success',__('Settings saved.'));}
=======
    public function index()
    {
        return view('settings.index', [
            'settings' => Setting::orderBy('group')->orderBy('key')->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request)
    {
        $values = $request->input('settings', []);
        if (!is_array($values)) {
            throw ValidationException::withMessages(['settings' => 'Invalid settings payload.']);
        }

        $settings = Setting::query()->whereIn('key', array_keys($values))->get()->keyBy('key');

        foreach ($values as $key => $value) {
            $setting = $settings->get($key);
            if (!$setting) {
                continue;
            }

            $normalized = $this->validateAndNormalize($setting, $value);
            $setting->update(['value' => $normalized]);
        }

        return back()->with('success', 'Settings saved. Clear Laravel caches if configuration was cached.');
    }

    private function validateAndNormalize(Setting $setting, mixed $value): ?string
    {
        $key = $setting->key;

        if ($setting->type === 'boolean') {
            $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($parsed === null && !in_array((string) $value, ['0', '1'], true)) {
                throw ValidationException::withMessages(["settings.$key" => 'This setting must be true or false.']);
            }
            return ($parsed ?? ((string) $value === '1')) ? '1' : '0';
        }

        $value = is_scalar($value) ? trim((string) $value) : '';

        if ($key === 'timezone' && !in_array($value, timezone_identifiers_list(), true)) {
            throw ValidationException::withMessages(["settings.$key" => 'Please select a valid PHP timezone identifier.']);
        }

        if (in_array($key, ['invoice_prefix', 'purchase_prefix'], true)) {
            $value = strtoupper($value);
            if (!preg_match('/^[A-Z0-9_-]{1,10}$/', $value)) {
                throw ValidationException::withMessages(["settings.$key" => 'Prefix may contain only A-Z, 0-9, underscore or hyphen (maximum 10 characters).']);
            }
        }

        if ($key === 'currency') {
            $value = strtoupper($value);
            if (!preg_match('/^[A-Z]{3,5}$/', $value)) {
                throw ValidationException::withMessages(["settings.$key" => 'Currency must be a 3-5 letter code such as USD.']);
            }
        }

        if ($key === 'date_format' && ($value === '' || strlen($value) > 30)) {
            throw ValidationException::withMessages(["settings.$key" => 'Date format is invalid.']);
        }

        if (strlen($value) > 1000) {
            throw ValidationException::withMessages(["settings.$key" => 'Setting value is too long.']);
        }

        return $value;
    }
>>>>>>> a903df116cf65a53178fd1bbf9f2abb17c72c5a5
}
