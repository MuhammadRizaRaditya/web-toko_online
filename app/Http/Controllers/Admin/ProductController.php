<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->withCount('variants')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('slug', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['created_by'] = auth()->id();

        $product = Product::create($validated);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil dibuat. Silakan tambahkan varian dan gambar.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants.size', 'variants.color']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load(['images', 'variants.size', 'variants.color']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $product->update($validated);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    // Image upload
    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
        ]);

        $image = $request->file('image');
        $filename = time().'_'.Str::random(10).'.'.$image->extension();
        $path = $image->storeAs('products/'.$product->id, $filename, 'public');

        // Resize and optimize using Intervention Image
        $img = Image::read($image->getRealPath());
        $img->scale(width: 1200);
        $img->toWebp(80)->save(storage_path('app/public/'.$path));

        // If set as primary, unset others
        if ($request->boolean('is_primary')) {
            $product->images()->update(['is_primary' => false]);
        }

        $product->images()->create([
            'path' => $path,
            'alt_text' => $request->alt_text,
            'sort_order' => $product->images()->max('sort_order') + 1,
            'is_primary' => $request->boolean('is_primary', false),
        ]);

        return redirect()->back()->with('success', 'Gambar berhasil diunggah.');
    }

    public function deleteImage(Product $product, $imageId)
    {
        $image = $product->images()->findOrFail($imageId);
        $path = storage_path('app/public/'.$image->path);
        if (file_exists($path)) {
            unlink($path);
        }
        $image->delete();

        return redirect()->back()->with('success', 'Gambar berhasil dihapus.');
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:product_images,id',
        ]);

        foreach ($request->image_ids as $index => $id) {
            $product->images()->where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage(Product $product, $imageId)
    {
        $product->images()->update(['is_primary' => false]);
        $product->images()->where('id', $imageId)->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Gambar utama diperbarui.');
    }
}
