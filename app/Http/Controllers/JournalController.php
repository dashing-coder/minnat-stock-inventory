<?php
namespace App\Http\Controllers;
use App\Models\Account;use App\Models\JournalEntry;use App\Services\Accounting\AccountingService;use Illuminate\Http\Request;use RuntimeException;
class JournalController extends Controller
{
    public function __construct(private AccountingService $service){}
    public function index(Request $r){$rows=JournalEntry::with('creator')->when($r->filled('from'),fn($q)=>$q->whereDate('journal_date','>=',$r->from))->when($r->filled('to'),fn($q)=>$q->whereDate('journal_date','<=',$r->to))->when($r->filled('reference_type'),fn($q)=>$q->where('reference_type',$r->reference_type))->latest('journal_date')->latest('id')->paginate(30)->withQueryString();return view('journals.index',compact('rows'));}
    public function create(){return view('journals.form',['accounts'=>Account::where('is_active',true)->orderBy('code')->get()]);}
    public function store(Request $r){$d=$r->validate(['journal_date'=>'required|date','description'=>'required|string','lines'=>'required|array|min:2','lines.*.account_id'=>'required|exists:accounts,id','lines.*.debit'=>'nullable|numeric|min:0','lines.*.credit'=>'nullable|numeric|min:0','lines.*.description'=>'nullable|string']);$tmp=new JournalEntry();$tmp->id=0;$lines=$d['lines'];$entry=$this->service->post('manual_journal',random_int(100000000,999999999),null,$d['journal_date'],$d['description'],$lines);return redirect()->route('journals.show',$entry)->with('success',__('Journal posted.'));}
    public function show(JournalEntry $journal){$journal->load('lines.account','creator');return view('journals.show',compact('journal'));}
}
