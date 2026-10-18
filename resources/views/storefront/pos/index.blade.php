<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - {{ config('app.name', 'RIZWEAR') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'RIZWEAR') }}</a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 font-medium">Dashboard</a>
                        <a href="{{ route('catalog.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">Katalog</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">POS</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 font-medium">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div id="posSuccess" class="hidden bg-white rounded-2xl border border-green-200 shadow-sm p-10 text-center max-w-lg mx-auto">
            <div class="w-16 h-16 mx-auto rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Transaksi Berhasil</h2>
            <p class="mt-1 text-sm text-gray-500">Nomor pesanan <span id="posSuccessNumber" class="font-semibold text-gray-900"></span></p>
            <p class="mt-1 text-sm text-gray-500">Total <span id="posSuccessTotal" class="font-semibold text-gray-900"></span></p>
            <p class="mt-4 text-xs text-gray-400">Memuat ulang halaman…</p>
        </div>

        <div id="posContent" class="grid lg:grid-cols-5 gap-6">
            <!-- Kolom Kiri: Pencarian & Produk -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Cari Produk</h2>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <input type="text" id="posSearch" placeholder="Ketik nama produk…"
                               class="w-full ps-10 pe-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div id="posSearchResults" class="mt-3 hidden bg-white rounded-lg border border-gray-200 shadow-lg divide-y divide-gray-50 max-h-72 overflow-y-auto"></div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Daftar Produk</h2>
                    @if ($products->isEmpty())
                        <p class="py-6 text-sm text-gray-400 text-center">Belum ada produk aktif dengan stok tersedia.</p>
                    @else
                        <div class="grid grid-cols-2 gap-3 max-h-[32rem] overflow-y-auto pe-1">
                            @foreach ($products as $product)
                                <button type="button" class="js-pick-product text-left p-3 border border-gray-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition"
                                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
                                    <span class="block text-[11px] uppercase tracking-wide text-gray-400 truncate">{{ $product->category->name ?? 'Produk' }}</span>
                                    <span class="block text-sm font-semibold text-gray-900 line-clamp-2 mt-0.5">{{ $product->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Varian, Keranjang, Form -->
            <div class="lg:col-span-3 space-y-4">
                <!-- Pemilih Varian -->
                <div id="variantPicker" class="hidden bg-white rounded-xl border-2 border-blue-300 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Pilih Varian</p>
                            <p id="variantProductName" class="text-lg font-bold text-gray-900 truncate mt-0.5"></p>
                        </div>
                        <button type="button" id="closeVariantPicker" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Ukuran</label>
                            <select id="variantSize" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($sizes as $size)
                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Warna</label>
                            <select id="variantColor" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <p id="variantError" class="hidden mt-3 text-sm text-red-600"></p>

                    <div id="variantInfo" class="hidden mt-4 flex items-center justify-between gap-4 bg-gray-50 rounded-lg p-4">
                        <div>
                            <p class="text-xs text-gray-400">SKU</p>
                            <p id="variantSku" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400">Harga</p>
                            <p id="variantPrice" class="text-sm font-bold text-gray-900"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400">Tersedia</p>
                            <p id="variantAvailable" class="text-sm font-bold text-gray-900"></p>
                        </div>
                        <button type="button" id="addToCartBtn"
                                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                            + Keranjang
                        </button>
                    </div>
                </div>

                <!-- Keranjang -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Keranjang Penjualan</h2>
                    <div id="posCartEmpty" class="py-8 text-sm text-gray-400 text-center">
                        Pilih produk untuk menambahkan ke keranjang.
                    </div>
                    <div id="posCartItems" class="hidden space-y-3"></div>

                    <div id="posSummary" class="hidden mt-4 pt-4 border-t border-gray-200 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="posSubtotal" class="font-medium text-gray-900">Rp 0</span>
                        </div>
                        <div id="posCodFeeRow" class="flex justify-between text-gray-600 hidden">
                            <span>Biaya COD</span>
                            <span id="posCodFee" class="font-medium text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-gray-900 pt-1">
                            <span>Total</span>
                            <span id="posTotal">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Pelanggan & Pembayaran -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Detail Pesanan</h2>
                    <form id="posOrderForm">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" required
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">No. Telepon <span class="text-red-500">*</span></label>
                                <input type="tel" name="customer_phone" required
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="customer_address" rows="2" required
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @if ($posSettings->cod_enabled)
                                    <label class="flex items-center gap-3 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="payment_method" value="cod" class="text-blue-600 focus:ring-blue-500">
                                        <span>
                                            <span class="block text-sm font-semibold text-gray-900">Bayar di Tempat (COD)</span>
                                            <span class="block text-xs text-gray-400">Biaya COD: Rp {{ number_format((float) $posSettings->cod_fee, 0, ',', '.') }}</span>
                                        </span>
                                    </label>
                                @endif
                                @if ($posSettings->transfer_enabled)
                                    <label class="flex items-center gap-3 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="payment_method" value="transfer" class="text-blue-600 focus:ring-blue-500"
                                               {{ $posSettings->cod_enabled ? '' : 'checked' }}>
                                        <span>
                                            <span class="block text-sm font-semibold text-gray-900">Transfer Bank</span>
                                            <span class="block text-xs text-gray-400">BNI, BCA, Mandiri</span>
                                        </span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Catatan (opsional)</label>
                            <input type="text" name="notes"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: hubungi dulu sebelum kirim">
                        </div>

                        <div id="formErrors" class="hidden mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700"></div>

                        <button type="submit" id="submitSale"
                                class="mt-5 w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span id="submitLabel">Proses Pesanan</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    @vite(['resources/js/app.js'])

    <script>
        const SEARCH_PRODUCTS_URL = @json(route('pos.searchProducts'));
        const SEARCH_VARIANT_TEMPLATE = @json(route('pos.searchVariant', ['product' => '__ID__']));
        const RECORD_SALE_URL = @json(route('pos.recordSale'));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        const COD_FEE = {{ (float) ($posSettings->cod_fee ?? 0) }};
        const SIZES = @json($sizes->map(fn ($s) => ['id' => $s->id, 'name' => $s->name]));
        const COLORS = @json($colors->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]));

        let cart = [];       // { variant_id, product_id, product_name, sku, size, color, price, qty, max }
        let selectedVariant = null;

        const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

        // ---------- Pencarian ----------
        const searchInput = document.getElementById('posSearch');
        const searchResults = document.getElementById('posSearchResults');
        let searchTimer = null;

        searchInput.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(searchTimer);
            if (q.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            searchTimer = setTimeout(() => {
                fetch(SEARCH_PRODUCTS_URL + '?q=' + encodeURIComponent(q))
                    .then((r) => r.json())
                    .then((data) => {
                        if (!data.length) {
                            searchResults.innerHTML = '<p class="text-gray-400 text-sm text-center py-4">Produk tidak ditemukan</p>';
                        } else {
                            searchResults.innerHTML = data.map((p) => `
                                <button type="button" class="js-pick-product w-full text-start p-3 hover:bg-blue-50 transition"
                                        data-product-id="${p.id}" data-product-name="${escapeHtml(p.name)}">
                                    <span class="block text-sm font-medium text-gray-900">${escapeHtml(p.name)}</span>
                                </button>`).join('');
                        }
                        searchResults.classList.remove('hidden');
                    })
                    .catch(() => searchResults.classList.add('hidden'));
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // ---------- Pemilih Varian ----------
        const picker = document.getElementById('variantPicker');
        const pickerProduct = document.getElementById('variantProductName');
        const variantError = document.getElementById('variantError');
        const variantInfo = document.getElementById('variantInfo');

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.js-pick-product');
            if (!btn) return;
            picker.classList.remove('hidden');
            pickerProduct.textContent = btn.dataset.productName;
            picker.dataset.productId = btn.dataset.productId;
            variantInfo.classList.add('hidden');
            variantError.classList.add('hidden');
            selectedVariant = null;
            picker.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            lookupVariant();
        });

        document.getElementById('closeVariantPicker').addEventListener('click', () => {
            picker.classList.add('hidden');
            selectedVariant = null;
        });

        document.getElementById('variantSize').addEventListener('change', lookupVariant);
        document.getElementById('variantColor').addEventListener('change', lookupVariant);

        function lookupVariant() {
            const productId = picker.dataset.productId;
            if (!productId) return;

            const sizeId = document.getElementById('variantSize').value;
            const colorId = document.getElementById('variantColor').value;
            variantError.classList.add('hidden');
            variantInfo.classList.add('hidden');
            selectedVariant = null;

            fetch(SEARCH_VARIANT_TEMPLATE.replace('__ID__', productId) + `?size_id=${sizeId}&color_id=${colorId}`)
                .then((r) => r.json().then((data) => ({ status: r.status, data })))
                .then(({ status, data }) => {
                    if (status !== 200 || data.error) {
                        variantError.textContent = data.error || 'Varian tidak tersedia';
                        variantError.classList.remove('hidden');
                        return;
                    }
                    selectedVariant = {
                        variant_id: data.variant.id,
                        product_id: productId,
                        product_name: pickerProduct.textContent,
                        sku: data.sku,
                        size: sizeName(sizeId),
                        color: colorName(colorId),
                        price: Number(data.price),
                        max: Number(data.available),
                    };
                    document.getElementById('variantSku').textContent = data.sku;
                    document.getElementById('variantPrice').textContent = rupiah(data.price);
                    document.getElementById('variantAvailable').textContent = data.available + ' pcs';
                    variantInfo.classList.remove('hidden');
                })
                .catch(() => {
                    variantError.textContent = 'Gagal memuat data varian. Coba lagi.';
                    variantError.classList.remove('hidden');
                });
        }

        document.getElementById('addToCartBtn').addEventListener('click', () => {
            if (!selectedVariant) return;

            const existing = cart.find((i) => i.variant_id === selectedVariant.variant_id);
            if (existing) {
                if (existing.qty + 1 > existing.max) {
                    variantError.textContent = 'Melebihi stok tersedia (' + existing.max + ')';
                    variantError.classList.remove('hidden');
                    return;
                }
                existing.qty += 1;
            } else {
                if (selectedVariant.max < 1) {
                    variantError.textContent = 'Stok habis';
                    variantError.classList.remove('hidden');
                    return;
                }
                cart.push({ ...selectedVariant, qty: 1 });
            }
            renderCart();
        });

        function sizeName(id) { return (SIZES.find((s) => s.id == id) || {}).name || ''; }
        function colorName(id) { return (COLORS.find((c) => c.id == id) || {}).name || ''; }

        // ---------- Keranjang ----------
        function renderCart() {
            const empty = document.getElementById('posCartEmpty');
            const items = document.getElementById('posCartItems');
            const summary = document.getElementById('posSummary');

            if (!cart.length) {
                empty.classList.remove('hidden');
                items.classList.add('hidden');
                summary.classList.add('hidden');
                items.innerHTML = '';
                return;
            }
            empty.classList.add('hidden');
            items.classList.remove('hidden');
            summary.classList.remove('hidden');

            items.innerHTML = cart.map((item, idx) => `
                <div class="flex items-center gap-3 border border-gray-100 rounded-lg p-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">${escapeHtml(item.product_name)}</p>
                        <p class="text-xs text-gray-400 truncate">${escapeHtml(item.sku)} · ${escapeHtml(item.size)} · ${escapeHtml(item.color)} · ${rupiah(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="js-qty w-7 h-7 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 text-sm font-bold" data-idx="${idx}" data-delta="-1">−</button>
                        <span class="w-7 text-center text-sm font-semibold text-gray-900">${item.qty}</span>
                        <button type="button" class="js-qty w-7 h-7 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 text-sm font-bold" data-idx="${idx}" data-delta="1">+</button>
                    </div>
                    <div class="w-24 text-end text-sm font-bold text-gray-900">${rupiah(item.price * item.qty)}</div>
                    <button type="button" class="js-remove text-gray-300 hover:text-red-500 p-1" data-idx="${idx}" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>`).join('');

            updateTotals();
        }

        document.getElementById('posCartItems').addEventListener('click', (e) => {
            const qtyBtn = e.target.closest('.js-qty');
            if (qtyBtn) {
                const idx = Number(qtyBtn.dataset.idx);
                const delta = Number(qtyBtn.dataset.delta);
                const item = cart[idx];
                const next = item.qty + delta;
                if (next < 1) { cart.splice(idx, 1); }
                else if (next > item.max) { flashQtyLimit(item); return; }
                else { item.qty = next; }
                renderCart();
            }
            const removeBtn = e.target.closest('.js-remove');
            if (removeBtn) {
                cart.splice(Number(removeBtn.dataset.idx), 1);
                renderCart();
            }
        });

        function flashQtyLimit(item) {
            const err = document.getElementById('formErrors');
            err.textContent = 'Stok ' + item.sku + ' tersisa ' + item.max + ' pcs.';
            err.classList.remove('hidden');
            setTimeout(() => err.classList.add('hidden'), 3000);
        }

        function currentMethod() {
            const el = document.querySelector('input[name="payment_method"]:checked');
            return el ? el.value : null;
        }

        function updateTotals() {
            const subtotal = cart.reduce((sum, i) => sum + i.price * i.qty, 0);
            const isCod = currentMethod() === 'cod';
            const fee = isCod ? COD_FEE : 0;

            document.getElementById('posSubtotal').textContent = rupiah(subtotal);
            document.getElementById('posCodFee').textContent = rupiah(fee);
            document.getElementById('posCodFeeRow').classList.toggle('hidden', !isCod || COD_FEE === 0);
            document.getElementById('posTotal').textContent = rupiah(subtotal + fee);
        }

        document.querySelectorAll('input[name="payment_method"]').forEach((el) => {
            el.addEventListener('change', updateTotals);
        });

        // ---------- Submit ----------
        document.getElementById('posOrderForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errBox = document.getElementById('formErrors');
            errBox.classList.add('hidden');

            if (!cart.length) {
                errBox.textContent = 'Keranjang masih kosong. Pilih produk terlebih dahulu.';
                errBox.classList.remove('hidden');
                return;
            }
            const method = currentMethod();
            if (!method) {
                errBox.textContent = 'Pilih metode pembayaran.';
                errBox.classList.remove('hidden');
                return;
            }

            const form = e.target;
            const payload = {
                variant_ids: cart.map((i) => i.variant_id),
                quantities: cart.map((i) => i.qty),
                customer_name: form.customer_name.value.trim(),
                customer_phone: form.customer_phone.value.trim(),
                customer_address: form.customer_address.value.trim(),
                payment_method: method,
                notes: form.notes.value.trim() || null,
            };

            if (!payload.customer_name || !payload.customer_phone || !payload.customer_address) {
                errBox.textContent = 'Lengkapi nama, telepon, dan alamat pelanggan.';
                errBox.classList.remove('hidden');
                return;
            }

            const btn = document.getElementById('submitSale');
            btn.disabled = true;
            document.getElementById('submitLabel').textContent = 'Memproses…';

            try {
                const res = await fetch(RECORD_SALE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    document.getElementById('posContent').classList.add('hidden');
                    document.getElementById('posSuccessNumber').textContent = data.order.order_number;
                    document.getElementById('posSuccessTotal').textContent = rupiah(data.order.grand_total);
                    document.getElementById('posSuccess').classList.remove('hidden');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    setTimeout(() => window.location.reload(), 2500);
                    return;
                }

                if (res.status === 422 && data.errors) {
                    errBox.textContent = Object.values(data.errors).flat().join(' ');
                } else if (data.message) {
                    errBox.textContent = data.message;
                } else {
                    errBox.textContent = 'Transaksi gagal diproses. Coba lagi.';
                }
                errBox.classList.remove('hidden');
            } catch (err) {
                errBox.textContent = 'Gagal terhubung ke server. Periksa koneksi lalu coba lagi.';
                errBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                document.getElementById('submitLabel').textContent = 'Proses Pesanan';
            }
        });

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }
    </script>
</body>
</html>
