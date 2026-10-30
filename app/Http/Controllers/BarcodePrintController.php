<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Barcode\Code39Barcode;
use App\Services\Barcode\BarcodeCatalogService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class BarcodePrintController extends Controller
{
    public function index(Request $request, Code39Barcode $barcode, BarcodeCatalogService $catalog)
    {
        $products = Product::with(['unit','category'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where(function ($x) use ($term) {
                    $x->where('name','like',"%{$term}%")
                        ->orWhere('sku','like',"%{$term}%")
                        ->orWhere('barcode','like',"%{$term}%");
                });
            })
            ->where('is_active', true)
            ->where('track_stock', true)
            ->orderBy('name')->paginate(50)->withQueryString();

        // Show the EXACT value to be printed, including the generated-code fallback.
        $labelInfo = [];
        foreach ($products as $product) {
            try {
                $printable = $catalog->printableCode($product, $barcode);
                $labelInfo[$product->id] = [
                    'code' => $printable['code'], 'source' => $printable['source'],
                    'minimum_width' => $barcode->minimumLabelWidthMm($printable['code']),
                ];
            } catch (InvalidArgumentException $exception) {
                $labelInfo[$product->id] = ['error' => $exception->getMessage()];
            }
        }

        return view('settings.barcode-print', compact('products','labelInfo'));
    }

    public function preview(Request $request, Code39Barcode $barcode, BarcodeCatalogService $catalog)
    {
        $data = $request->validate([
            'products' => ['required','array','min:1'],
            'products.*' => ['required','integer','min:0','max:100'],
            'show_price' => ['nullable','boolean'],
            'show_name' => ['nullable','boolean'],
            'label_width' => ['required','numeric','min:30','max:100'],
            'label_height' => ['required','numeric','min:20','max:80'],
        ]);

        // A very short label cuts off product name/price or the barcode itself.
        $minHeight = ($request->boolean('show_name') && $request->boolean('show_price')) ? 28
            : (($request->boolean('show_name') || $request->boolean('show_price')) ? 24 : 20);
        if ((float)$data['label_height'] < $minHeight) {
            throw ValidationException::withMessages(['label_height' => __('Label height must be at least :height mm for the selected text options.', ['height' => $minHeight])]);
        }

        $selected = collect($data['products'])->filter(fn($copies) => (int) $copies > 0);
        if ($selected->isEmpty()) {
            throw ValidationException::withMessages(['products'=>__('Select at least one product and enter a label quantity.')]);
        }

        if ($selected->sum(fn ($copies) => (int) $copies) > 500) {
            throw ValidationException::withMessages(['products' => __('Print no more than 500 labels in one batch.')]);
        }
        $products = Product::where('is_active', true)->where('track_stock', true)
            ->whereIn('id', $selected->keys()->map(fn ($id) => (int) $id))
            ->get()->keyBy('id');
        $labels = [];
        foreach ($selected as $id => $copies) {
            $product = $products->get((int) $id);
            if (! $product) {
                throw ValidationException::withMessages(['products' => __('A selected product is unavailable. Reload and try again.')]);
            }
            try {
                $printable = $catalog->printableCode($product, $barcode);
                $normalized = $printable['code'];
                $collision = $catalog->firstCollision($normalized, (int) $product->id);
                if ($collision) {
                    throw ValidationException::withMessages(['products' => __('Cannot print :product: its scanned value :code also belongs to product #:id (:other). Fix the identifiers before printing.', [
                        'product' => $product->name, 'code' => $normalized,
                        'id' => $collision->id, 'other' => $collision->name,
                    ])]);
                }
                $minimumWidth = $barcode->minimumLabelWidthMm($normalized);
                if ((float) $data['label_width'] < $minimumWidth) {
                    throw ValidationException::withMessages(['label_width' => __('Label width for :product must be at least :width mm for a scannable Code 39 barcode.', [
                        'product' => $product->name, 'width' => $minimumWidth,
                    ])]);
                }
                $svg = $barcode->svg($normalized);
            } catch (InvalidArgumentException $e) {
                throw ValidationException::withMessages(['products' => __('Cannot print Code 39 label for :product: :reason', [
                    'product' => $product->name, 'reason' => $e->getMessage(),
                ])]);
            }
            for ($i = 0; $i < (int) $copies; $i++) {
                $labels[] = ['product' => $product, 'value' => $normalized, 'source' => $printable['source'], 'svg' => $svg];
            }
        }

        return view('settings.barcode-labels', [
            'labels'=>$labels,
            'showPrice'=>$request->boolean('show_price'),
            'showName'=>$request->boolean('show_name'),
            'labelWidth'=>(float)$data['label_width'],
            'labelHeight'=>(float)$data['label_height'],
        ]);
    }
}
