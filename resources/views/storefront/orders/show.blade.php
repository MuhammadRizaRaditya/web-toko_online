<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan {{ $order->order_number }} - {{ config('app.name', 'RIZWEAR') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="text-2xl font-bold">{{ config('app.name', 'RIZWEAR') }}</a>
            <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Kembali ke katalog</a>
        </nav>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-xl border border-gray-200 bg-white p-6 sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-green-700">Pesanan berhasil dibuat</p>
            <h1 class="mt-2 text-3xl font-bold">Terima kasih atas pesanan Anda.</h1>
            <p class="mt-3 text-sm text-gray-600">Nomor pesanan: <strong>{{ $order->order_number }}</strong></p>
            <p class="mt-1 text-sm text-gray-600">Status pesanan: <strong>{{ str_replace('_', ' ', ucfirst($order->order_status)) }}</strong></p>

            <div class="mt-6 rounded-lg bg-gray-50 p-4">
                <h2 class="font-semibold">Informasi pembayaran</h2>
                @if ($order->payment?->method === 'transfer')
                    <p class="mt-2 text-sm leading-6 text-gray-600">Pesanan menunggu pembayaran transfer manual dan verifikasi admin. Informasi rekening perlu dikonfirmasi kepada pengelola toko sebelum melakukan transfer.</p>
                @else
                    <p class="mt-2 text-sm leading-6 text-gray-600">Pembayaran COD dilakukan saat pesanan diterima. Total sudah mencakup biaya COD jika berlaku.</p>
                @endif
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-semibold">Rincian pesanan</h2>
                <ul class="mt-3 divide-y divide-gray-200">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-4 py-3 text-sm">
                            <span>{{ $item->product_name_snapshot }} ({{ $item->size_snapshot }} · {{ $item->color_snapshot }}) × {{ $item->quantity }}</span>
                            <span class="shrink-0">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 space-y-2 border-t border-gray-200 pt-4 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>{{ $order->payment?->method === 'cod' ? 'Biaya COD' : 'Biaya tambahan' }}</span><span>Rp {{ number_format((float) $order->shipping_total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-base font-bold"><span>Total</span><span>Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('catalog.index') }}" class="rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-700">Lanjut belanja</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-md border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Ke dashboard</a>
                @endauth
            </div>
        </section>
    </main>
</body>
</html>
