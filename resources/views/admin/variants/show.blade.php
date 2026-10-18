<x-admin.layout title="Detail Varian: {{ $variant->sku }}">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $variant->sku }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.variants.edit', $variant) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.variants.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Varian</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">ID</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $variant->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Produk</dt>
                            <dd class="text-sm text-gray-900">
                                <a href="{{ route('admin.products.edit', $variant->product) }}" class="text-blue-600 hover:underline">
                                    {{ $variant->product->name }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">SKU</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $variant->sku }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Barcode</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $variant->barcode ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Ukuran</dt>
                            <dd class="text-sm text-gray-900">{{ $variant->size->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Warna</dt>
                            <dd class="text-sm text-gray-900 flex items-center">
                                @if ($variant->color->hex_code)
                                    <div class="w-5 h-5 rounded-full mr-2 border border-gray-200"
                                         style="background-color: {{ $variant->color->hex_code }}"></div>
                                @endif
                                {{ $variant->color->name }}
                                @if ($variant->color->hex_code)
                                    <span class="ml-2 text-xs font-mono text-gray-500">({{ $variant->color->hex_code }})</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Harga</dt>
                            <dd class="text-sm text-gray-900">Rp {{ number_format($variant->price, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Harga Coret</dt>
                            <dd class="text-sm text-gray-900">{{ $variant->compare_at_price ? 'Rp ' . number_format($variant->compare_at_price, 0, ',', '.') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Stok Tersedia</dt>
                            <dd class="text-sm font-medium
                                {{ $variant->is_low_stock ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $variant->available_stock }} / {{ $variant->stock_on_hand }}
                                @if ($variant->stock_reserved > 0)
                                    <span class="text-gray-400 text-normal font-normal"> ({{ $variant->stock_reserved }} reserved)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Batas Stok Rendah</dt>
                            <dd class="text-sm text-gray-900">{{ $variant->low_stock_threshold }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full
                                    {{ $variant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Dibuat</dt>
                            <dd class="text-sm text-gray-900">{{ $variant->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Diperbarui</dt>
                            <dd class="text-sm text-gray-900">{{ $variant->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Stock Adjustment Form -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Penyesuaian Stok</h3>
                    <form method="POST" action="{{ route('admin.variants.adjustStock', $variant) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Perubahan Stok (Delta) <span class="text-red-500">*</span></label>
                                <input type="number" name="delta" required
                                       placeholder="Contoh: 10 (tambah) atau -5 (kurang)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('delta')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                                <input type="text" name="reason" required
                                       placeholder="Contoh: Koreksi stok fisik, retur pelanggan, dll"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Sesuaikan Stok
                        </button>
                        <a href="{{ route('admin.variants.stockHistory', $variant) }}" class="btn btn-outline ml-2">Riwayat Stok</a>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Stok</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Stok Tersedia</span>
                            <span class="font-medium text-lg {{ $variant->available_stock <= 0 ? 'text-red-600' : ($variant->is_low_stock ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $variant->available_stock }}
                            </span>
                        </div>
                        <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Stok Total (On Hand)</span>
                            <span class="font-medium text-lg text-gray-900">{{ $variant->stock_on_hand }}</span>
                        </div>
                        <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Stok Reserved</span>
                            <span class="font-medium text-lg text-gray-900">{{ $variant->stock_reserved }}</span>
                        </div>
                        <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Batas Stok Rendah</span>
                            <span class="font-medium text-lg text-gray-900">{{ $variant->low_stock_threshold }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Harga Jual</span>
                            <span class="font-medium text-lg text-gray-900">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                        </div>
                        @if ($variant->compare_at_price)
                            <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Harga Coret</span>
                                <span class="font-medium text-lg text-gray-500 line-through">Rp {{ number_format($variant->compare_at_price, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Links -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tautan Terkait</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.products.edit', $variant->product) }}#varians"
                           class="block p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5 inline mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Kelola Produk: {{ $variant->product->name }}
                        </a>
                        <a href="{{ route('admin.variants.stockHistory', $variant) }}"
                           class="block p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5 inline mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat Stok
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Movements Table (Recent 20) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Stok Terbaru (20)</h3>
                <a href="{{ route('admin.variants.stockHistory', $variant) }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perubahan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alasan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelaku</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($variant->stockMovements->isEmpty())
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat stok</td>
                            </tr>
                        @else
                            @foreach ($variant->stockMovements as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $typeColors = [
                                                'purchase_receive' => 'bg-blue-100 text-blue-800',
                                                'reservation' => 'bg-yellow-100 text-yellow-800',
                                                'release' => 'bg-purple-100 text-purple-800',
                                                'commit' => 'bg-green-100 text-green-800',
                                                'adjustment_in' => 'bg-indigo-100 text-indigo-800',
                                                'adjustment_out' => 'bg-red-100 text-red-800',
                                            ];
                                            $typeLabels = [
                                                'purchase_receive' => 'Penerimaan Pembelian',
                                                'reservation' => 'Reservasi',
                                                'release' => 'Rilis Reservasi',
                                                'commit' => 'Komit Penjualan',
                                                'adjustment_in' => 'Koreksi Masuk',
                                                'adjustment_out' => 'Koreksi Keluar',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $typeLabels[$movement->movement_type] ?? $movement->movement_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono {{ $movement->quantity_delta > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $movement->quantity_delta > 0 ? '+' : '' }}{{ $movement->quantity_delta }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->reason }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->actor->name ?? 'System' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin.layout>