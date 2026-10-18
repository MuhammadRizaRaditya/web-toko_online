<x-admin.layout title="Tambah Ukuran Baru">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Tambah Ukuran Baru</h1>
            <a href="{{ route('admin.sizes.index') }}" class="btn btn-outline">Kembali</a>
        </div>

        <form method="POST" action="{{ route('admin.sizes.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ukuran <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="{{ old('name') }}"
                       placeholder="Contoh: S, M, L, XL, 28, 30, 32"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutan (Sort Order)</label>
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', 0) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">Urutan tampilkan ukuran. Angka lebih kecil ditampilkan lebih dulu.</p>
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       id="is_active" {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Ukuran
                </button>
            </div>
        </form>
    </div>
</x-admin.layout>