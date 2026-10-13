<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - RIZWEAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-gray-900">{{ config('app.name', 'RIZWEAR') }}</a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="{{ route('catalog.index') }}" class="text-gray-700 hover:text-gray-900 font-medium">Katalog</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 font-medium">Tentang Kami</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 font-medium">Kontak</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="relative text-gray-700 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="sr-only">Keranjang</span>
                        @if (cartItemCount)
                            <span class="absolute -top-1 right-2 bg-red-600 text-white text-xs rounded w-4 h-4 flex items-center justify-center">{{ cartItemCount }}</span>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                        <a href="{{ route('logout') }}" class="text-sm text-gray-700 hover:text-gray-900" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16m0 0v6m0-6H4"/>
                            </svg>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid lg:grid-cols-2 gap-12 items-start">
            <!-- Product Images -->
            <div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($product->images as $index => $image)
                            <div class="relative border border-gray-200 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $image->path) }}"
                                     alt="{{ $image->alt_text }}"
                                     class="w-full h-48 object-cover"
                                     wire:click="$dispatch('selectImage', {{ $image->id }})">
                                @if ($image->is_primary)
                                    <div class="absolute top-1 left-1 bg-green-600 text-white text-xs px-2 py-1 rounded">Utama</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Thumbnails -->
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        @foreach ($product->images as $index => $image)
                            <div class="relative rounded-lg overflow-hidden bg-gray-100 cursor-pointer"
                                 wire:click="$dispatch('selectImage', {{ $image->id }})"
                                 @if ($image->is_primary)
                                     class="border-2 border-green-600"
                                 @endif
                                 >
                                <img src="{{ asset('storage/' . $image->path) }}"
                                     alt=""
                                     class="w-full h-24 object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Image Navigation -->
                @if ($product->images->count() > 1)
                    <div class="mt-6 flex items-center justify-between">
                        <button class="prev-btn text-gray-400 hover:text-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7M5 10l7-7 7 7"/>
                            </svg>
                        </button>
                        <button class="next-btn text-gray-400 hover:text-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7M19 11l-7 7 7 7"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="mb-8">
                    @php
                        $activeVariant = $product->variants->where('is_active', true)->first();
                        $minPrice = $product->variants->where('is_active', true)->min('price');
                        $maxPrice = $product->variants->where('is_active', true)->max('price');
                    @endphp
                    @if ($minPrice !== $maxPrice && $minPrice)
                        <p class="text-xl text-gray-500 mb-2">Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}</p>
                        <p class="text-3xl font-semibold text-red-600">Rp {{ number_format($activeVariant->price ?? $minPrice, 0, ',', '.') }}</p>
                    @else
                        <p class="text-xl text-gray-500 mb-2">Rp {{ number_format($minPrice ?? 0, 0, ',', '.') }}</p>
                        <p class="text-3xl font-semibold text-red-600">Rp {{ number_format($activeVariant->price ?? $minPrice ?? 0, 0, ',', '.') }}</p>
                    @endif
                </div>

                <!-- Stock Status -->
                @php
                    $variant = $activeVariant;
                    $totalStock = $variant?->stock_on_hand ?? 0;
                    $reservedStock = $variant?->stock_reserved ?? 0;
                    $availableStock = $totalStock - $reservedStock;
                    $isLowStock = $availableStock <= 5 && $availableStock > 0;
                    $isOutOfStock = $availableStock <= 0;
                @endphp
                @if ($isOutOfStock)
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 rounded-md text-red-700">
                        <p class="font-medium">Habis Dicari</p>
                        <p class="text-sm mt-1">Stok telah habis. Silakan tunggu stok kembali atau pilih produk lain.</p>
                    </div>
                @elseif ($isLowStock)
                    <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 rounded-md text-yellow-700">
                        <p class="font-medium">Stok Terbatas</p>
                        <p class="text-sm mt-1">Hanya tersisa {{ $available_stock }} unit. Segera checkout!</p>
                    </div>
                @endif

                <!-- Variant Selection -->
                @if ($product->variants->isNotEmpty())
                    <div class="mb-8">
                        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Pilih Varian</h2>
                        
                        <div class="grid grid-cols-2 gap-3" id="variantSelection">
                            @foreach ($product->variants as $variant)
                                <div 
                                    class="variant-item rounded-lg border-2 border-transparent transition-all duration-200 hover:border-blue-500 hover:bg-blue-50 focus:outline-none focus:border-blue-500"
                                    data-variant-id="{{ $variant->id }}"
                                    data-price="{{ $variant->price }}"
                                    data-sku="{{ $variant->sku }}"
                                    data-size="{{ $variant->size_id }}"
                                    data-color="{{ $variant->color_id }}"
                                    @if ($variant->is_active)
                                        class="bg-blue-50 border-blue-500 text-blue-800"
                                    @endif
                                >
                                    <div class="p-3">
                                        <p class="font-medium text-sm text-gray-800">{{ $variant->size->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $variant->color->name }}</p>
                                    </div>
                                    <p class="text-lg font-bold text-red-600">Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Size Selection (if multiple) -->
                @if ($product->variants->count() > 1)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                        <select name="size_id" id="sizeSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Ukuran</option>
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->size_id }}"
                                        {{ $activeVariant && $activeVariant->size_id == $variant->size_id ? 'selected' : '' }}
                                        data-price="{{ $variant->price }}"
                                        data-color="{{ $variant->color_id }}"
                                        style="background-color: {{$variant->color->hex_color}};">
                                        {{ $variant->size->name }} - Rp {{ number_format($variant->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Color Selection -->
                @if ($product->variants->count() > 1)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna</label>
                        <div class="grid grid-cols-4 gap-2" id="colorSelection">
                            @foreach ($product->variants as $variant)
                                <label class="flex flex-col items-center cursor-pointer select-variant"
                                       data-variant-id="{{ $variant->id }}"
                                       data-price="{{ $variant->price }}"
                                       style="background-color: {{$variant->color->hex_color}}; border: 2px solid {{$variant->color->is_active ? 'green' : 'transparent'}};"
                                       wire:click="$dispatch('selectVariant', {{ $variant->id }})">
                                    <span class="w-8 h-8 rounded-full mr-1 border border-gray-300"
                                          style="background-color: {{$variant->color->hex_color}};"></span>
                                    <span class="text-xs text-gray-600 capitalize">{{ $variant->color->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                        <button type="button" 
                                wire:click="decreaseQuantity"
                                class="px-3 py-1 border-y border-gray-300"
                                @if ($availableStock <= 0)
                                    class="pointer-events-none text-gray-300"
                                @endif
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <input type="number" name="quantity" id="quantity"
                               value="{{ min($availableStock, 1) }}" min="1"
                               max="{{ $availableStock > 0 ? $availableStock : 1 }}"
                               class="w-24 px-3 py-1 border-y text-center focus:outline-none"
                               @if ($availableStock <= 0)
                                   disabled
                                   class="text-gray-400 cursor-not-allowed"
                               @endif>
                        <button type="button"
                                wire:click="increaseQuantity"
                                class="px-3 py-1 border-y border-gray-300"
                                @if ($availableStock <= 0)
                                    class="pointer-events-none text-gray-300"
                                @endif
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <p class="px-3 py-1 text-sm text-gray-500">Tersedia: {{ $availableStock }}</p>
                    </div>
                </div>

                <!-- Add to Cart Button -->
                <div class="mb-6">
                    @if ($isOutOfStock)
                        <button disabled class="btn btn-lg btn-secondary w-full">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Habis Dicari</span>
                        </button>
                    @elseif ($isLowStock)
                        <button wire:click="addToCart" class="btn btn-lg btn-primary w-full">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Beli Sekarang</span>
                        </button>
                    @else
                        <button wire:click="addToCart" class="btn btn-lg btn-primary w-full">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Beli Sekarang</span>
                        </button>
                    @endif
                </div>

                <!-- Product Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {{ $product->description ?? '<em class="text-gray-400">Tidak ada deskripsi</em>' }}
                    </div>
                </div>

                <!-- Details -->
                <div class="grid grid-cols-2 gap-6 text-sm text-gray-600">
                    <div>
                        <p><strong>Kategori:</strong> {{ $product->category?->name ?? '-' }}</p>
                        @php
                            $statusColors = [
                                'draft' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'archived' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusLabels = [
                                'draft' => 'Draft',
                                'active' => 'Aktif',
                                'archived' => 'Diarsipkan',
                            ];
                        @endphp
                        <p><strong>Status:</strong> <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$product->status] }}">{{ $statusLabels[$product->status] }}</span></p>
                        <p><strong>Dibuat:</strong> {{ $product->created_at->format('d F Y') }}</p>
                    </div>
                    <div>
                        @php
                            $activeVariant = $product->variants->where('is_active', true)->first();
                            $totalStock = $activeVariant?->stock_on_hand ?? 0;
                            $reservedStock = $activeVariant?->stock_reserved ?? 0;
                            $availableStock = $totalStock - $reservedStock;
                        @endphp
                        <p><strong>Total Stok:</strong> {{ $totalStock }} unit</p>
                        <p><strong>Tersedia:</strong> {{ $availableStock }} unit</p>
                        @if ($reservedStock > 0)
                            <p><strong>Terreservasi:</strong> {{ $reservedStock }} unit</p>
                        @endif
                        <p><strong>Varian:</strong> {{ $product->variants->count() }} varian</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">{{ config('app.name', 'RIZWEAR') }}</h3>
                    <p class="text-sm">Fashion berkualitas untuk keseharian Anda.</p>
                </div>
                <div>
                    <h4 class="text-white font-medium mb-4">Navigasi</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white">Katalog</a></li>
                        <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-medium mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">Pengiriman</a></li>
                        <li><a href="#" class="hover:text-white">Pengembalian</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-medium mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li>email@rizwear.com</li>
                        <li>+62 812-3456-7890</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'RIZWEAR') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @vite(['resources/js/app.js'])

    <!-- Variant Selection JavaScript -->
    <script>
        // Initialize variant selection from server-side data
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial variant based on selected size/color
            const sizeSelect = document.getElementById('sizeSelect');
            const colorSelection = document.getElementById('colorSelection');
            const variantItems = document.querySelectorAll('.variant-item');
            
            // If size dropdown exists, set initial selection
            if (sizeSelect) {
                sizeSelect.addEventListener('change', function() {
                    const selectedSizeId = this.value;
                    const variant = variantItems[itemWithSize(selectedSizeId)];
                    if (variant) {
                        updateVariantDisplay(variant);
                    }
                });
            }
            
            // If color selection exists
            if (colorSelection) {
                colorSelection.addEventListener('click', function(e) {
                    if (e.target.closest('.select-variant')) {
                        const variantId = e.target.closest('.select-variant').dataset.variantId;
                        updateVariantDisplay(document.querySelector(`.variant-item[data-variant-id="${variantId}"]`));
                    }
                });
            }
            
            // Click on variant items
            variantItems.forEach(item => {
                item.addEventListener('click', function() {
                    updateVariantDisplay(this);
                });
            });
        });
        
        function itemWithSize(sizeId) {
            return Array.from(variantItems).findIndex(item => 
                item.dataset.size === sizeId
            );
        }
        
        function updateVariantDisplay(selectedItem) {
            // Remove active class from all items
            document.querySelectorAll('.variant-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-800');
            });
            
            // Add active class to selected
            selectedItem.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-800');
            
            // Update price and other details
            const price = selectedItem.dataset.price;
            const sku = selectedItem.dataset.sku;
            
            // Update price display
            if (priceElement) {
                priceElement.textContent = 'Rp ' + price.toLocaleString();
            }
            
            // Update SKU display
            if (skuElement) {
                skuElement.textContent = sku;
            }
        }
    </script>
</body>
</html>