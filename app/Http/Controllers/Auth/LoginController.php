<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;use Illuminate\Http\Request;use Illuminate\Support\Facades\Auth;use Illuminate\Support\Facades\RateLimiter;use Illuminate\Validation\ValidationException;
class LoginController extends Controller
{
    public function create(){return view('auth.login');}
    public function store(Request $request){$credentials=$request->validate(['login'=>['required','string'],'password'=>['required','string']]);$key=strtolower($credentials['login']).'|'.$request->ip();if(RateLimiter::tooManyAttempts($key,5)){throw ValidationException::withMessages(['login'=>__('Too many login attempts. Please try again in ').RateLimiter::availableIn($key).' '.__('seconds.')]);}$field=filter_var($credentials['login'],FILTER_VALIDATE_EMAIL)?'email':'username';if(!Auth::attempt([$field=>$credentials['login'],'password'=>$credentials['password']],$request->boolean('remember'))){RateLimiter::hit($key,60);throw ValidationException::withMessages(['login'=>__('Invalid credentials.')]);}RateLimiter::clear($key);$request->session()->regenerate();if(auth()->user()->status!=='active'){Auth::logout();throw ValidationException::withMessages(['login'=>__('This account is not active.')]);}auth()->user()->forceFill(['last_login_at'=>now(),'last_login_ip'=>$request->ip()])->save();return redirect()->intended(route('dashboard'));}
    public function destroy(Request $request){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('login');}
}
