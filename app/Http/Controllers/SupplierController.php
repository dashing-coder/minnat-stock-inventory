<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use App\Services\PartyDeletionGuard;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $service) {}

    public function index(Request $request)
    {
        $suppliers = Supplier::with('group')
            ->withSum('ledgerEntries as ledger_debit', 'debit')
            ->withSum('ledgerEntries as ledger_credit', 'credit')
            ->when($request->filled('q'), fn($q)=>$q->where(function($x) use ($request) {
                $t=$request->q;
                $x->where('name','like',"%{$t}%")->orWhere('code','like',"%{$t}%")
                  ->orWhere('phone','like',"%{$t}%")->orWhere('company_name','like',"%{$t}%");
            }))
            ->when($request->filled('group'), fn($q)=>$q->where('supplier_group_id',$request->group))
            ->when($request->filled('status'), fn($q)=>$q->where('is_active',$request->status==='active'))
            ->latest()->paginate(20)->withQueryString();

        return view('suppliers.index', [
            'suppliers'=>$suppliers,
            'groups'=>SupplierGroup::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('suppliers.form', [
            'supplier'=>new Supplier(),
            'groups'=>SupplierGroup::where('is_active',true)->orderBy('name')->get(),
        ]);
    }

    public function store(SupplierRequest $request)
    {
        $supplier=$this->service->create($request->validated());
        return redirect()->route('suppliers.show',$supplier)->with('success',__('Supplier created successfully.'));
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('group');
        return view('suppliers.show',compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', [
            'supplier'=>$supplier,
            'groups'=>SupplierGroup::query()->where('is_active',true)->orWhere('id', $supplier->supplier_group_id)->orderBy('name')->get(),
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->service->update($supplier,$request->validated());
        return redirect()->route('suppliers.show',$supplier)->with('success',__('Supplier updated successfully.'));
    }

    public function destroy(Supplier $supplier, PartyDeletionGuard $guard)
    {
        // A soft delete is not protected by SQL foreign-key restrictions. Lock the
        // party and refuse deletion if any historical or draft document exists.
        DB::transaction(function () use ($supplier, $guard) {
            $locked = Supplier::query()->lockForUpdate()->findOrFail($supplier->id);
            $guard->assertSupplierCanBeDeleted($locked);
            $locked->delete();
        });
        return redirect()->route('suppliers.index')->with('success',__('Supplier deleted.'));
    }
}
