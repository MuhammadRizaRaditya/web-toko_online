<x-admin.layout title="Edit Warna: {{ $color->name }}">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Warna: {{ $color->name }}</h1>
            <a href="{{ route('admin.colors.index') }}" class="btn btn-outline">Kembali</a>
        </div>

        <form method="POST" action="{{ route('admin.colors.update', $color) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Warna <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="{{ old('name', $color->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Hex</label>
                <div class="flex items-center space-x-3">
                    <input type="color" name="hex_code"
                           value="{{ old('hex_code', $color->hex_code ?? '#000000') }}"
                           class="h-10 w-10 rounded-lg border border-gray-300 cursor-pointer">
                    <input type="text" name="hex_code_text"
                           value="{{ old('hex_code', $color->hex_code ?? '') }}"
                           placeholder="#000000"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono"
                           maxlength="7"
                           pattern="#[0-9A-Fa-f]{6}">
                </div>
                <p class="mt-1 text-xs text-gray-500">Format: #RRGGBB (contoh: #FF0000 untuk merah). Kosongkan jika tidak ada kode warna.</p>
                @error('hex_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       id="is_active" {{ old('is_active', $color->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.colors.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <script>
            // Sync color picker with text input
            document.querySelector('input[type="color"]').addEventListener('input', function(e) {
                document.querySelector('input[name="hex_code_text"]').value = e.target.value;
            });
            document.querySelector('input[name="hex_code_text"]').addEventListener('input', function(e) {
                if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                    document.querySelector('input[type="color"]').value = e.target.value;
                }
            });
        </script>
</x-admin.layout>