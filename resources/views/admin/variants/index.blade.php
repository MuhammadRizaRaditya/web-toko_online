<x-admin.layout title="Manajemen Varian & Stok">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Varian & Stok</h1>
        <a href="{{ route('admin.variants.create') }}"
           class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Varian
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Varian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="SKU atau nama produk..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="flex items-center">
                    <input type="checkbox" name="low_stock" value="1"
                           {{ request('low_stock') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Stok Rendah</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="out_of_stock" value="1"
                           {{ request('out_of_stock') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Stok Habis</span>
                </label>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-secondary mr-2">Filter</button>
                <a href="{{ route('admin.variants.index') }}" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <!-- Variants Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if ($variants->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-lg font-medium">Belum ada varian</p>
                <p class="text-sm">Mulai dengan menambahkan varian pertama Anda</p>
                <a href="{{ route('admin.variants.create') }}" class="mt-4 inline-block btn btn-primary">Tambah Varian</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Warna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($variants as $variant)
                            <tr class="hover:bg-gray-50 {{ $variant->is_low_stock ? 'bg-red-50' : '' }} {{ $variant->available_stock <= 0 ? 'bg-red-100' : '' }}">
                                <td class="px-4 py-4">
                                    <span class="font-mono text-sm text-gray-700">{{ $variant->sku }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">{{ $variant->product->name }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">{{ $variant->size->name }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        @if ($variant->color->hex_code)
                                            <div class="w-6 h-6 rounded-full mr-2 border border-gray-200"
                                                 style="background-color: {{ $variant->color->hex_code }}"></div>
                                        @endif
                                        <span class="text-sm text-gray-700">{{ $variant->color->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div>
                                        <span class="{{ $variant->is_low_stock ? 'text-red-600 font-medium' : 'text-gray-700' }} font-mono">
                                            {{ $variant->available_stock }} / {{ $variant->stock_on_hand }}
                                        </span>
                                        @if ($variant->stock_reserved > 0)
                                            <span class="text-gray-400 text-xs ml-1">({{ $variant->stock_reserved }} reserved)</span>
                                        @endif
                                        @if ($variant->is_low_stock)
                                            <span class="ml-2 inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Stok Rendah</span>
                                        @endif
                                        @if ($variant->available_stock <= 0)
                                            <span class="ml-2 inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full bg-red-200 text-red-900">Habis</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $variant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right pr-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.variants.show', $variant) }}"
                                           class="btn btn-sm btn-outline">Detail</a>
                                        <a href="{{ route('admin.variants.edit', $variant) }}"
                                           class="btn btn-sm btn-primary">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200">
                {{ $variants->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>