<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ config('app.name', 'RIZWEAR') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="text-2xl font-bold">{{ config('app.name', 'RIZWEAR') }}</a>
            <a href="{{ route('cart.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Kembali ke keranjang</a>
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Checkout</h1>
        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]">
            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6">
                @csrf
                <section>
                    <h2 class="text-lg font-semibold">Informasi penerima</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="recipient_name" class="block text-sm font-medium">Nama lengkap</label>
                            <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth()->user()?->name) }}" required maxlength="255" autocomplete="name" class="mt-1 w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label for="recipient_phone" class="block text-sm font-medium">Nomor telepon</label>
                            <input id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', auth()->user()?->phone) }}" required maxlength="50" autocomplete="tel" class="mt-1 w-full rounded-md border-gray-300">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="recipient_address" class="block text-sm font-medium">Alamat lengkap</label>
                            <textarea id="recipient_address" name="recipient_address" rows="3" required maxlength="2000" autocomplete="street-address" class="mt-1 w-full rounded-md border-gray-300">{{ old('recipient_address') }}</textarea>
                        </div>
                        <div>
                            <label for="recipient_city" class="block text-sm font-medium">Kota/Kabupaten</label>
                            <input id="recipient_city" name="recipient_city" value="{{ old('recipient_city') }}" required maxlength="255" autocomplete="address-level2" class="mt-1 w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label for="recipient_province" class="block text-sm font-medium">Provinsi</label>
                            <input id="recipient_province" name="recipient_province" value="{{ old('recipient_province') }}" required maxlength="255" autocomplete="address-level1" class="mt-1 w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label for="recipient_postal_code" class="block text-sm font-medium">Kode pos</label>
                            <input id="recipient_postal_code" name="recipient_postal_code" value="{{ old('recipient_postal_code') }}" required maxlength="20" autocomplete="postal-code" class="mt-1 w-full rounded-md border-gray-300">
                        </div>
                    </div>
                </section>

                <section class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-semibold">Metode pembayaran</h2>
                    <div class="mt-4 space-y-3">
                        @if (! $settings->cod_enabled && ! $settings->transfer_enabled)
                            <p role="alert" class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Belum ada metode pembayaran yang diaktifkan. Silakan hubungi pengelola toko.</p>
                        @endif
                        @if ($settings->cod_enabled)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4">
                                <input type="radio" name="payment_method" value="cod" required @checked(old('payment_method', 'cod') === 'cod') class="mt-1">
                                <span>
                                    <span class="block text-sm font-semibold">Bayar di tempat (COD)</span>
                                    <span class="mt-1 block text-xs text-gray-600">Pembayaran dilakukan saat pesanan diterima. Biaya COD: Rp {{ number_format((float) $settings->cod_fee, 0, ',', '.') }}.</span>
                                </span>
                            </label>
                        @endif
                        @if ($settings->transfer_enabled)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4">
                                <input type="radio" name="payment_method" value="transfer" required @checked(old('payment_method') === 'transfer' || ! $settings->cod_enabled) class="mt-1">
                                <span>
                                    <span class="block text-sm font-semibold">Transfer manual</span>
                                    <span class="mt-1 block text-xs text-gray-600">Pesanan menunggu pembayaran dan verifikasi admin. Informasi rekening perlu dikonfirmasi kepada pengelola toko.</span>
                                </span>
                            </label>
                        @endif
                    </div>
                </section>

                <button type="submit" @disabled(! $settings->cod_enabled && ! $settings->transfer_enabled) class="w-full rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-gray-400">Buat pesanan</button>
            </form>

            <aside class="h-fit rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-lg font-semibold">Ringkasan pesanan</h2>
                <ul class="mt-4 divide-y divide-gray-200">
                    @foreach ($items as $item)
                        <li class="flex justify-between gap-4 py-3 text-sm">
                            <span>{{ $item->variant->product->name }} ({{ $item->variant->size?->name }} · {{ $item->variant->color?->name }}) × {{ $item->quantity }}</span>
                            <span class="shrink-0">Rp {{ number_format((float) $item->variant->price * $item->quantity, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-sm">
                    <span>Subtotal</span>
                    <span class="font-semibold">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                </div>
                <p class="mt-2 text-xs text-gray-500">Biaya COD, jika dipilih, ditambahkan ke total pesanan.</p>
            </aside>
        </div>
    </main>
</body>
</html>
