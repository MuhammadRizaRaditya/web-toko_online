<x-admin.layout title="Detail Produk: {{ $product->name }}">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">ID</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $product->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Slug</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $product->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Kategori</dt>
                            <dd class="text-sm text-gray-900">{{ $product->category?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Merek</dt>
                            <dd class="text-sm text-gray-900">{{ $product->brand ?? '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Bahan</dt>
                                <dd class="text-sm text-gray-900">{{ $product->material ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Model / Potongan</dt>
                                <dd class="text-sm text-gray-900">{{ $product->fit_style ?? '-' }}</dd>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Berat</dt>
                                <dd class="text-sm text-gray-900">{{ $product->weight_grams ? $product->weight_grams.' gram' : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">HPP (internal)</dt>
                                <dd class="text-sm text-gray-900 font-medium">
                                    @if ($product->cost_price !== null)
                                        Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                        <span class="text-xs text-gray-400 font-normal">(tak tampil di toko)</span>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Label</dt>
                            <dd class="flex flex-wrap gap-1.5 mt-1">
                                @if (empty($product->labels))
                                    <span class="text-sm text-gray-400">-</span>
                                @else
                                    @foreach ($product->labels as $label)
                                        @if (isset(\App\Models\Product::LABEL_OPTIONS[$label]))
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ \App\Models\Product::LABEL_OPTIONS[$label] }}
                                            </span>
                                        @endif
                                    @endforeach
                                @endif
                                @if ($product->is_featured)
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Unggulan</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
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
                                <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full {{ $statusColors[$product->status] }}">
                                    {{ $statusLabels[$product->status] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Dibuat</dt>
                            <dd class="text-sm text-gray-900">{{ $product->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Diperbarui</dt>
                            <dd class="text-sm text-gray-900">{{ $product->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Dibuat Oleh</dt>
                            <dd class="text-sm text-gray-900">{{ $product->creator?->name ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        @if ($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <em class="text-gray-400">Tidak ada deskripsi</em>
                        @endif
                    </div>
                    @if ($product->size_chart)
                        <h4 class="text-sm font-semibold text-gray-900 mt-5 mb-2">Panduan Ukuran</h4>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $product->size_chart }}</div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Images -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Gambar ({{ $product->images->count() }})</h3>
                    @if ($product->images->isEmpty())
                        <p class="text-gray-500 text-center py-8">Tidak ada gambar</p>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($product->images as $image)
                                <div class="relative border border-gray-200 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $image->path) }}"
                                         alt="{{ $image->alt_text }}"
                                         class="w-full h-24 object-cover">
                                    @if ($image->is_primary)
                                        <div class="absolute top-1 left-1 bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">Utama</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Variants Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Varian ({{ $product->variants->count() }})</h3>
                    @if ($product->variants->isEmpty())
                        <p class="text-gray-500 text-center py-4">Belum ada varian</p>
                    @else
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach ($product->variants as $variant)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-sm text-gray-900">{{ $variant->sku }}</p>
                                        <p class="text-xs text-gray-500">{{ $variant->size->name }} / {{ $variant->color->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-sm text-gray-900">Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                                        <p class="text-xs {{ $variant->is_low_stock ? 'text-red-600' : 'text-gray-500' }}">
                                            Stok: {{ $variant->available_stock }} / {{ $variant->stock_on_hand }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Variants Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Semua Varian</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Warna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok Tersedia / Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pr-4">Aksi</th>
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
                                    </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $variant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right pr-4">
                                    <a href="{{ route('admin.variants.edit', $variant) }}" class="btn btn-sm btn-outline">Edit</a>
                                    <a href="{{ route('admin.variants.show', $variant) }}" class="btn btn-sm btn-secondary ml-1">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin.layout>