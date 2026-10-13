<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Storefront\CatalogController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Storefront\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // POS Routes
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/record-sale', [PosController::class, 'recordSale'])->name('pos.recordSale');
    Route::get('/pos/search-products', [PosController::class, 'searchProducts'])->name('pos.searchProducts');
    Route::get('/pos/search-variant/{product}', [PosController::class, 'searchVariant'])->name('pos.searchVariant');
});

require __DIR__.'/auth.php';

// ===================== STOREFRONT ROUTES =====================
Route::get('/produk', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/kategori/{category:slug}', [CatalogController::class, 'show'])->name('catalog.category');
Route::get('/produk/{product:slug}', [StorefrontProductController::class, 'show'])->name('product.show');
Route::get('/api/product/{product:slug}/variant', [StorefrontProductController::class, 'getVariant'])->name('product.variant.api');

// ===================== ADMIN ROUTES =====================
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {

    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/images', [AdminProductController::class, 'uploadImage'])->name('products.uploadImage');
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::put('products/{product}/images/reorder', [AdminProductController::class, 'reorderImages'])->name('products.reorderImages');
    Route::put('products/{product}/images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.setPrimaryImage');

    // Categories
    Route::resource('categories', CategoryController::class);
    Route::put('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Sizes
    Route::resource('sizes', SizeController::class);
    Route::put('sizes/reorder', [SizeController::class, 'reorder'])->name('sizes.reorder');

    // Colors
    Route::resource('colors', ColorController::class);

    // Variants (nested under products or standalone)
    Route::resource('variants', VariantController::class);
    Route::post('variants/{variant}/adjust-stock', [VariantController::class, 'adjustStock'])->name('variants.adjustStock');
    Route::get('variants/{variant}/stock-history', [VariantController::class, 'stockHistory'])->name('variants.stockHistory');
});

// ===================== TEST ROUTES ===================
if (app()->environment('local', 'testing')) {
    Route::middleware(['auth'])->group(function () {
        Route::get('/api/orders/{order}', function (\App\Models\Order $order) {
            return response()->json($order);
        })->can('view', 'order');

        Route::put('/api/addresses/{address}', function (\App\Models\Address $address) {
            return response()->json($address);
        })->can('update', 'address');

        Route::post('/api/stock/{variant}/adjust', function (\App\Models\ProductVariant $variant) {
            return response()->json(['success' => true]);
        })->can('adjust', 'variant');

        Route::post('/api/products', function () {
            return response()->json(['success' => true], 201);
        })->middleware('permission:products.create');

        Route::get('/api/cart/{cart}', function (\App\Models\Cart $cart) {
            return response()->json($cart);
        })->can('view', 'cart');

        Route::middleware(['role:admin,owner'])->group(function () {
            Route::get('/admin/test', fn() => response()->json(['admin' => true]));
            Route::get('/admin/orders', fn() => response()->json(['orders' => true]));
        });
    });
}