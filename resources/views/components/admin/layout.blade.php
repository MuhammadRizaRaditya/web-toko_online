<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'RIZWEAR') }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <a href="{{ route('admin.products.index') }}" class="text-2xl font-bold text-gray-900">
                    {{ config('app.name', 'RIZWEAR') }} Admin
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Manajemen Produk
                </div>
                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.products*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Produk
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.categories*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Kategori
                </a>
                <a href="{{ route('admin.variants.index') }}"
                   class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.variants*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Varian & Stok
                </a>

                <div class="pt-4 border-t border-gray-200">
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Referensi
                    </div>
                    <a href="{{ route('admin.sizes.index') }}"
                       class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.sizes*') ? 'bg-blue-50 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ukuran
                    </a>
                    <a href="{{ route('admin.colors.index') }}"
                       class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.colors*') ? 'bg-blue-50 text-blue-700' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Warna
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-gray-200">
                <a href="{{ url('/') }}" target="_blank"
                   class="flex items-center px-3 py-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Lihat Toko
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-xl font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-700 font-medium text-sm">{{ strtoupper(auth()->user()->name[0]) }}</span>
                            </div>
                            <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="p-6">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>