<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $query = Size::withCount('variants')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $sizes = $query->paginate(20)->withQueryString();

        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:sizes,name',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Size::create($validated);

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Ukuran berhasil dibuat.');
    }

    public function edit(Size $size)
    {
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:sizes,name,'.$size->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $size->update($validated);

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Ukuran berhasil diperbarui.');
    }

    public function destroy(Size $size)
    {
        if ($size->variants()->exists()) {
            return back()->withErrors(['error' => 'Ukuran sedang digunakan oleh varian produk.']);
        }

        $size->delete();

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Ukuran berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'size_ids' => 'required|array',
            'size_ids.*' => 'exists:sizes,id',
        ]);

        foreach ($request->size_ids as $index => $id) {
            Size::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
