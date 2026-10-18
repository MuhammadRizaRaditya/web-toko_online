<x-dashboard.layout title="Dashboard Admin">
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Halo, {{ $user->name }} 👋</h2>
                <p class="mt-1 text-sm text-gray-500">Ringkasan katalog, stok, dan pesanan RIZWEAR</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Produk Baru
                </a>
                <a href="{{ route('pos.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg border border-gray-200 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Buka POS
                </a>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-dashboard.stat-card title="Total Produk" :value="$totalProduk" :sub="$produkAktif . ' aktif'" accent="blue">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Kategori" :value="$totalKategori" sub="kategori produk" accent="violet">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Varian" :value="$totalVarian" :sub="$varianAktif . ' aktif (varian stok)'" accent="teal">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Stok Perlu Perhatian" :value="$stokMenipis->count()" :sub="$stokHabis . ' varian habis'" accent="rose">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </x-slot:icon>
            </x-dashboard.stat-card>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Pesanan Terbaru --}}
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
                    <h3 class="font-semibold text-gray-900">Stok Menipis / Habis</h3>
                </div>
                @if ($stokMenipis->isEmpty())
                    <p class="px-6 py-10 text-sm text-gray-400 text-center">Semua stok varian dalam batas aman.</p>
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
                    <div class="px-6 py-3 border-t border-gray-100">
                        <a href="{{ route('admin.variants.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                            Kelola varian & stok &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Navigasi Pengelolaan --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Pengelolaan Produk</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <x-dashboard.nav-card :href="route('admin.products.index')" title="Produk" desc="Tambah, ubah, dan atur status produk">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </x-slot:icon>
                </x-dashboard.nav-card>

                <x-dashboard.nav-card :href="route('admin.categories.index')" title="Kategori" desc="Struktur katalog dan subkategori">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </x-slot:icon>
                </x-dashboard.nav-card>

                <x-dashboard.nav-card :href="route('admin.variants.index')" title="Varian & Stok" desc="Ukuran, warna, SKU, dan penyesuaian stok">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </x-slot:icon>
                </x-dashboard.nav-card>

                <x-dashboard.nav-card :href="route('admin.colors.index')" title="Warna" desc="Daftar warna untuk varian produk">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </x-slot:icon>
                </x-dashboard.nav-card>

                <x-dashboard.nav-card :href="route('admin.sizes.index')" title="Ukuran" desc="Daftar ukuran dan urutan tampil">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </x-slot:icon>
                </x-dashboard.nav-card>

                <x-dashboard.nav-card :href="route('catalog.index')" title="Lihat Katalog" desc="Pratinjau toko seperti pelanggan">
                    <x-slot:icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </x-slot:icon>
                </x-dashboard.nav-card>
            </div>
        </div>
    </div>
</x-dashboard.layout>
