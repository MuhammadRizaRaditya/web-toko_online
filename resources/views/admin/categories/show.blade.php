<x-admin.layout title="Detail Kategori: {{ $category->name }}">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">ID</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $category->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Slug</dt>
                            <dd class="text-sm font-mono text-gray-900">{{ $category->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Parent Kategori</dt>
                            <dd class="text-sm text-gray-900">{{ $category->parent?->name ?? 'Kategori Utama (Tidak ada parent)' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full
                                    {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Urutan (Sort Order)</dt>
                            <dd class="text-sm text-gray-900">{{ $category->sort_order }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Jumlah Produk</dt>
                            <dd class="text-sm text-gray-900">{{ $category->products_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Jumlah Sub-kategori</dt>
                            <dd class="text-sm text-gray-900">{{ $category->children_count ?? $category->children->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Dibuat</dt>
                            <dd class="text-sm text-gray-900">{{ $category->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Diperbarui</dt>
                            <dd class="text-sm text-gray-900">{{ $category->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {{ $category->description ?? '<em class="text-gray-400">Tidak ada deskripsi</em>' }}
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Sub-categories -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sub-kategori ({{ $category->children->count() }})</h3>
                    @if ($category->children->isEmpty())
                        <p class="text-gray-500 text-center py-8">Belum ada sub-kategori</p>
                    @else
                        <ul class="space-y-3">
                            @foreach ($category->children as $child)
                                <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-sm text-gray-900">{{ $child->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $child->products_count }} produk</p>
                                    </div>
                                    <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline">Edit</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Products in this category -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Produk ({{ $category->products_count }})</h3>
                    @if ($category->products->isEmpty())
                        <p class="text-gray-500 text-center py-4">Belum ada produk di kategori ini</p>
                    @else
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach ($category->products->take(10) as $product)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-sm text-gray-900">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->variants_count }} varian</p>
                                    </div>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline">Edit</a>
                                </div>
                            @endforeach
                            @if ($category->products->count() > 10)
                                <p class="text-xs text-gray-500 text-center mt-2">Dan {{ $category->products->count() - 10 }} produk lainnya...</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>