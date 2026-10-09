<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $service) {}

    public function index(Request $request)
    {
        $suppliers = Supplier::with('group')
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
            'groups'=>SupplierGroup::where('is_active',true)->orderBy('name')->get(),
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->service->update($supplier,$request->validated());
        return redirect()->route('suppliers.show',$supplier)->with('success',__('Supplier updated successfully.'));
    }

    public function destroy(Supplier $supplier)
    {
        abort_if($supplier->ledgerEntries()->exists(),422,__('Supplier has ledger transactions. Set the supplier inactive instead.'));
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success',__('Supplier deleted.'));
    }
}
