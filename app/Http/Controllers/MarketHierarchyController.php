<?php

namespace App\Http\Controllers;

use App\Models\MarketHierarchy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MarketHierarchyController extends Controller
{
    public function index()
    {
        $nodes = MarketHierarchy::with(['parent'])->withCount(['children','customers'])->orderBy('sort_order')->orderBy('name')->get();
        return view('settings.market-hierarchy', [
            'nodes' => $nodes,
            'options' => MarketHierarchy::with('parent')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        MarketHierarchy::create($data);
        return back()->with('success', __('Market hierarchy item created successfully.'));
    }

    public function update(Request $request, MarketHierarchy $marketHierarchy)
    {
        $data = $this->validated($request, $marketHierarchy);
        if (!empty($data['parent_id']) && $this->wouldCreateCycle($marketHierarchy, (int) $data['parent_id'])) {
            throw ValidationException::withMessages(['parent_id'=>__('A market hierarchy item cannot be moved under itself or one of its descendants.')]);
        }
        $marketHierarchy->update($data);
        return back()->with('success', __('Market hierarchy item updated successfully.'));
    }

    public function destroy(MarketHierarchy $marketHierarchy)
    {
        if ($marketHierarchy->children()->exists()) {
            throw ValidationException::withMessages(['market_hierarchy'=>__('Delete or move child hierarchy items first.')]);
        }
        if ($marketHierarchy->customers()->exists()) {
            throw ValidationException::withMessages(['market_hierarchy'=>__('This market hierarchy item is assigned to clients. Reassign those clients first.')]);
        }
        $marketHierarchy->delete();
        return back()->with('success', __('Market hierarchy item deleted successfully.'));
    }

    private function validated(Request $request, ?MarketHierarchy $node = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable','integer','exists:market_hierarchies,id', Rule::notIn(array_filter([$node?->id]))],
            'name' => ['required','string','max:180'],
            'code' => ['nullable','string','max:60', Rule::unique('market_hierarchies','code')->ignore($node?->id)],
            'level_type' => ['required', Rule::in(['region','area','territory','market'])],
            'sort_order' => ['required','integer','min:0','max:999999'],
            'description' => ['nullable','string','max:2000'],
            'is_active' => ['required','boolean'],
        ]);
    }

    private function wouldCreateCycle(MarketHierarchy $node, int $candidateParentId): bool
    {
        if ($candidateParentId === $node->id) return true;
        $cursor = MarketHierarchy::find($candidateParentId);
        $guard = 0;
        while ($cursor && $guard++ < 100) {
            if ($cursor->id === $node->id) return true;
            $cursor = $cursor->parent;
        }
        return false;
    }
}
