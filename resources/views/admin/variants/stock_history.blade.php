<x-admin.layout title="Riwayat Stok: {{ $variant->sku }}">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Stok</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $variant->product->name }} - {{ $variant->size->name }} / {{ $variant->color->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.variants.show', $variant) }}" class="btn btn-outline">Detail Varian</a>
                <a href="{{ route('admin.variants.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Stok Tersedia</p>
                <p class="text-2xl font-bold {{ $variant->available_stock <= 0 ? 'text-red-600' : ($variant->is_low_stock ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $variant->available_stock }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Stok Total</p>
                <p class="text-2xl font-bold text-gray-900">{{ $variant->stock_on_hand }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Stok Reserved</p>
                <p class="text-2xl font-bold text-gray-900">{{ $variant->stock_reserved }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm text-gray-600">Total Pergerakan</p>
                <p class="text-2xl font-bold text-blue-600">{{ $movements->total() }}</p>
            </div>
        </div>

        <!-- Stock Movements Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-wrap gap-4 items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Semua Riwayat Pergerakan Stok</h3>
                <form method="GET" class="flex gap-2">
                    <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="purchase_receive" {{ request('type') === 'purchase_receive' ? 'selected' : '' }}>Penerimaan Pembelian</option>
                        <option value="reservation" {{ request('type') === 'reservation' ? 'selected' : '' }}>Reservasi</option>
                        <option value="release" {{ request('type') === 'release' ? 'selected' : '' }}>Rilis Reservasi</option>
                        <option value="commit" {{ request('type') === 'commit' ? 'selected' : '' }}>Komit Penjualan</option>
                        <option value="adjustment_in" {{ request('type') === 'adjustment_in' ? 'selected' : '' }}>Koreksi Masuk</option>
                        <option value="adjustment_out" {{ request('type') === 'adjustment_out' ? 'selected' : '' }}>Koreksi Keluar</option>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                    <a href="{{ route('admin.variants.stockHistory', $variant) }}" class="btn btn-outline btn-sm">Reset</a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe Pergerakan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perubahan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alasan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Referensi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelaku</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($movements->isEmpty())
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat stok</td>
                            </tr>
                        @else
                            @foreach ($movements as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->created_at->format('d M Y H:i:s') }}</td>
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
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @if ($movement->reference_type && $movement->reference_id)
                                            <span class="font-mono text-xs">{{ $movement->reference_type }} #{{ $movement->reference_id }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->actor->name ?? 'System' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($movements->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $movements->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin.layout>