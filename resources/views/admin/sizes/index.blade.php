<x-admin.layout title="Manajemen Ukuran">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Ukuran</h1>
        <a href="{{ route('admin.sizes.create') }}"
           class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Ukuran
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Ukuran</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama ukuran..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-secondary mr-2">Filter</button>
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <!-- Sizes Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if ($sizes->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-lg font-medium">Belum ada ukuran</p>
                <p class="text-sm">Mulai dengan menambahkan ukuran pertama Anda</p>
                <a href="{{ route('admin.sizes.create') }}" class="mt-4 inline-block btn btn-primary">Tambah Ukuran</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Urutan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Varian</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($sizes as $size)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <span class="font-medium text-gray-900">{{ $size->name }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ $size->sort_order }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-700">{{ $size->variants_count }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $size->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $size->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right pr-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.sizes.edit', $size) }}"
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.sizes.destroy', $size) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Yakin ingin menghapus ukuran ini?')"
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
                {{ $sizes->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>