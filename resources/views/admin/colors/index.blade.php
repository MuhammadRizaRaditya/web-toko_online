<x-admin.layout title="Manajemen Warna">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Warna</h1>
        <a href="{{ route('admin.colors.create') }}"
           class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Warna
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Warna</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama warna..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-secondary mr-2">Filter</button>
                <a href="{{ route('admin.colors.index') }}" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <!-- Colors Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if ($colors->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <p class="text-lg font-medium">Belum ada warna</p>
                <p class="text-sm">Mulai dengan menambahkan warna pertama Anda</p>
                <a href="{{ route('admin.colors.create') }}" class="mt-4 inline-block btn btn-primary">Tambah Warna</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Warna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Hex</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Varian</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($colors as $color)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        @if ($color->hex_code)
                                            <div class="w-8 h-8 rounded-full mr-3 border border-gray-200"
                                                 style="background-color: {{ $color->hex_code }}"></div>
                                        @else
                                            <div class="w-8 h-8 rounded-full mr-3 border border-gray-200 bg-gray-100"></div>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $color->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($color->hex_code)
                                        <span class="font-mono text-sm text-gray-700">{{ $color->hex_code }}</span>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">{{ $color->variants_count }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $color->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $color->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right pr-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.colors.edit', $color) }}"
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.colors.destroy', $color) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Yakin ingin menghapus warna ini?')"
                                                    class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200">
                {{ $colors->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>