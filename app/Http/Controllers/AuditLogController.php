<?php
namespace App\Http\Controllers;
use App\Models\ActivityLog;use Illuminate\Http\Request;
class AuditLogController extends Controller
{
    public function index(Request $r){$rows=ActivityLog::with('user')->when($r->filled('user_id'),fn($q)=>$q->where('user_id',$r->user_id))->when($r->filled('action'),fn($q)=>$q->where('action',$r->action))->when($r->filled('from'),fn($q)=>$q->whereDate('created_at','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('created_at','<=',$r->to))->latest()->paginate(50)->withQueryString();return view('audit_logs.index',compact('rows'));}
}
