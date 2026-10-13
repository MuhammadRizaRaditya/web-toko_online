<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - RIZWEAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-gray-900">{{ config('app.name', 'RIZWEAR') }}</a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="{{ route('catalog.index') }}" class="text-gray-700 hover:text-gray-900 font-medium">Katalog</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 font-medium">Tentang</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('logout') }}" class="text-sm text-gray-700 hover:text-gray-900" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16m0 0v6m0-6H4"/>
                        </svg>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-4 gap-6">
            <!-- POS Search & Product List -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Cari Produk</h2>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <input type="text" id="posSearch" placeholder="Cari produk/barcode/SKU..."
                               class="w-full px-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10">
                    </div>
                    <div id="posSearchResults" class="mt-3 max-h-80 overflow-y-auto hidden bg-white rounded-lg border border-gray-200 p-2 shadow-lg">
                        <!-- Results loaded dynamically -->
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Daftar Produk</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($products as $product)
                            <div class="cursor-pointer select-variant-product" data-product-id="{{ $product->id }}"
                                 data-product-name="{{ $product->name }}"
                                 style="transition: all 0.2s;">
                                <div class="p-3 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $product->name }}</p>
                                    <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ Str::limit($product->name, 30) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Cart & Summary -->
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Keranjang Belanja</h2>
                    <div id="posCartItems" class="space-y-3">
                        <!-- Cart items loaded dynamically -->
                        <p class="text-gray-500 text-center py-8">Cari produk untuk menambah keranjang</p>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Subtotal</span>
                            <span id="posSubtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Biaya COD</span>
                            <span id="posCodFee">Rp {{ number_format($posSettings->cod_fee ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-medium text-gray-900">
                            <span>Total</span>
                            <span id="posTotal">Rp {{ number_format(($posSettings->cod_fee ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Pesanan</h2>
                    <form id="posOrderForm">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pelanggan</label>
                                <input type="text" name="customer_name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No Telepon</label>
                                <input type="tel" name="customer_phone"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea name="customer_address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method" value="cod"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="font-medium">Bayar di Tempat (COD)</span>
                                        <span class="text-sm text-gray-500">Only Jawa</span>
                                    </div>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method" value="transfer"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                                    <div class="ml-3">
                                        <span class="font-medium">Transfer Bank</span>
                                        <span class="text-sm text-gray-500">BNI, BCA, Mandiri</span>
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Biaya COD: Rp {{ number_format($posSettings->cod_fee ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <button type="submit" wire:click="processPayment"
                                class="w-full btn btn-primary mt-4 py-3 px-6">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Proses Pesanan</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

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
                        <li><a href="#" class="hover:text-white">Bantuan</a></li>
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
                &copy {{ date('Y') }} {{ config('app.name', 'RIZWEAR') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @vite(['resources/js/app.js'])

    <script>
        // POS Search Functionality
        const posSearchInput = document.getElementById('posSearch');
        const posSearchResults = document.getElementById('posSearchResults');

        posSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                posSearchResults.classList.add('hidden');
                posSearchResults.innerHTML = '';
                return;
            }

            fetch('/pos/search-products?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        posSearchResults.classList.add('hidden');
                        posSearchResults.innerHTML = '<p class="text-gray-500 text-center py-4">Produk tidak ditemukan</p>';
                        return;
                    }

                    let html = '';
                    data.forEach(item => {
                        html += `
                            <div class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer select-variant-product"
                                 data-product-id="${item.id}"
                                 data-product-name="${item.name}"
                                 style="background: #f8f9fa;">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">${item.name}</p>
                                    <p class="text-xs text-gray-500">${item.sku}</p>
                                </div>
                            </div>
                        `;
                    });

                    posSearchResults.innerHTML = html;
                    posSearchResults.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error searching products:', error);
                    posSearchResults.classList.add('hidden');
                });
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!posSearchInput.contains(e.target) && !posSearchResults.contains(e.target)) {
                posSearchResults.classList.add('hidden');
                posSearchResults.innerHTML = '';
            }
        });

        // Variant selection
        document.querySelectorAll('.select-variant-product').forEach(element => {
            element.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;

                // Fetch variant details
                fetch(`/pos/search-variant?product_id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        // Pre-fill the form with variant details
                        // ... (would need to integrate with the form)
                        alert('Produk dipilih: ' + data.variant.sku + ' - Rp ' + data.variant.price);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    </script>
</body>
</html>