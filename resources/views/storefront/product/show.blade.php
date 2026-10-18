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
                    <a href="{{ route('catalog.index') }}" class="relative text-gray-700 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="sr-only">Keranjang</span>
                        @if (cartItemCount())
                            <span class="absolute -top-1 right-2 bg-red-600 text-white text-xs rounded w-4 h-4 flex items-center justify-center">{{ cartItemCount() }}</span>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                        <a href="{{ route('logout') }}" class="text-sm text-gray-700 hover:text-gray-900" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        @if ($product->status !== 'active')
            <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-md text-amber-800 text-sm">
                <strong>Mode pratinjau admin:</strong> produk ini berstatus
                {{ $product->status === 'draft' ? 'Draft' : 'Diarsipkan' }} dan belum tampil untuk pembeli.
            </div>
        @endif

        <div class="grid lg:grid-cols-2 gap-12 items-start">
            <!-- Product Images -->
            <div>
                @php
                    $orderedImages = $product->images->sortBy([['is_primary', 'desc'], ['sort_order', 'asc']])->values();
                    $mainImage = $orderedImages->firstWhere('is_primary') ?? $orderedImages->first();
                @endphp

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-4">
                    @if ($mainImage)
                        <img id="mainImage" src="{{ asset('storage/' . $mainImage->path) }}"
                             alt="{{ $mainImage->alt_text ?: $product->name }}"
                             class="w-full h-[420px] object-cover bg-gray-100">
                    @else
                        <div class="w-full h-[420px] flex items-center justify-center text-gray-400 bg-gray-100">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                @if ($orderedImages->count() > 1)
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ($orderedImages as $image)
                            <button type="button" class="thumb border-2 {{ $image->id === $mainImage?->id ? 'border-blue-600' : 'border-transparent' }} rounded-lg overflow-hidden"
                                    data-full="{{ asset('storage/' . $image->path) }}"
                                    onclick="selectThumb(this)">
                                <img src="{{ asset('storage/' . $image->path) }}" alt=""
                                     class="w-full h-20 object-cover bg-gray-100">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if ($product->brand)
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $product->brand }}</span>
                    @endif
                    @foreach (($product->labels ?? []) as $label)
                        @if (isset(\App\Models\Product::LABEL_OPTIONS[$label]))
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $label === 'promo' ? 'bg-red-100 text-red-700' : ($label === 'best_seller' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ \App\Models\Product::LABEL_OPTIONS[$label] }}
                            </span>
                        @endif
                    @endforeach
                    @if ($product->is_featured)
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Unggulan</span>
                    @endif
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                @php
                    $activeVariants = $product->variants->where('is_active', true);
                    $minPrice = $activeVariants->min('price');
                    $maxPrice = $activeVariants->max('price');
                @endphp

                <!-- Price -->
                <div class="mb-6">
                    <div class="flex items-end gap-3 flex-wrap">
                        <p class="text-3xl font-semibold text-red-600" id="priceDisplay">Rp {{ number_format($minPrice ?? 0, 0, ',', '.') }}</p>
                        <p class="text-lg text-gray-400 line-through hidden" id="compareDisplay"></p>
                        <span class="text-sm font-semibold text-white bg-red-600 px-2 py-0.5 rounded hidden" id="discountBadge"></span>
                    </div>
                    @if ($minPrice !== $maxPrice && $minPrice)
                        <p class="text-xs text-gray-500 mt-1">Rentang harga: Rp {{ number_format($minPrice, 0, ',', '.') }} – Rp {{ number_format($maxPrice, 0, ',', '.') }} (tergantung varian)</p>
                    @endif

                    <!-- Stock status -->
                    <div id="stockBanner" class="mt-4 p-4 rounded-md text-sm hidden"></div>

                    <!-- Variant selection -->
                    @if ($activeVariants->isNotEmpty())
                        <div class="mt-5">
                            <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Pilih Varian (Ukuran & Warna)</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach ($activeVariants as $variant)
                                    @php
                                        $available = $variant->available_stock;
                                    @endphp
                                    <button type="button"
                                            class="variant-card text-left border-2 rounded-lg p-3 transition-all
                                                {{ $available <= 0 ? 'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed' : 'border-gray-200 hover:border-blue-400' }}"
                                            {{ $available <= 0 ? 'disabled' : '' }}
                                            data-id="{{ $variant->id }}"
                                            data-name="{{ $variant->size->name }} · {{ $variant->color->name }}"
                                            data-price="{{ $variant->price }}"
                                            data-compare="{{ $variant->compare_at_price && $variant->compare_at_price > $variant->price ? $variant->compare_at_price : '' }}"
                                            data-stock="{{ $available }}"
                                            data-threshold="{{ $variant->low_stock_threshold }}"
                                            onclick="selectVariant(this)">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="w-3 h-3 rounded-full border border-gray-300" style="background-color: {{ $variant->color->hex_code }};"></span>
                                            <span class="text-sm font-medium text-gray-800">{{ $variant->size->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $variant->color->name }}</span>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                                        <p class="text-xs {{ $available <= 0 ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                            {{ $available <= 0 ? 'Habis' : 'Stok: '.$available }}
                                        </p>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quantity -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="quantity">Jumlah</label>
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden w-fit">
                            <button type="button" onclick="changeQty(-1)" class="px-3 py-2 border-r border-gray-300 text-gray-600 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $initialVariant?->available_stock ?: 1 }}"
                                   class="w-20 px-3 py-2 text-center focus:outline-none">
                            <button type="button" onclick="changeQty(1)" class="px-3 py-2 border-l border-gray-300 text-gray-600 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500" id="stockInfo"></p>
                    </div>

                    <!-- Purchase (fitur dalam pengembangan) -->
                    <div class="mt-6 p-4 bg-gray-100 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Keranjang belanja dan checkout sedang dalam pengembangan. Silakan pilih varian dan jumlah — pesanan akan segera bisa diproses.
                        </p>
                    </div>
                </div>

                <!-- Info singkat -->
                <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600 mb-8 border-t border-gray-200 pt-6">
                    <p><strong>Kategori:</strong> {{ $product->category?->name ?? '-' }}</p>
                    @if ($product->brand)<p><strong>Merek:</strong> {{ $product->brand }}</p>@endif
                    @if ($product->material)<p><strong>Bahan:</strong> {{ $product->material }}</p>@endif
                    @if ($product->fit_style)<p><strong>Model:</strong> {{ $product->fit_style }}</p>@endif
                    @if ($product->weight_grams)<p><strong>Berat:</strong> {{ $product->weight_grams }} gram</p>@endif
                    <p><strong>Varian tersedia:</strong> {{ $activeVariants->count() }}</p>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Deskripsi</h2>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        @if ($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <em class="text-gray-400">Tidak ada deskripsi</em>
                        @endif
                    </div>
                </div>

                @if ($product->size_chart)
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Panduan Ukuran</h2>
                        <div class="text-sm text-gray-700 whitespace-pre-line bg-white border border-gray-200 rounded-lg p-4">{{ $product->size_chart }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Related products -->
        @if ($relatedProducts->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Produk Lainnya</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $related)
                        @php
                            $relatedImage = $related->images->firstWhere('is_primary') ?? $related->images->first();
                            $relatedMin = $related->variants->where('is_active', true)->min('price');
                        @endphp
                        <a href="{{ route('product.show', $related) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                @if ($relatedImage)
                                    <img src="{{ asset('storage/'.$relatedImage->path) }}" alt="{{ $related->name }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="text-xs text-gray-500 uppercase">{{ $related->category?->name ?? '' }}</p>
                                <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mt-0.5">{{ $related->name }}</h3>
                                <p class="text-sm font-bold text-gray-900 mt-1">Rp {{ number_format($relatedMin ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
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

    <script>
        const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

        function selectThumb(el) {
            document.getElementById('mainImage').src = el.dataset.full;
            document.querySelectorAll('.thumb').forEach(t => t.classList.remove('border-blue-600'));
            document.querySelectorAll('.thumb').forEach(t => t.classList.add('border-transparent'));
            el.classList.add('border-blue-600');
            el.classList.remove('border-transparent');
        }

        function formatPrice() {
            const card = document.querySelector('.variant-card.is-selected');
            const priceEl = document.getElementById('priceDisplay');
            const compareEl = document.getElementById('compareDisplay');
            const badgeEl = document.getElementById('discountBadge');

            if (!card) return;

            const price = Number(card.dataset.price);
            const compare = card.dataset.compare ? Number(card.dataset.compare) : null;

            priceEl.textContent = rp(price);

            if (compare && compare > price) {
                compareEl.textContent = rp(compare);
                compareEl.classList.remove('hidden');
                const pct = Math.round(((compare - price) / compare) * 100);
                badgeEl.textContent = '-' + pct + '%';
                badgeEl.classList.remove('hidden');
            } else {
                compareEl.classList.add('hidden');
                badgeEl.classList.add('hidden');
            }
        }

        function formatStock() {
            const card = document.querySelector('.variant-card.is-selected');
            const banner = document.getElementById('stockBanner');
            const info = document.getElementById('stockInfo');
            const qty = document.getElementById('quantity');

            if (!card) return;

            const stock = Number(card.dataset.stock);
            const threshold = Number(card.dataset.threshold || 5);

            qty.max = stock > 0 ? stock : 1;
            if (Number(qty.value) > stock) qty.value = stock > 0 ? stock : 1;
            if (stock <= 0) qty.value = 1;

            banner.classList.remove('hidden', 'bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700',
                'bg-yellow-100', 'border-yellow-500', 'text-yellow-700', 'bg-green-50', 'border-green-500', 'text-green-700');
            banner.classList.add('border-l-4');

            if (stock <= 0) {
                banner.classList.add('bg-red-100', 'border-red-500', 'text-red-700');
                banner.innerHTML = '<strong>Habis</strong><br>Varian ini sedang habis. Silakan pilih varian lain.';
            } else if (stock <= threshold) {
                banner.classList.add('bg-yellow-100', 'border-yellow-500', 'text-yellow-700');
                banner.innerHTML = '<strong>Stok Terbatas</strong><br>Hanya tersisa ' + stock + ' unit. Segera pesan!';
            } else {
                banner.classList.add('bg-green-50', 'border-green-500', 'text-green-700');
                banner.innerHTML = '<strong>Tersedia</strong><br>' + stock + ' unit siap dikirim.';
            }

            info.textContent = stock > 0 ? 'Maksimal ' + stock + ' unit per pemesanan' : 'Varian ini habis';
        }

        function selectVariant(el) {
            if (el.disabled) return;
            document.querySelectorAll('.variant-card').forEach(c => {
                c.classList.remove('is-selected', 'border-blue-600', 'bg-blue-50');
                c.classList.add('border-gray-200');
            });
            el.classList.add('is-selected', 'border-blue-600', 'bg-blue-50');
            el.classList.remove('border-gray-200');
            formatPrice();
            formatStock();
        }

        function changeQty(delta) {
            const qty = document.getElementById('quantity');
            const max = Number(qty.max || 1);
            let value = Number(qty.value || 1) + delta;
            if (value < 1) value = 1;
            if (value > max) value = max;
            qty.value = value;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const preselected = document.querySelector('.variant-card.is-selected')
                || document.querySelector('.variant-card:not([disabled])')
                || document.querySelector('.variant-card');
            if (preselected) selectVariant(preselected);
            formatPrice();
            formatStock();
        });
    </script>
</body>
</html>
