<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReorderLevelController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category','unit'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where(function ($x) use ($term) {
                    $x->where('name','like',"%{$term}%")
                        ->orWhere('sku','like',"%{$term}%")
                        ->orWhere('barcode','like',"%{$term}%");
                });
            })
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('settings.reorder-levels', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'levels' => ['required','array','min:1'],
            'levels.*.reorder_level' => ['required','numeric','min:0'],
            'levels.*.minimum_stock_level' => ['required','numeric','min:0'],
            'levels.*.maximum_stock_level' => ['nullable','numeric','min:0'],
        ]);

        $ids = array_map('intval', array_keys($validated['levels']));
        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($validated['levels'] as $id => $row) {
            if (!$products->has((int) $id)) {
                throw ValidationException::withMessages(['levels'=>__('One or more products no longer exist.')]);
            }
            $reorder = (float) $row['reorder_level'];
            $minimum = (float) $row['minimum_stock_level'];
            $maximum = $row['maximum_stock_level'] === null || $row['maximum_stock_level'] === '' ? null : (float) $row['maximum_stock_level'];
            if ($maximum !== null && $maximum < $minimum) {
                throw ValidationException::withMessages([
                    "levels.$id.maximum_stock_level" => __('Maximum stock must be greater than or equal to minimum stock.'),
                ]);
            }
            if ($maximum !== null && $maximum < $reorder) {
                throw ValidationException::withMessages([
                    "levels.$id.maximum_stock_level" => __('Maximum stock must be greater than or equal to reorder level.'),
                ]);
            }
        }

        DB::transaction(function () use ($validated, $products) {
            foreach ($validated['levels'] as $id => $row) {
                $products[(int) $id]->update([
                    'reorder_level' => $row['reorder_level'],
                    'minimum_stock_level' => $row['minimum_stock_level'],
                    'maximum_stock_level' => $row['maximum_stock_level'] === '' ? null : $row['maximum_stock_level'],
                ]);
            }
        });

        return back()->with('success', __('Reorder levels updated successfully.'));
    }
}
