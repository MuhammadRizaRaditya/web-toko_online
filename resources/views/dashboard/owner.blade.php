<x-dashboard.layout title="Dashboard Owner">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Sapaan --}}
        @php
            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $tanggalTxt = $hari[now()->dayOfWeek] . ', ' . now()->day . ' ' . $bulan[now()->month - 1] . ' ' . now()->year;
        @endphp
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl shadow text-white p-6 sm:p-8">
            <p class="text-blue-100 text-sm font-medium">
                {{ $tanggalTxt }}
            </p>
            <h2 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight">
                Selamat {{ now()->hour < 11 ? 'Pagi' : (now()->hour < 15 ? 'Siang' : (now()->hour < 18 ? 'Sore' : 'Malam')) }}, {{ $user->name }}
            </h2>
            <p class="mt-1 text-blue-100 text-sm">Ringkasan performa toko RIZWEAR</p>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-dashboard.stat-card title="Omzet Total" value="Rp {{ number_format($omzetTotal, 0, ',', '.') }}" sub="Pesanan valid (tanpa batal/refund)" accent="emerald">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Omzet Bulan Ini" value="Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}" sub="{{ $bulan[now()->month - 1] }} {{ now()->year }}" accent="blue">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Total Pesanan" :value="$totalPesanan" :sub="$pesananBerjalan . ' pesanan sedang berjalan'" accent="violet">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Produk & Varian" value="{{ $totalProduk }} / {{ $totalVarian }}" sub="produk / varian terdaftar" accent="amber">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>
        </div>

        {{-- Pesanan Terbaru + Produk Terlaris --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Pesanan Terbaru</h3>
                    <span class="text-xs text-gray-400">{{ $totalPesanan }} total</span>
                </div>
                @if ($pesananTerbaru->isEmpty())
                    <p class="px-6 py-10 text-sm text-gray-400 text-center">Belum ada pesanan masuk.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                                    <th class="px-6 py-3">No. Pesanan</th>
                                    <th class="px-6 py-3">Pelanggan</th>
                                    <th class="px-6 py-3">Channel</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($pesananTerbaru as $order)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $order->order_number }}
                                            <span class="block text-xs text-gray-400 font-normal">{{ $order->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">{{ $order->recipient_name }}</td>
                                        <td class="px-6 py-3">
                                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $order->channel === 'pos' ? 'bg-teal-50 text-teal-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ strtoupper($order->channel) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3"><x-dashboard.badge :status="$order->order_status" /></td>
                                        <td class="px-6 py-3 text-right font-semibold text-gray-900 whitespace-nowrap"><x-rupiah :value="$order->grand_total" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Produk Terlaris</h3>
                </div>
                @if ($produkTerlaris->isEmpty())
                    <p class="px-6 py-10 text-sm text-gray-400 text-center">Belum ada penjualan tercatat.</p>
                @else
                    <ol class="divide-y divide-gray-50">
                        @foreach ($produkTerlaris as $i => $top)
                            <li class="px-6 py-3 flex items-center gap-3">
                                <span class="w-6 h-6 shrink-0 rounded-full bg-blue-50 text-blue-700 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $top->product_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $top->total_qty }} terjual</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 whitespace-nowrap"><x-rupiah :value="$top->total_revenue" /></span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Perhatian Stok</h3>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $stokHabis > 0 ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $stokHabis }} habis / {{ $stokMenipis->count() }} menipis
                </span>
            </div>
            @if ($stokMenipis->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-400 text-center">Semua stok varian dalam batas aman.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 p-4">
                    @foreach ($stokMenipis as $varian)
                        <div class="rounded-lg border border-gray-200 p-3 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $varian->product->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $varian->sku }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-bold px-2 py-1 rounded-md {{ $varian->available_stock <= 0 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ max(0, $varian->available_stock) }} / {{ $varian->low_stock_threshold }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Navigasi Cepat --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <x-dashboard.nav-card :href="route('admin.products.index')" title="Panel Admin" desc="Kelola produk, kategori, varian, dan stok">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </x-slot:icon>
            </x-dashboard.nav-card>

            <x-dashboard.nav-card :href="route('pos.index')" title="Kasir (POS)" desc="Lakukan penjualan langsung di toko">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </x-slot:icon>
            </x-dashboard.nav-card>

            <x-dashboard.nav-card :href="route('catalog.index')" title="Lihat Katalog" desc="Pratinjau toko seperti dilihat pelanggan">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </x-slot:icon>
            </x-dashboard.nav-card>
        </div>

        <p class="text-xs text-gray-400 text-center pb-4">
            Statistik dihitung langsung dari data transaksi toko. Modul Laporan & Ekspor belum tersedia.
        </p>
    </div>
</x-dashboard.layout>
