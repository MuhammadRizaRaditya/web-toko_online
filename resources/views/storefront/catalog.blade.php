<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - {{ config('app.name', 'RIZWEAR') }}</title>
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
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter</h2>

                    <form method="GET" class="space-y-6">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <div class="space-y-2">
                                @foreach ($categories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="category" value="{{ $category->slug }}"
                                               {{ request('category') == $category->slug ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                    @if ($category->children->isNotEmpty())
                                        <div class="ml-4 space-y-1">
                                            @foreach ($category->children as $child)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="category" value="{{ $child->slug }}"
                                                           {{ request('category') == $child->slug ? 'checked' : '' }}
                                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <span class="ml-2 text-xs text-gray-600">{{ $child->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                            @php
                                $minPrice = $priceRange->min() ?? 0;
                                $maxPrice = $priceRange->max() ?? 1000000;
                            @endphp
                            <div class="space-y-2">
                                <input type="hidden" name="min_price" id="min_price" value="{{ request('min_price', $minPrice) }}">
                                <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price', $maxPrice) }}">
                                <div id="price-slider" class="h-2 bg-gray-200 rounded-lg"></div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                    <span>Rp {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Size Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                            <div class="space-y-2">
                                @foreach ($sizes as $size)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="size[]" value="{{ $size->id }}"
                                               {{ in_array($size->id, (array)request('size', [])) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $size->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Color Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warna</label>
                            <div class="space-y-2">
                                @foreach ($colors as $color)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="color[]" value="{{ $color->id }}"
                                               {{ in_array($color->id, (array)request('color', [])) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                                            <span class="w-4 h-4 rounded-full mr-2 border border-gray-300"
                                                  style="background-color: {{ $color->hex_code }}"></span>
                                            {{ $color->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- In Stock Only -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="in_stock" value="1"
                                       {{ request('in_stock') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Hanya yang tersedia</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-2 pt-4 border-t border-gray-200">
                            <button type="submit" class="flex-1 btn btn-primary btn-sm">Terapkan</button>
                            <a href="{{ route('catalog.index') }}" class="btn btn-outline btn-sm">Reset</a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Header with Sort -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Katalog Produk</h1>
                        <p class="text-gray-500">{{ $products->total() }} produk ditemukan</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label for="sort" class="text-sm text-gray-700">Urutkan:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                @if ($products->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada produk ditemukan</h2>
                        <p class="text-gray-500 mb-4">Coba ubah filter atau kata kunci pencarian Anda</p>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">Reset Filter</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($products as $product)
                            <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <a href="{{ route('product.show', $product) }}" class="block">
                                    <div class="relative aspect-square bg-gray-100 overflow-hidden">
                                        @php
                                            $cardImage = $product->images->firstWhere('is_primary') ?? $product->images->first();
                                        @endphp
                                        @if ($cardImage)
                                            <img src="{{ asset('storage/' . $cardImage->path) }}"
                                                 alt="{{ $cardImage->alt_text ?: $product->name }}"
                                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        @php
                                            $activeV = $product->variants->where('is_active', true);
                                            $discount = $activeV->max('discount_percent');
                                            $isOutOfStock = ! $activeV->contains(fn ($v) => $v->available_stock > 0);
                                        @endphp
                                        <div class="absolute top-2 left-2 flex flex-col gap-1 items-start">
                                            @if ($product->is_featured)
                                                <span class="bg-amber-500 text-white text-xs px-2 py-1 rounded font-medium">Unggulan</span>
                                            @endif
                                            @if ($discount)
                                                <span class="bg-red-600 text-white text-xs px-2 py-1 rounded font-semibold">-{{ $discount }}%</span>
                                            @endif
                                            @if ($isOutOfStock)
                                                <span class="bg-gray-800 text-white text-xs px-2 py-1 rounded font-medium">Habis</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                                            {{ $product->category?->name ?? 'Uncategorized' }}
                                        </p>
                                        <h3 class="font-medium text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                @php
                                                    $minPrice = $product->variants->where('is_active', true)->min('price');
                                                    $maxPrice = $product->variants->where('is_active', true)->max('price');
                                                    $struckCompare = $product->variants->where('is_active', true)
                                                        ->where('compare_at_price', '>=', $maxPrice)
                                                        ->max('compare_at_price');
                                                @endphp
                                                @if ($struckCompare && $struckCompare > $maxPrice)
                                                    <span class="text-sm text-gray-400 line-through mr-1.5">Rp {{ number_format($struckCompare, 0, ',', '.') }}</span>
                                                @endif
                                                @if ($minPrice !== $maxPrice)
                                                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($minPrice, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">- Rp {{ number_format($maxPrice, 0, ',', '.') }}</span></span>
                                                @else
                                                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                {{ $product->variants->where('is_active', true)->count() }} varian
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
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
</body>
</html>