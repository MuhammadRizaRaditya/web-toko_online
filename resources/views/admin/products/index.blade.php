<x-admin.layout title="Manajemen Produk">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Produk</h1>
        <a href="{{ route('admin.products.create') }}"
           class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama, slug, atau SKU..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-secondary mr-2">Filter</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if ($products->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-lg font-medium">Belum ada produk</p>
                <p class="text-sm">Mulai dengan menambahkan produk pertama Anda</p>
                <a href="{{ route('admin.products.create') }}" class="mt-4 inline-block btn btn-primary">Tambah Produk</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Varian</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        @if ($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-12 h-12 object-cover rounded-lg mr-3">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $product->slug }}</p>
                                            @if ($product->variants_count > 0)
                                                <p class="text-xs text-gray-400">{{ $product->variants_count }} varian</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">{{ $product->category?->name ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">{{ $product->variants_count }}</span>
                                    @if ($product->variants_count > 0)
                                        <a href="{{ route('admin.products.edit', $product) }}#varians"
                                           class="text-xs text-blue-600 hover:underline ml-2">Kelola</a>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
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
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$product->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$product->status] ?? $product->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($product->variants_count > 0)
                                        <span class="text-sm text-gray-700">
                                            Rp {{ number_format($product->variants->min('price'), 0, ',', '.') }}
                                            @if ($product->variants->max('price') != $product->variants->min('price'))
                                                - Rp {{ number_format($product->variants->max('price'), 0, ',', '.') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    {{ $product->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-4 text-right pr-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.products.show', $product) }}"
                                           class="btn btn-sm btn-outline">Lihat</a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
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
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>