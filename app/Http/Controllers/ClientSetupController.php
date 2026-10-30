<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\MarketHierarchy;
use Illuminate\Http\Request;

class ClientSetupController extends Controller
{
    public function index(Request $request)
    {
        $clients = Customer::with(['group','marketHierarchy'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where(function ($x) use ($term) {
                    $x->where('name','like',"%{$term}%")
                        ->orWhere('code','like',"%{$term}%")
                        ->orWhere('phone','like',"%{$term}%")
                        ->orWhere('company_name','like',"%{$term}%");
                });
            })
            ->when($request->filled('group'), fn($q) => $q->where('customer_group_id', $request->integer('group')))
            ->when($request->filled('market'), fn($q) => $q->where('market_hierarchy_id', $request->integer('market')))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('settings.client-setup', [
            'clients' => $clients,
            'groups' => CustomerGroup::orderBy('name')->get(),
            'markets' => MarketHierarchy::with('parent')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'stats' => [
                'total' => Customer::count(),
                'active' => Customer::where('is_active', true)->count(),
                'groups' => CustomerGroup::where('is_active', true)->count(),
                'markets' => MarketHierarchy::where('is_active', true)->count(),
            ],
        ]);
    }
}
