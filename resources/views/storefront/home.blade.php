<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Temukan koleksi pakaian RIZWEAR dan jelajahi katalog produk terbaru.">
    <title>{{ config('app.name', 'RIZWEAR') }} - Toko Pakaian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight" aria-label="RIZWEAR, beranda">RIZWEAR</a>
            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-stone-700 hover:text-stone-950">Katalog</a>
                <a href="{{ route('cart.index') }}" aria-label="Keranjang belanja, {{ cartItemCount() }} item" class="relative text-stone-700 hover:text-stone-950">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @if (cartItemCount())
                        <span class="absolute -right-2 -top-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-xs text-white">{{ cartItemCount() }}</span>
                    @endif
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-md bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-stone-700 hover:text-stone-950">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 md:grid-cols-2 md:items-center md:py-28 lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-stone-500">Gaya untuk keseharian</p>
                <h1 class="mt-4 max-w-xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">Temukan gaya yang terasa seperti diri sendiri.</h1>
                <p class="mt-6 max-w-lg text-base leading-7 text-stone-600">Jelajahi pilihan pakaian RIZWEAR, lihat detail produk, dan pilih ukuran serta warna yang tersedia.</p>
                <a href="{{ route('catalog.index') }}" class="mt-8 inline-flex items-center rounded-md bg-stone-900 px-6 py-3 text-sm font-semibold text-white hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-900 focus:ring-offset-2">
                    Jelajahi katalog
                </a>
            </div>
            <div class="flex min-h-64 items-center justify-center rounded-2xl bg-stone-200 p-10 md:min-h-96">
                <p class="text-center text-3xl font-bold tracking-[0.2em] text-stone-500 sm:text-4xl">RIZWEAR</p>
            </div>
        </section>

        <section class="border-y border-stone-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                    <h2 class="font-semibold">Koleksi produk</h2>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Telusuri katalog dan temukan informasi produk serta varian yang tersedia.</p>
                </div>
                <div>
                    <h2 class="font-semibold">Pilihan yang mudah</h2>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Gunakan filter kategori, harga, ukuran, warna, dan ketersediaan stok.</p>
                </div>
                <div>
                    <h2 class="font-semibold">Pengelolaan terintegrasi</h2>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Pengelola dapat mengatur produk dan stok, sementara kasir mencatat penjualan melalui POS.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="mx-auto max-w-7xl px-4 py-8 text-sm text-stone-500 sm:px-6 lg:px-8">
        <p>&copy; {{ date('Y') }} RIZWEAR</p>
    </footer>
</body>
</html>
