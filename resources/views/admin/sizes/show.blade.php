<x-admin.layout title="Detail Ukuran: {{ $size->name }}">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $size->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.sizes.edit', $size) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Ukuran</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-500">ID</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $size->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Urutan (Sort Order)</dt>
                    <dd class="text-sm text-gray-900">{{ $size->sort_order }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full
                            {{ $size->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $size->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Jumlah Varian</dt>
                    <dd class="text-sm text-gray-900">{{ $size->variants_count }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Dibuat</dt>
                    <dd class="text-sm text-gray-900">{{ $size->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Diperbarui</dt>
                    <dd class="text-sm text-gray-900">{{ $size->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        @if ($size->variants->isNotEmpty())
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Varian yang Menggunakan Ukuran Ini ({{ $size->variants_count }})</h3>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach ($size->variants->take(15) as $variant)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-sm text-gray-900">{{ $variant->sku }}</p>
                                <p class="text-xs text-gray-500">{{ $variant->product->name }} - {{ $variant->color->name }}</p>
                            </div>
                            <a href="{{ route('admin.variants.edit', $variant) }}" class="btn btn-sm btn-outline">Edit</a>
                        </div>
                    @endforeach
                    @if ($size->variants->count() > 15)
                        <p class="text-xs text-gray-500 text-center mt-2">Dan {{ $size->variants->count() - 15 }} varian lainnya...</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-admin.layout>