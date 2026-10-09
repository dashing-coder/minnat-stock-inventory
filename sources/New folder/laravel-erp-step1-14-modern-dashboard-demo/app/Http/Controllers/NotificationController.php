<?php
namespace App\Http\Controllers;
use App\Models\SystemNotification;
class NotificationController extends Controller
{
    public function index(){return view('notifications.index',['rows'=>SystemNotification::where(fn($q)=>$q->whereNull('user_id')->orWhere('user_id',auth()->id()))->latest()->paginate(30)]);}
    public function read(SystemNotification $notification){abort_if($notification->user_id && $notification->user_id!==auth()->id(),403);$notification->update(['read_at'=>now()]);return back();}
    public function readAll(){SystemNotification::where(fn($q)=>$q->whereNull('user_id')->orWhere('user_id',auth()->id()))->whereNull('read_at')->update(['read_at'=>now()]);return back()->with('success','Notifications marked read.');}
}
