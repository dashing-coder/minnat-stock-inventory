<?php
namespace App\Http\Controllers;
use App\Models\Setting;use Illuminate\Http\Request;
class SettingsController extends Controller
{
    public function index(){return view('settings.index',['settings'=>Setting::orderBy('group')->orderBy('key')->get()->groupBy('group')]);}
    public function update(Request $r){$values=$r->input('settings',[]);foreach($values as $key=>$value){Setting::where('key',$key)->update(['value'=>$value]);}return back()->with('success','Settings saved.');}
}
