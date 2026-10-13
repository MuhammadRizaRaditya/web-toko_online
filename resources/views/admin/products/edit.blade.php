<x-admin.layout title="Edit Produk: {{ $product->name }}">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Produk: {{ $product->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline">Lihat</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="{{ old('name', $product->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug"
                       value="{{ old('slug', $product->slug) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk auto-generate dari nama. Harus unik.</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="5"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
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
                                    <td class="px-4 py-3 text-sm text-gray-700">Rp {{ number_format($variant->price, 0, ',', '.') }}</td>
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
                <button wire:click="$toggle('imageUploadModal')" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Unggah Gambar
                </button>
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
                                    <button wire:click="setPrimaryImage({{ $image->id }})"
                                            class="btn btn-sm btn-primary mb-2 w-full">Jadikan Utama</button>
                                @else
                                    <span class="btn btn-sm btn-outline text-green-700 border-green-300 bg-green-50 w-full">Utama</span>
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