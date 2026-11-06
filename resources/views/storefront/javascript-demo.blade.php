<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo JavaScript Dasar - RIZWEAR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
    <main class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <a href="{{ route('home') }}" class="text-sm font-semibold text-stone-600 hover:text-stone-950">&larr; Kembali ke halaman utama</a>

        <section id="javascript-demo" class="mt-6 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm" aria-labelledby="javascript-demo-title">
            <h1 id="javascript-demo-title" class="text-2xl font-bold">Contoh JavaScript Dasar Pertemuan 9</h1>
            <p class="mt-2 text-sm leading-6 text-stone-600">
                Contoh variabel, tipe data, operator, console.log(), dan alert(). Data juga dapat dilihat melalui Developer Tools.
            </p>
            <button id="javascript-demo-button" type="button" class="mt-4 inline-flex items-center rounded-md bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-900 focus:ring-offset-2">
                Jalankan Demo RIZWEAR
            </button>
            <p id="javascript-demo-output" class="mt-4 text-sm font-medium text-stone-700" aria-live="polite"></p>
        </section>
    </main>
</body>
</html>
