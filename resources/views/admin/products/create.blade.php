<x-admin.layout title="Tambah Produk Baru">
    <div class="max-w-5xl mx-auto" x-data="productForm()" x-init="init()" x-on:submit="syncFiles($event)">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi informasi, foto, harga, varian, lalu pilih status publikasi.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Kembali</a>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- ============ 1. INFORMASI PRODUK ============ --}}
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                    <span class="w-7 h-7 shrink-0 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center">1</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Produk</h2>
                        <p class="text-xs text-gray-500">Identitas produk yang tampil di katalog dan halaman produk.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Belum ada? <a href="{{ route('admin.categories.index') }}" target="_blank" class="text-blue-600 hover:underline">Kelola kategori ↗</a>
                        </p>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merek / Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" maxlength="100"
                               placeholder="Contoh: RIZWEAR, Uniqlo, ..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Opsional.</p>
                        @error('brand')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-ref="nameInput" required maxlength="255"
                               value="{{ old('name') }}" x-on:input="autoSlug()"
                               placeholder="Contoh: Essential Oversized T-Shirt"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug URL</label>
                        <input type="text" name="slug" x-model="slug" x-on:input="slugTouched = true" maxlength="255"
                               placeholder="otomatis-dari-nama-produk"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Terisi otomatis dari nama, tetap bisa diedit. Harus unik — dipakai di URL toko.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="5"
                                  placeholder="Bahan, detail potongan, cara perawatan, dll..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ============ 2. FOTO PRODUK ============ --}}
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                    <span class="w-7 h-7 shrink-0 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center">2</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Foto Produk</h2>
                        <p class="text-xs text-gray-500">JPG, JPEG, PNG, atau WebP — maksimal 2 MB per foto. Foto pertama menjadi foto utama.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Foto utama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Utama <span class="text-red-500">*</span></label>

                        <div x-show="!primaryUrl" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer"
                             x-on:click="$refs.primaryInput.click()">
                            <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Klik untuk memilih foto utama</p>
                            <p class="text-xs text-gray-400">Disarankan minimal 800×800 px, rasio persegi</p>
                        </div>

                        <div x-show="primaryUrl" x-cloak class="relative border border-gray-200 rounded-lg overflow-hidden">
                            <img :src="primaryUrl" alt="Preview foto utama" class="w-full h-56 object-cover bg-gray-100">
                            <div class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">Utama</div>
                            <button type="button" x-on:click="removePrimary()"
                                    class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded">
                                Hapus
                            </button>
                        </div>

                        <input type="file" x-ref="primaryInput" name="primary_image" class="hidden"
                               accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                               x-on:change="setPrimary($event)">
                        <p x-show="primaryError" x-cloak class="mt-1 text-sm text-red-600" x-text="primaryError"></p>
                        @error('primary_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Foto tambahan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Tambahan (galeri)</label>

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer"
                             x-on:click="$refs.galleryInput.click()">
                            <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Klik untuk menambah beberapa foto</p>
                            <p class="text-xs text-gray-400">Maksimal 8 foto, masing-masing maksimal 2 MB</p>
                        </div>

                        <input type="file" x-ref="galleryInput" name="images[]" multiple class="hidden"
                               accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                               x-on:change="addGallery($event)">

                        <div class="grid grid-cols-3 gap-2 mt-3" x-show="gallery.length" x-cloak>
                            <template x-for="(item, gi) in gallery" :key="item.key">
                                <div class="relative border border-gray-200 rounded-lg overflow-hidden">
                                    <img :src="item.url" alt="" class="w-full h-20 object-cover bg-gray-100">
                                    <button type="button" x-on:click="removeGallery(gi)"
                                            class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white text-xs w-5 h-5 rounded flex items-center justify-center">&times;</button>
                                </div>
                            </template>
                        </div>

                        <p x-show="galleryError" x-cloak class="mt-1 text-sm text-red-600" x-text="galleryError"></p>
                        @error('images.0')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ============ 3. HARGA DAN DISKON ============ --}}
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                    <span class="w-7 h-7 shrink-0 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center">3</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Harga dan Diskon</h2>
                        <p class="text-xs text-gray-500">Harga dasar yang dipakai semua varian; bisa ditimper per varian di bagian berikutnya.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="price" x-model.number="price" required min="0" step="0.01"
                                   value="{{ old('price') }}"
                                   placeholder="149000"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Terisi: <span class="font-medium text-gray-700" x-text="rp(price)"></span></p>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Sebelum Diskon</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="compare_at_price" x-model.number="compareAt" min="0" step="0.01"
                                   value="{{ old('compare_at_price') }}"
                                   placeholder="199000"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-1 flex items-center gap-2">
                            <p class="text-xs text-gray-500">Harga coret. Opsional, harus ≥ harga jual.</p>
                            <span x-show="discountPct > 0" x-cloak
                                  class="inline-flex items-center text-xs font-semibold bg-red-100 text-red-700 px-1.5 py-0.5 rounded">
                                Diskon <span x-text="discountPct"></span>%
                            </span>
                        </div>
                        @error('compare_at_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-1.5">
                            Harga Modal (HPP)
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" name="cost_price" value="{{ old('cost_price') }}" min="0" step="0.01"
                                   placeholder="80000"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Internal admin saja — tidak pernah ditampilkan ke pembeli.</p>
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ============ 4. VARIAN, SKU, DAN STOK ============ --}}
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                    <span class="w-7 h-7 shrink-0 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center">4</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Varian, SKU, dan Stok</h2>
                        <p class="text-xs text-gray-500">Pilih ukuran & warna — kombinasi otomatis dibuat. Stok dicatat per kombinasi varian.</p>
                    </div>
                </div>

                @if ($sizes->isEmpty() || $colors->isEmpty())
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-md text-yellow-800">
                        <p class="font-medium">Referensi ukuran/warna belum lengkap.</p>
                        <p class="text-sm mt-1">
                            Buat minimal satu ukuran dan satu warna terlebih dahulu:
                            <a href="{{ route('admin.sizes.index') }}" target="_blank" class="underline font-medium">Ukuran ↗</a> ·
                            <a href="{{ route('admin.colors.index') }}" target="_blank" class="underline font-medium">Warna ↗</a>
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" value="{{ $size->id }}"
                                               x-model="selectedSizes">
                                        <span class="inline-flex px-3 py-1.5 text-sm rounded-lg border border-gray-300 text-gray-600
                                             peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white transition-colors">
                                            {{ $size->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <a href="{{ route('admin.sizes.index') }}" target="_blank" class="text-blue-600 hover:underline">Kelola ukuran ↗</a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warna <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($colors as $color)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" value="{{ $color->id }}"
                                               x-model="selectedColors">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg border border-gray-300 text-gray-600
                                             peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white transition-colors">
                                            <span class="w-3 h-3 rounded-full border border-gray-300" style="background-color: {{ $color->hex_code }}"></span>
                                            {{ $color->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <a href="{{ route('admin.colors.index') }}" target="_blank" class="text-blue-600 hover:underline">Kelola warna ↗</a>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold" x-text="rows.length"></span> kombinasi varian siap disimpan
                        </p>
                        <p class="text-xs text-gray-400">Kosongkan SKU untuk auto-generate SKU unik.</p>
                    </div>

                    @error('variants')
                        <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{{ $message }}</div>
                    @enderror

                    <div class="space-y-3">
                        <template x-for="(row, i) in rows" :key="row.key">
                            <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-gray-800">
                                        <span x-text="row.sizeName"></span>
                                        <span class="text-gray-400 mx-1">·</span>
                                        <span x-text="row.colorName"></span>
                                    </p>
                                    <label class="flex items-center gap-1.5 text-xs text-gray-600 select-none">
                                        <input type="hidden" :name="'variants[' + i + '][is_active]'" value="0">
                                        <input type="checkbox" :name="'variants[' + i + '][is_active]'" value="1"
                                               x-model="row.active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        Dijual
                                    </label>
                                </div>

                                <input type="hidden" :name="'variants[' + i + '][size_id]'" :value="row.sizeId">
                                <input type="hidden" :name="'variants[' + i + '][color_id]'" :value="row.colorId">

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="sm:col-span-1">
                                        <label class="block text-xs text-gray-500 mb-1">SKU</label>
                                        <input type="text" :name="'variants[' + i + '][sku]'" x-model="row.sku"
                                               maxlength="100" pattern="[A-Za-z0-9\-_]*"
                                               :placeholder="suggestSku(row)"
                                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Stok <span class="text-red-500">*</span></label>
                                        <input type="number" :name="'variants[' + i + '][stock]'" x-model="row.stock"
                                               min="0" max="1000000" required
                                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Harga (opsional)</label>
                                        <input type="number" :name="'variants[' + i + '][price]'" x-model="row.price"
                                               min="0" step="0.01" :placeholder="'Ikut harga dasar: ' + price"
                                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="rows.length === 0" x-cloak
                         class="mt-3 p-4 border border-dashed border-gray-300 rounded-lg text-center text-sm text-gray-500">
                        Pilih minimal satu ukuran dan satu warna untuk membuat kombinasi varian.
                    </div>
                @endif
            </section>

            {{-- ============ 5. INFORMASI TAMBAHAN ============ --}}
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                    <span class="w-7 h-7 shrink-0 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center">5</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h2>
                        <p class="text-xs text-gray-500">Semua bersifat opsional untuk melengkapi halaman produk.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bahan</label>
                        <input type="text" name="material" value="{{ old('material') }}" maxlength="100"
                               placeholder="Contoh: Katun Combed 30s"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('material')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model / Potongan</label>
                        <input type="text" name="fit_style" value="{{ old('fit_style') }}" maxlength="100"
                               placeholder="Contoh: Oversized, Slim Fit, Regular"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('fit_style')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Berat (gram)</label>
                        <div class="relative">
                            <input type="number" name="weight_grams" value="{{ old('weight_grams') }}" min="0" max="100000"
                                   placeholder="300"
                                   class="w-full pr-12 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">gram</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Dipakai untuk perhitungan ongkir.</p>
                        @error('weight_grams')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Label Produk</label>
                        <div class="space-y-2 mt-1">
                            @foreach (\App\Models\Product::LABEL_OPTIONS as $value => $labelText)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="labels[]" value="{{ $value }}"
                                           {{ in_array($value, old('labels') ?? []) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    {{ $labelText }}
                                </label>
                            @endforeach
                        </div>
                        @error('labels')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('labels.0')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Panduan Ukuran (Size Chart)</label>
                        <textarea name="size_chart" rows="3"
                                  placeholder="{{ "S : Lingkar dada 52cm, Panjang 68cm\nM : Lingkar dada 55cm, Panjang 70cm" }}"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('size_chart') }}</textarea>
                        @error('size_chart')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>
                                <span class="text-sm font-medium text-gray-800">Produk Unggulan</span>
                                <span class="block text-xs text-gray-500">Ditandai sebagai produk unggulan toko (badge "Unggulan" di katalog).</span>
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            {{-- ============ 6. STATUS DAN PUBLIKASI ============ --}}
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                    <span class="w-7 h-7 shrink-0 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center">6</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Status dan Publikasi</h2>
                        <p class="text-xs text-gray-500">Draft: tidak terlihat pembeli. Aktif: langsung tampil di katalog dan bisa dibeli.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        Produk yang sudah tersimpan bisa diubah statusnya (termasuk Diarsipkan) dari halaman Edit.
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Batal</a>
                        <button type="submit" name="intent" value="draft" class="btn btn-outline">
                            Simpan sebagai Draft
                        </button>
                        <button type="submit" name="intent" value="active" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan &amp; Aktifkan
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>

    <script>
        function productForm() {
            return {
                slugTouched: {{ old('slug') !== null && old('slug') !== '' ? 'true' : 'false' }},
                slug: @js(old('slug')),
                price: @js(old('price') !== null && old('price') !== '' ? (float) old('price') : ''),
                compareAt: @js(old('compare_at_price') !== null && old('compare_at_price') !== '' ? (float) old('compare_at_price') : ''),
                primaryFile: null,
                primaryUrl: null,
                primaryError: '',
                gallery: [],
                galleryError: '',
                galleryKey: 0,
                sizeOptions: @js($sizes->map(fn ($s) => ['id' => (string) $s->id, 'name' => $s->name])->values()),
                colorOptions: @js($colors->map(fn ($c) => ['id' => (string) $c->id, 'name' => $c->name])->values()),
                selectedSizes: [],
                selectedColors: [],
                rows: [],
                oldVariants: @js(old('variants', [])),

                init() {
                    if (this.oldVariants.length) {
                        this.selectedSizes = [...new Set(this.oldVariants.map(v => String(v.size_id)))]
                            .filter(id => this.sizeOptions.some(s => s.id === id));
                        this.selectedColors = [...new Set(this.oldVariants.map(v => String(v.color_id)))]
                            .filter(id => this.colorOptions.some(c => c.id === id));
                        this.rebuild();

                        this.oldVariants.forEach(v => {
                            const row = this.rows.find(r => r.key === String(v.size_id) + '|' + String(v.color_id));
                            if (row) {
                                row.sku = v.sku || '';
                                row.stock = v.stock !== undefined ? v.stock : 0;
                                row.price = v.price !== undefined && v.price !== '' ? v.price : '';
                                row.active = v.is_active === undefined
                                    ? true
                                    : (v.is_active === '1' || v.is_active === 1 || v.is_active === true);
                            }
                        });
                    }

                    this.$watch('selectedSizes', () => this.rebuild());
                    this.$watch('selectedColors', () => this.rebuild());
                },

                rp(value) {
                    const n = parseFloat(value);
                    if (isNaN(n)) return 'Rp 0';
                    return 'Rp ' + n.toLocaleString('id-ID');
                },

                get discountPct() {
                    const p = parseFloat(this.price);
                    const c = parseFloat(this.compareAt);
                    if (isNaN(p) || isNaN(c) || c <= 0 || p >= c) return 0;
                    return Math.round(((c - p) / c) * 100);
                },

                slugify(text) {
                    return String(text)
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                },

                autoSlug() {
                    if (!this.slugTouched) {
                        this.slug = this.slugify(this.$refs.nameInput ? this.$refs.nameInput.value : '');
                    }
                },

                codeOf(text, max) {
                    const code = String(text).toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, max || 10);
                    return code || 'X';
                },

                suggestSku(row) {
                    const name = this.$refs.nameInput ? this.$refs.nameInput.value : '';
                    const base = this.codeOf(this.slugify(name), 10) || 'PRD';
                    return base + '-' + this.codeOf(row.sizeName, 8) + '-' + this.codeOf(row.colorName, 8);
                },

                rebuild() {
                    const previous = Object.fromEntries(this.rows.map(r => [r.key, r]));
                    const next = [];

                    this.selectedSizes.forEach(sizeId => {
                        this.selectedColors.forEach(colorId => {
                            const key = sizeId + '|' + colorId;
                            const prev = previous[key];
                            const size = this.sizeOptions.find(s => s.id === String(sizeId));
                            const color = this.colorOptions.find(c => c.id === String(colorId));

                            next.push({
                                key: key,
                                sizeId: String(sizeId),
                                sizeName: size ? size.name : '',
                                colorId: String(colorId),
                                colorName: color ? color.name : '',
                                sku: prev ? prev.sku : '',
                                stock: prev ? prev.stock : 0,
                                price: prev ? prev.price : '',
                                active: prev ? prev.active : true,
                            });
                        });
                    });

                    this.rows = next;
                },

                validateImage(file) {
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    const allowedExt = /\.(jpe?g|png|webp)$/i;
                    if (!allowedTypes.includes(file.type) && !allowedExt.test(file.name)) {
                        return 'Format tidak didukung (harus JPG, JPEG, PNG, atau WebP).';
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        return 'Ukuran file maksimal 2 MB.';
                    }
                    return '';
                },

                setPrimary(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const error = this.validateImage(file);
                    if (error) {
                        this.primaryError = error;
                        event.target.value = '';
                        return;
                    }

                    this.primaryError = '';
                    this.removePrimary();
                    this.primaryFile = file;
                    this.primaryUrl = URL.createObjectURL(file);
                },

                removePrimary() {
                    if (this.primaryUrl) URL.revokeObjectURL(this.primaryUrl);
                    this.primaryUrl = null;
                    this.primaryFile = null;
                    if (this.$refs.primaryInput) this.$refs.primaryInput.value = '';
                },

                addGallery(event) {
                    this.galleryError = '';
                    Array.from(event.target.files).forEach(file => {
                        const error = this.validateImage(file);
                        if (error) {
                            this.galleryError = error + ' (' + file.name + ')';
                            return;
                        }
                        if (this.gallery.length >= 8) {
                            this.galleryError = 'Maksimal 8 foto tambahan.';
                            return;
                        }
                        this.gallery.push({
                            key: this.galleryKey++,
                            file: file,
                            url: URL.createObjectURL(file),
                        });
                    });
                    event.target.value = '';
                },

                removeGallery(index) {
                    URL.revokeObjectURL(this.gallery[index].url);
                    this.gallery.splice(index, 1);
                },

                syncFiles(event) {
                    if (!this.primaryFile) {
                        this.primaryError = 'Foto utama wajib dipilih.';
                        event.preventDefault();
                        if (this.$refs.primaryInput) {
                            this.$refs.primaryInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return;
                    }

                    const primaryData = new DataTransfer();
                    primaryData.items.add(this.primaryFile);
                    this.$refs.primaryInput.files = primaryData.files;

                    const galleryData = new DataTransfer();
                    this.gallery.forEach(item => galleryData.items.add(item.file));
                    this.$refs.galleryInput.files = galleryData.files;
                },
            };
        }
    </script>
</x-admin.layout>
