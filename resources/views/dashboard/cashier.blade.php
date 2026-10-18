<x-dashboard.layout title="Dashboard Kasir">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Hero: Buka POS --}}
        <div class="bg-gradient-to-r from-slate-900 to-slate-700 rounded-2xl shadow text-white p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="flex-1">
                <p class="text-slate-300 text-sm font-medium">Halo, {{ $user->name }} — siap melayani penjualan hari ini?</p>
                <h2 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight">Mulai Transaksi Kasir</h2>
                <p class="mt-1 text-slate-300 text-sm">Catat penjualan tunai/transfer langsung lewat POS RIZWEAR.</p>
            </div>
            <a href="{{ route('pos.index') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white text-slate-900 hover:bg-blue-50 text-sm font-bold rounded-xl shadow-lg transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Buka POS
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Statistik Hari Ini --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-dashboard.stat-card title="Transaksi POS Hari Ini" :value="$posHariIni" sub="semua status pesanan" accent="blue">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Omzet POS Hari Ini" value="Rp {{ number_format($omzetHariIni, 0, ',', '.') }}" sub="tanpa pesanan batal/refund" accent="emerald">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Riwayat Transaksi POS --}}
            <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Riwayat Transaksi Terbaru</h3>
                </div>
                @if ($transaksiTerbaru->isEmpty())
                    <p class="px-6 py-10 text-sm text-gray-400 text-center">Belum ada transaksi POS. Buka POS untuk memulai penjualan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                                    <th class="px-6 py-3">No. Pesanan</th>
                                    <th class="px-6 py-3">Pelanggan</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($transaksiTerbaru as $order)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $order->order_number }}
                                            <span class="block text-xs text-gray-400 font-normal">{{ $order->created_at->format('d M H:i') }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">{{ $order->recipient_name }}</td>
                                        <td class="px-6 py-3"><x-dashboard.badge :status="$order->order_status" /></td>
                                        <td class="px-6 py-3 text-right font-semibold text-gray-900 whitespace-nowrap"><x-rupiah :value="$order->grand_total" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Stok Menipis --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Stok Menipis</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Informasi untuk melayani pembeli</p>
                </div>
                @if ($stokMenipis->isEmpty())
                    <p class="px-6 py-10 text-sm text-gray-400 text-center">Semua stok dalam batas aman.</p>
                @else
                    <ul class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                        @foreach ($stokMenipis as $varian)
                            <li class="px-6 py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $varian->product->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $varian->sku }}</p>
                                </div>
                                <span class="shrink-0 text-xs font-bold px-2 py-1 rounded-md {{ $varian->available_stock <= 0 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ max(0, $varian->available_stock) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Navigasi Cepat --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-dashboard.nav-card :href="route('pos.index')" title="Kasir (POS)" desc="Buat penjualan baru">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </x-slot:icon>
            </x-dashboard.nav-card>

            <x-dashboard.nav-card :href="route('catalog.index')" title="Katalog Produk" desc="Cek harga dan ketersediaan produk">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </x-slot:icon>
            </x-dashboard.nav-card>
        </div>
    </div>
</x-dashboard.layout>
