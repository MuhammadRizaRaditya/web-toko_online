<x-admin.layout title="Edit Produk: {{ $product->name }}">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Produk: {{ $product->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Harga dan stok dikelola per varian di bawah. Deskripsi, foto, dan info tambahan diubah di sini.</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline">Lihat</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Merek / Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" maxlength="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required maxlength="255"
                           value="{{ old('name', $product->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk auto-generate dari nama. Harus unik.</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="5"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bahan</label>
                    <input type="text" name="material" value="{{ old('material', $product->material) }}" maxlength="100"
                           placeholder="Contoh: Katun Combed 30s"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('material')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Model / Potongan</label>
                    <input type="text" name="fit_style" value="{{ old('fit_style', $product->fit_style) }}" maxlength="100"
                           placeholder="Contoh: Oversized, Slim Fit"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('fit_style')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Berat (gram)</label>
                    <input type="number" name="weight_grams" value="{{ old('weight_grams', $product->weight_grams) }}"
                           min="0" max="100000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('weight_grams')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Modal (HPP)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}"
                           min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    <p class="mt-1 text-xs text-gray-500">Internal admin saja — tidak ditampilkan ke pembeli.</p>
                    @error('cost_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Panduan Ukuran (Size Chart)</label>
                    <textarea name="size_chart" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('size_chart', $product->size_chart) }}</textarea>
                    @error('size_chart')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Label Produk</label>
                    <div class="space-y-2 mt-1">
                        @foreach (\App\Models\Product::LABEL_OPTIONS as $value => $labelText)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="labels[]" value="{{ $value }}"
                                       {{ in_array($value, old('labels', $product->labels ?? [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                {{ $labelText }}
                            </label>
                        @endforeach
                    </div>
                    @error('labels')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>
                            <span class="text-sm font-medium text-gray-800">Produk Unggulan</span>
                            <span class="block text-xs text-gray-500">Ditandai sebagai produk unggulan toko (badge "Unggulan" di katalog).</span>
                        </span>
                    </label>
                </div>
            </div>

            <p class="mt-4 text-xs text-gray-500 bg-blue-50 border border-blue-100 rounded-lg p-3">
                Harga jual, harga coret, dan stok diubah melalui tabel <a href="#varians" class="text-blue-600 underline">Varian Produk</a> di bawah —
                setiap kombinasi ukuran & warna memiliki harga dan stok sendiri.
            </p>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 mt-4">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <!-- Varian Section -->
        <div id="varians" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Varian Produk</h2>
                <a href="{{ route('admin.variants.create', $product) }}" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Varian
                </a>
            </div>

            @if ($product->variants->isEmpty())
                <p class="text-gray-500 text-center py-8">Belum ada varian. Tambahkan varian pertama untuk produk ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Warna</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($product->variants as $variant)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ $variant->sku }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $variant->size->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $variant->color->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        Rp {{ number_format($variant->price, 0, ',', '.') }}
                                        @if ($variant->discount_percent)
                                            <span class="ml-1 text-xs font-semibold text-red-600">-{{ $variant->discount_percent }}%</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $variant->is_low_stock ? 'text-red-600 font-medium' : 'text-gray-700' }} font-mono">
                                            {{ $variant->available_stock }} / {{ $variant->stock_on_hand }}
                                            @if ($variant->stock_reserved > 0)
                                                <span class="text-gray-400 text-xs"> ({{ $variant->stock_reserved }} reserved)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            {{ $variant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right pr-4">
                                        <a href="{{ route('admin.variants.edit', $variant) }}" class="btn btn-sm btn-outline">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Images Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Gambar Produk</h2>
                <button type="button" onclick="document.getElementById('imageUploadForm').classList.toggle('hidden')"
                        class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Unggah Gambar
                </button>
            </div>

            <!-- Upload form (toggle) -->
            <div id="imageUploadForm" class="hidden mb-5 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <form method="POST" action="{{ route('admin.products.uploadImage', $product) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">File Gambar <span class="text-red-500">*</span></label>
                            <input type="file" name="image" required
                                   accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                                   class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">JPG/PNG/WebP, maksimal 2 MB.</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks Alternatif</label>
                            <input type="text" name="alt_text" maxlength="255"
                                   placeholder="Deskripsi untuk SEO/screen reader"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-center gap-4 pb-1">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_primary" value="1"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Jadikan foto utama
                            </label>
                            <button type="submit" class="btn btn-primary btn-sm">Unggah</button>
                        </div>
                    </div>
                </form>
            </div>

            @if ($product->images->isEmpty())
                <p class="text-gray-500 text-center py-8">Belum ada gambar.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($product->images as $image)
                        <div class="relative group border border-gray-200 rounded-lg overflow-hidden bg-white">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->alt_text }}"
                                 class="w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center p-2">
                                @if (!$image->is_primary)
                                    <form method="POST" action="{{ route('admin.products.setPrimaryImage', [$product, $image]) }}" class="w-full mb-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-primary w-full">Jadikan Utama</button>
                                    </form>
                                @else
                                    <span class="btn btn-sm btn-outline text-green-700 border-green-300 bg-green-50 w-full mb-2">Utama</span>
                                @endif
                                <form method="POST" action="{{ route('admin.products.deleteImage', [$product, $image]) }}" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Hapus gambar ini?')"
                                            class="btn btn-sm btn-danger w-full">Hapus</button>
                                </form>
                            </div>
                            @if ($image->is_primary)
                                <div class="absolute top-1 left-1">
                                    <span class="bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">Utama</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-admin.layout>
