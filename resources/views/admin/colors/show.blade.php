<x-admin.layout title="Detail Warna: {{ $color->name }}">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $color->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.colors.edit', $color) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.colors.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Warna</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-500">ID</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $color->id }}</dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm text-gray-500 w-32">Kode Hex</dt>
                    <dd class="text-sm text-gray-900 flex items-center">
                        @if ($color->hex_code)
                            <div class="w-6 h-6 rounded-full mr-2 border border-gray-200"
                                 style="background-color: {{ $color->hex_code }}"></div>
                            <span class="font-mono">{{ $color->hex_code }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full
                            {{ $color->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $color->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Jumlah Varian</dt>
                    <dd class="text-sm text-gray-900">{{ $color->variants_count }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Dibuat</dt>
                    <dd class="text-sm text-gray-900">{{ $color->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Diperbarui</dt>
                    <dd class="text-sm text-gray-900">{{ $color->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        @if ($color->variants->isNotEmpty())
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Varian yang Menggunakan Warna Ini ({{ $color->variants_count }})</h3>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach ($color->variants->take(15) as $variant)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-sm text-gray-900">{{ $variant->sku }}</p>
                                <p class="text-xs text-gray-500">{{ $variant->product->name }} - {{ $variant->size->name }}</p>
                            </div>
                            <a href="{{ route('admin.variants.edit', $variant) }}" class="btn btn-sm btn-outline">Edit</a>
                        </div>
                    @endforeach
                    @if ($color->variants->count() > 15)
                        <p class="text-xs text-gray-500 text-center mt-2">Dan {{ $color->variants->count() - 15 }} varian lainnya...</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-admin.layout>