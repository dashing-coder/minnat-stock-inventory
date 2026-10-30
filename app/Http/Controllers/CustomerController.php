<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\MarketHierarchy;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use App\Services\PartyDeletionGuard;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $service) {}

    public function index(Request $request)
    {
        $customers = Customer::with(['group','marketHierarchy'])
            ->withSum('ledgerEntries as ledger_debit', 'debit')
            ->withSum('ledgerEntries as ledger_credit', 'credit')
            ->when($request->filled('q'), fn($q)=>$q->where(function($x) use ($request) {
                $t=$request->q;
                $x->where('name','like',"%{$t}%")->orWhere('code','like',"%{$t}%")
                  ->orWhere('phone','like',"%{$t}%")->orWhere('company_name','like',"%{$t}%");
            }))
            ->when($request->filled('group'), fn($q)=>$q->where('customer_group_id',$request->group))
            ->when($request->filled('status'), fn($q)=>$q->where('is_active',$request->status==='active'))
            ->latest()->paginate(20)->withQueryString();

        return view('customers.index', [
            'customers'=>$customers,
            'groups'=>CustomerGroup::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('customers.form', [
            'customer'=>new Customer(),
            'groups'=>CustomerGroup::where('is_active',true)->orderBy('name')->get(),
            'markets'=>MarketHierarchy::with('parent')->where('is_active',true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(CustomerRequest $request)
    {
        $customer=$this->service->create($request->validated());
        return redirect()->route('customers.show',$customer)->with('success',__('Customer created successfully.'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['group','marketHierarchy']);
        return view('customers.show',compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', [
            'customer'=>$customer,
            'groups'=>CustomerGroup::query()->where('is_active',true)->orWhere('id', $customer->customer_group_id)->orderBy('name')->get(),
            'markets'=>MarketHierarchy::with('parent')->where('is_active',true)->orWhere('id', $customer->market_hierarchy_id)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->service->update($customer,$request->validated());
        return redirect()->route('customers.show',$customer)->with('success',__('Customer updated successfully.'));
    }

    public function destroy(Customer $customer, PartyDeletionGuard $guard)
    {
        // A soft delete is not protected by SQL foreign-key restrictions. Lock the
        // party and refuse deletion if any historical or draft document exists.
        DB::transaction(function () use ($customer, $guard) {
            $locked = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            $guard->assertCustomerCanBeDeleted($locked);
            $locked->delete();
        });
        return redirect()->route('customers.index')->with('success',__('Customer deleted.'));
    }
}
