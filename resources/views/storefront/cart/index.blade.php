<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - {{ config('app.name', 'RIZWEAR') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="text-2xl font-bold">{{ config('app.name', 'RIZWEAR') }}</a>
            <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Lanjut belanja</a>
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Keranjang Belanja</h1>

        @if (session('status'))
            <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($items->isEmpty())
            <section class="mt-8 rounded-xl border border-gray-200 bg-white p-8 text-center">
                <p class="text-gray-600">Keranjang Anda masih kosong.</p>
                <a href="{{ route('catalog.index') }}" class="mt-5 inline-flex rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-700">Jelajahi katalog</a>
            </section>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]">
                <section class="space-y-4" aria-label="Produk dalam keranjang">
                    @foreach ($items as $item)
                        @php
                            $variant = $item->variant;
                            $image = $variant?->product?->images->firstWhere('is_primary') ?? $variant?->product?->images->first();
                        @endphp
                        <article class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-center">
                            @if ($image)
                                <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $variant->product->name }}" class="h-24 w-24 rounded-lg object-cover">
                            @else
                                <div class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-500">Tanpa foto</div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h2 class="font-semibold">{{ $variant?->product?->name ?? 'Produk tidak tersedia' }}</h2>
                                <p class="mt-1 text-sm text-gray-600">{{ $variant?->size?->name }} · {{ $variant?->color?->name }}</p>
                                <p class="mt-2 text-sm font-semibold">Rp {{ number_format((float) ($variant?->price ?? 0), 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="quantity-{{ $item->id }}">Jumlah {{ $variant?->product?->name }}</label>
                                    <input id="quantity-{{ $item->id }}" name="quantity" type="number" min="1" max="100" value="{{ $item->quantity }}" class="w-20 rounded-md border-gray-300 text-sm">
                                    <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium hover:bg-gray-50">Ubah</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </section>

                <aside class="h-fit rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-lg font-semibold">Ringkasan</h2>
                    <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-sm">
                        <span>Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Biaya COD (jika berlaku) dihitung saat checkout.</p>
                    <a href="{{ route('checkout.index') }}" class="mt-6 block rounded-md bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-gray-700">Lanjut ke checkout</a>
                </aside>
            </div>
        @endif
    </main>
</body>
</html>
