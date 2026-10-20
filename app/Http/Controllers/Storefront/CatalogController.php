<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('status', 'active')
            ->with(['category', 'images', 'variants.size', 'variants.color'])
            ->whereHas('variants', function ($q) {
                $q->where('is_active', true);
            });

        // Category filter
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $childIds = $category->children()->pluck('id')->toArray();
                $query->where(function ($q) use ($category, $childIds) {
                    $q->where('category_id', $category->id)
                        ->orWhereIn('category_id', $childIds);
                });
            }
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->whereHas('variants', fn ($q) => $q->where('price', '>=', $request->min_price));
        }
        if ($request->filled('max_price')) {
            $query->whereHas('variants', fn ($q) => $q->where('price', '<=', $request->max_price));
        }

        // Size filter
        if ($request->filled('size')) {
            $sizeIds = is_array($request->size) ? $request->size : [$request->size];
            $query->whereHas('variants', fn ($q) => $q->whereIn('size_id', $sizeIds));
        }

        // Color filter
        if ($request->filled('color')) {
            $colorIds = is_array($request->color) ? $request->color : [$request->color];
            $query->whereHas('variants', fn ($q) => $q->whereIn('color_id', $colorIds));
        }

        // Availability filter
        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', function ($q) {
                $q->where('is_active', true)
                    ->whereRaw('stock_on_hand - stock_reserved > 0');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->withMin('variants', 'price as min_price')->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->withMin('variants', 'price as min_price')->orderBy('min_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
                $query->withCount('variants')->orderBy('variants_count', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(24)->withQueryString();

        // Get filter options
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $sizes = Size::where('is_active', true)
            ->whereHas('variants', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->get();

        $colors = Color::where('is_active', true)
            ->whereHas('variants', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        // Price range for slider
        $priceRange = Product::where('status', 'active')
            ->whereHas('variants', fn ($q) => $q->where('is_active', true))
            ->with(['variants' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->flatMap->variants
            ->pluck('price')
            ->filter();

        return view('storefront.catalog', compact(
            'products',
            'categories',
            'sizes',
            'colors',
            'priceRange'
        ));
    }

    public function show(Category $category)
    {
        return $this->index(request()->merge(['category' => $category->slug]));
    }
}
