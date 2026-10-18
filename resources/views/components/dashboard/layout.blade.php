@props(['title' => 'Dashboard'])
@php
    $authUser = Auth::user();

    $iconGrid = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
    $iconPos = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
    $iconShield = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
    $iconStore = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>';
    $iconBag = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';

    $groups = [];

    $groups[] = [
        'label' => 'Utama',
        'items' => [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => $iconGrid],
        ],
    ];

    if ($authUser->can('pos.access')) {
        $groups[] = [
            'label' => 'Penjualan',
            'items' => [
                ['label' => 'Kasir (POS)', 'href' => route('pos.index'), 'active' => request()->routeIs('pos.*'), 'icon' => $iconPos],
            ],
        ];
    }

    if ($authUser->hasAnyRole(['owner', 'admin'])) {
        $groups[] = [
            'label' => 'Pengelolaan',
            'items' => [
                ['label' => 'Panel Admin', 'href' => route('admin.products.index'), 'active' => request()->routeIs('admin.*'), 'icon' => $iconShield],
            ],
        ];
    }

    $groups[] = [
        'label' => 'Toko',
        'items' => [
            ['label' => 'Katalog Produk', 'href' => route('catalog.index'), 'active' => request()->routeIs('catalog.*') || request()->routeIs('product.*'), 'icon' => $iconBag],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RIZWEAR') }} - {{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out flex flex-col lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="h-16 px-6 flex items-center border-b border-gray-200">
                <a href="{{ route('dashboard') }}" class="text-xl font-extrabold tracking-tight text-gray-900">
                    {{ config('app.name', 'RIZWEAR') }}
                </a>
                <span class="ms-2 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 border border-blue-200 rounded-full">
                    {{ $authUser->getRoleNames()->first() ?? 'Member' }}
                </span>
            </div>

            <nav class="p-4 space-y-6 overflow-y-auto flex-1" style="max-height: calc(100vh - 8rem);">
                @foreach ($groups as $group)
                    <div>
                        <div class="px-3 mb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $group['label'] }}</div>
                        <div class="space-y-1">
                            @foreach ($group['items'] as $item)
                                <a href="{{ $item['href'] }}"
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ $item['active'] ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                    <span class="mr-3 {{ $item['active'] ? 'text-blue-600' : 'text-gray-400' }}">{!! $item['icon'] !!}</span>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="p-4 border-t border-gray-200 shrink-0">
                <a href="{{ url('/') }}" target="_blank"
                   class="flex items-center px-3 py-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    <span class="mr-3">{!! $iconStore !!}</span>
                    Lihat Toko
                    <svg class="ml-auto w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </aside>

        <!-- Overlay (mobile) -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black/40 lg:hidden" style="display: none;"></div>

        <!-- Konten -->
        <div class="flex-1 flex flex-col min-w-0 lg:ms-0">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center px-4 sm:px-6 gap-4">
                <button type="button" @click="sidebarOpen = ! sidebarOpen" class="lg:hidden p-2 -ms-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <h1 class="text-lg font-semibold text-gray-900 truncate">{{ $title }}</h1>

                <div class="ms-auto relative" x-data="{ dropdown: false }" @click.away="dropdown = false">
                    <button type="button" @click="dropdown = ! dropdown" class="flex items-center gap-3 text-sm text-gray-600 hover:text-gray-900 focus:outline-none">
                        <span class="hidden sm:inline font-medium">{{ $authUser->name }}</span>
                        <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold uppercase">{{ strtoupper(substr($authUser->name, 0, 1)) }}</span>
                    </button>

                    <div x-show="dropdown" x-cloak style="display: none;"
                         class="absolute end-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-40">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $authUser->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $authUser->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Profil Saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-start px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Konten Halaman -->
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
