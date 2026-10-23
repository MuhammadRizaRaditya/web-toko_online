<x-dashboard.layout title="Dashboard">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Sapaan & Akun --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center gap-6">
            <span class="w-16 h-16 shrink-0 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold uppercase">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-400">Halo, selamat datang di RIZWEAR</p>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight truncate">{{ $user->name }}</h2>
                <p class="mt-0.5 text-sm text-gray-500 truncate">
                    {{ $user->email }}
                    @if ($user->created_at)
                        · Anggota sejak {{ $user->created_at->format('d M Y') }}
                    @endif
                </p>
            </div>
            <a href="{{ route('catalog.index') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Belanja Sekarang
            </a>
        </div>

        {{-- Kategori --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Kategori Produk</h3>
            </div>
            @if ($kategori->isEmpty())
                <p class="text-sm text-gray-400">Belum ada kategori tersedia.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($kategori as $kat)
                        <a href="{{ route('catalog.category', $kat->slug) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition shadow-sm">
                            {{ $kat->name }}
                            @if ($kat->children_count > 0)
                                <span class="text-[10px] font-bold text-gray-400">{{ $kat->children_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Produk Terbaru --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Produk Terbaru</h3>
                <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat semua &rarr;</a>
            </div>

            @if ($produkTerbaru->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-sm text-gray-400">
                    Belum ada produk tersedia saat ini. Silakan kembali lagi nanti.
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($produkTerbaru as $produk)
                        @php
                            $hargaMin = $produk->variants->min('price');
                            $imagePath = $produk->images->first()?->path;
                        @endphp
                        <a href="{{ route('product.show', $produk->slug) }}"
                           class="group bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md hover:border-blue-200 transition">
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if ($imagePath)
                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $produk->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="text-xs text-gray-400 truncate">{{ $produk->category->name ?? 'Tanpa Kategori' }}</p>
                                <p class="mt-0.5 text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-blue-700">{{ $produk->name }}</p>
                                <p class="mt-1.5 text-sm font-bold text-blue-600">
                                    @if ($hargaMin !== null)
                                        Mulai <x-rupiah :value="$hargaMin" />
                                    @else
                                        <span class="text-gray-400 font-normal text-xs">Harga belum tersedia</span>
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Riwayat Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pesanan Terbaru</h3>
                <a href="{{ route('cart.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat keranjang</a>
            </div>
            @if ($pesananTerbaru->isEmpty())
                <p class="mt-4 text-sm text-gray-500">Pesanan Anda akan muncul di sini setelah checkout.</p>
            @else
                <div class="mt-4 divide-y divide-gray-200">
                    @foreach ($pesananTerbaru as $pesanan)
                        <a href="{{ route('orders.show', $pesanan) }}" class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                            <span class="font-medium text-gray-900">{{ $pesanan->order_number }}</span>
                            <span class="text-gray-500">{{ str_replace('_', ' ', ucfirst($pesanan->order_status)) }} · Rp {{ number_format((float) $pesanan->grand_total, 0, ',', '.') }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dashboard.layout>
