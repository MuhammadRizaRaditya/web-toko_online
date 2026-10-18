<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Throwable;

class ProductController extends Controller
{
    private const IMAGE_RULE = 'file|image|mimes:jpeg,jpg,png,webp|max:2048';

    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->withCount('variants')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('slug', 'like', "%{$request->search}%")
                    ->orWhere('brand', 'like', "%{$request->search}%")
                    ->orWhereHas('variants', fn ($qv) => $qv->where('sku', 'like', "%{$request->search}%"));
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
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $sizes = Size::where('is_active', true)->orderBy('sort_order')->get();
        $colors = Color::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'sizes', 'colors'));
    }

    public function store(Request $request)
    {
        $this->normalizeVariantSkuInput($request);

        $data = $request->validate($this->productRules([
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'status' => 'nullable|in:draft,active,archived',
            'price' => 'required|numeric|min:0|max:9999999999',
            'compare_at_price' => 'nullable|numeric|min:0|max:9999999999|gte:price',
            'cost_price' => 'nullable|numeric|min:0|max:9999999999',
            'primary_image' => 'required|'.self::IMAGE_RULE,
            'images' => 'nullable|array|max:8',
            'images.*' => self::IMAGE_RULE,
            'variants' => 'required|array|min:1|max:100',
            'variants.*.size_id' => 'required|integer|exists:sizes,id',
            'variants.*.color_id' => 'required|integer|exists:colors,id',
            'variants.*.sku' => 'nullable|string|max:100|regex:/^[A-Za-z0-9\-_]+$/|unique:product_variants,sku',
            'variants.*.price' => 'nullable|numeric|min:0|max:9999999999',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0|max:9999999999',
            'variants.*.stock' => 'required|integer|min:0|max:1000000',
            'variants.*.is_active' => 'nullable|boolean',
            'intent' => 'nullable|in:draft,active',
        ]));

        $this->assertVariantRowsAreConsistent($data);

        $status = match ($request->input('intent')) {
            'active' => 'active',
            'draft' => 'draft',
            default => $data['status'] ?? 'draft',
        };

        $slug = $this->resolveUniqueSlug($data['slug'] ?? null, $data['name']);
        $variantsPayload = $this->prepareVariantsPayload($data);

        $tmpPaths = [];
        $movedPaths = [];
        $productId = null;

        try {
            $tmpPaths[] = $data['primary_image']->store('products/tmp', 'public');
            foreach ($data['images'] ?? [] as $galleryFile) {
                $tmpPaths[] = $galleryFile->store('products/tmp', 'public');
            }

            $product = DB::transaction(function () use ($data, $slug, $status, $request, $variantsPayload, &$movedPaths, &$productId, $tmpPaths) {
                $product = Product::create([
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'slug' => $slug,
                    'brand' => $data['brand'] ?? null,
                    'material' => $data['material'] ?? null,
                    'fit_style' => $data['fit_style'] ?? null,
                    'weight_grams' => $data['weight_grams'] ?? null,
                    'size_chart' => $data['size_chart'] ?? null,
                    'description' => $data['description'] ?? null,
                    'labels' => $data['labels'] ?? null,
                    'status' => $status,
                    'is_featured' => $request->boolean('is_featured'),
                    'cost_price' => $data['cost_price'] ?? null,
                    'created_by' => $request->user()->id,
                ]);

                $productId = $product->id;

                $imageEntries = array_merge(
                    [['tmp' => $tmpPaths[0], 'is_primary' => true, 'alt' => 'Foto utama: '.$product->name]],
                    collect($tmpPaths)->slice(1)->values()->map(fn ($path, $i) => [
                        'tmp' => $path,
                        'is_primary' => false,
                        'alt' => $data['name'].' - foto '.($i + 2),
                    ])->all(),
                );

                foreach ($imageEntries as $index => $entry) {
                    $filename = Str::random(16).'.'.pathinfo($entry['tmp'], PATHINFO_EXTENSION);
                    $destination = "products/{$product->id}/{$filename}";

                    Storage::disk('public')->move($entry['tmp'], $destination);
                    $movedPaths[] = $destination;

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $destination,
                        'alt_text' => $entry['alt'],
                        'sort_order' => $index,
                        'is_primary' => $entry['is_primary'],
                    ]);
                }

                foreach ($variantsPayload as $row) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'size_id' => $row['size_id'],
                        'color_id' => $row['color_id'],
                        'sku' => $row['sku'],
                        'price' => $row['price'],
                        'compare_at_price' => $row['compare_at_price'],
                        'stock_on_hand' => $row['stock'],
                        'stock_reserved' => 0,
                        'low_stock_threshold' => $row['low_stock_threshold'],
                        'is_active' => $row['is_active'],
                    ]);

                    if ($row['stock'] > 0) {
                        $variant->stockMovements()->create([
                            'movement_type' => 'purchase_receive',
                            'quantity_delta' => $row['stock'],
                            'reason' => 'Initial stock on product creation',
                            'actor_user_id' => $request->user()->id,
                            'reference_type' => 'product_variant',
                            'reference_id' => $variant->id,
                        ]);
                    }
                }

                return $product;
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($movedPaths);
            if ($productId !== null) {
                Storage::disk('public')->deleteDirectory('products/'.$productId);
            }
            Storage::disk('public')->delete($tmpPaths);

            report($e);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan produk: '.$e->getMessage()]);
        } finally {
            Storage::disk('public')->delete($tmpPaths);
        }

        $label = $status === 'active' ? 'Aktif' : 'Draft';

        return redirect()->route('admin.products.edit', $product)
            ->with('success', "Produk berhasil disimpan sebagai {$label}. Silakan lengkapi gambar/varian tambahan jika diperlukan.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants.size', 'variants.color']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load(['images', 'variants.size', 'variants.color']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate($this->productRules([
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$product->id,
            'status' => 'required|in:draft,active,archived',
            'cost_price' => 'nullable|numeric|min:0|max:9999999999',
        ]));

        $data['slug'] = $this->resolveUniqueSlug($data['slug'] ?? null, $data['name'], $product->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['labels'] = $data['labels'] ?? null;

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $imageDirectory = 'products/'.$product->id;

        $product->delete();

        Storage::disk('public')->deleteDirectory($imageDirectory);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    // Image upload
    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|'.self::IMAGE_RULE,
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
        ]);

        $image = $request->file('image');
        $path = $image->store('products/'.$product->id, 'public');

        // Resize and optimize when an image driver is available; otherwise keep the original file.
        if (extension_loaded('gd') || extension_loaded('imagick')) {
            try {
                $img = Image::read($image->getRealPath());
                $img->scaleDown(width: 1200);
                $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);
                $img->toWebp(80)->save(storage_path('app/public/'.$webpPath));

                if ($webpPath !== $path) {
                    Storage::disk('public')->delete($path);
                    $path = $webpPath;
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        // If set as primary, unset others
        if ($request->boolean('is_primary')) {
            $product->images()->update(['is_primary' => false]);
        }

        $product->images()->create([
            'path' => $path,
            'alt_text' => $request->alt_text,
            'sort_order' => ($product->images()->max('sort_order') ?? -1) + 1,
            'is_primary' => $request->boolean('is_primary', false),
        ]);

        return redirect()->back()->with('success', 'Gambar berhasil diunggah.');
    }

    public function deleteImage(Product $product, $imageId)
    {
        $image = $product->images()->findOrFail($imageId);

        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
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

    /**
     * Normalisasi input SKU varian sebelum validasi (trim + huruf besar),
     * supaya rule unique dan duplikat ganda konsisten dengan penyimpanan.
     */
    private function normalizeVariantSkuInput(Request $request): void
    {
        $variants = $request->input('variants');

        if (! is_array($variants)) {
            return;
        }

        foreach ($variants as $index => $row) {
            if (is_array($row) && array_key_exists('sku', $row) && is_string($row['sku'])) {
                $variants[$index]['sku'] = strtoupper(trim($row['sku']));
            }
        }

        $request->merge(['variants' => $variants]);
    }

    /**
     * Rules dasar produk, bisa ditambah rule khusus (store/update).
     */
    private function productRules(array $extra = []): array
    {
        return array_merge([
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:20000',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'fit_style' => 'nullable|string|max:100',
            'weight_grams' => 'nullable|integer|min:0|max:100000',
            'size_chart' => 'nullable|string|max:10000',
            'labels' => 'nullable|array|max:'.count(Product::LABEL_OPTIONS),
            'labels.*' => 'string|in:'.implode(',', array_keys(Product::LABEL_OPTIONS)),
            'is_featured' => 'nullable|boolean',
        ], $extra);
    }

    /**
     * Cek ganda dalam payload: kombinasi varian dan SKU ganda.
     */
    private function assertVariantRowsAreConsistent(array $data): void
    {
        $variants = $data['variants'];

        $combos = array_map(fn ($row) => $row['size_id'].'|'.$row['color_id'], $variants);
        if (count($combos) !== count(array_unique($combos))) {
            throw ValidationException::withMessages([
                'variants' => 'Terdapat kombinasi ukuran dan warna yang ganda. Setiap kombinasi hanya boleh muncul sekali.',
            ]);
        }

        $skus = array_values(array_filter(array_column($variants, 'sku')));
        if (count($skus) !== count(array_unique($skus))) {
            throw ValidationException::withMessages([
                'variants' => 'Terdapat SKU yang ganda pada daftar varian.',
            ]);
        }

        foreach ($variants as $index => $row) {
            $effectivePrice = (float) ($row['price'] ?? $data['price']);
            $effectiveCompare = $row['compare_at_price'] ?? ($data['compare_at_price'] ?? null);

            if ($effectiveCompare !== null && (float) $effectiveCompare < $effectivePrice) {
                throw ValidationException::withMessages([
                    "variants.{$index}.compare_at_price" => 'Harga sebelum diskon tidak boleh lebih kecil daripada harga jual varian.',
                ]);
            }
        }
    }

    /**
     * Slug: gunakan yang diisi admin, atau generate dari nama. Selalu unik.
     */
    private function resolveUniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $slug = $slug ?: Str::slug($name);

        if ($slug === '') {
            $slug = 'produk';
        }

        $base = $slug;
        $suffix = 2;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /**
     * Siapkan payload varian final: SKU auto-generate bila kosong,
     * harga fallback ke harga dasar produk, ambang stok default.
     */
    private function prepareVariantsPayload(array $data): array
    {
        $usedSku = [];

        return array_map(function ($row) use ($data, &$usedSku) {
            $sku = Str::upper((string) ($row['sku'] ?? ''));

            if ($sku === '') {
                $sku = $this->generateSku($data, $row, $usedSku);
            }

            $usedSku[$sku] = true;

            return [
                'size_id' => (int) $row['size_id'],
                'color_id' => (int) $row['color_id'],
                'sku' => $sku,
                'price' => $row['price'] ?? $data['price'],
                'compare_at_price' => $row['compare_at_price'] ?? ($data['compare_at_price'] ?? null),
                'stock' => (int) $row['stock'],
                'low_stock_threshold' => (int) ($row['low_stock_threshold'] ?? 5),
                'is_active' => ! array_key_exists('is_active', $row) || filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN),
            ];
        }, array_values($data['variants']));
    }

    private function generateSku(array $data, array $row, array $usedSku): string
    {
        $productCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', Str::substr(Str::slug($data['name']), 0, 10))) ?: 'PRD';
        $size = Size::find($row['size_id']);
        $color = Color::find($row['color_id']);

        $sizeCode = Str::substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $size?->name)), 0, 12) ?: 'SZ';
        $colorCode = Str::substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $color?->name)), 0, 12) ?: 'CL';

        $base = "{$productCode}-{$sizeCode}-{$colorCode}";
        $sku = $base;
        $suffix = 2;

        while (
            ProductVariant::where('sku', $sku)->exists()
            || isset($usedSku[$sku])
        ) {
            $sku = $base.'-'.$suffix++;
        }

        return $sku;
    }
}
