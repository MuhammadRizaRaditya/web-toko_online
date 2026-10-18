<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $canPreview = auth()->user()?->hasAnyRole(['admin', 'owner']) ?? false;

        abort_if($product->status !== 'active' && ! $canPreview, 404);

        $product->load([
            'category',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'variants' => fn ($q) => $q->where('is_active', true)
                ->with(['size', 'color'])
                ->orderBy('price'),
        ]);

        // Varian awal yang ditampilkan: yang ready stock, kalau tidak ada yang pertama
        $initialVariant = $product->variants
            ->sortByDesc(fn ($v) => $v->available_stock > 0 ? 1 : 0)
            ->first();

        // Related products from same category
        $relatedProducts = Product::where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('variants', fn ($q) => $q->where('is_active', true)->whereRaw('stock_on_hand - stock_reserved > 0'))
            ->with(['images', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('storefront.product.show', compact(
            'product',
            'initialVariant',
            'relatedProducts'
        ));
    }

    public function getVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
        ]);

        $variant = $product->variants()
            ->where('size_id', $validated['size_id'])
            ->where('color_id', $validated['color_id'])
            ->where('is_active', true)
            ->first();

        if (! $variant) {
            return response()->json(['error' => 'Varian tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $variant->id,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'compare_at_price' => $variant->compare_at_price,
            'stock_available' => $variant->available_stock,
            'is_low_stock' => $variant->is_low_stock,
        ]);
    }
}
