<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $query = Color::withCount('variants')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $colors = $query->paginate(20)->withQueryString();

        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:colors,name',
            'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Color::create($validated);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Warna berhasil dibuat.');
    }

    public function edit(Color $color)
    {
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:colors,name,'.$color->id,
            'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $color->update($validated);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Warna berhasil diperbarui.');
    }

    public function destroy(Color $color)
    {
        if ($color->variants()->exists()) {
            return back()->withErrors(['error' => 'Warna sedang digunakan oleh varian produk.']);
        }

        $color->delete();

        return redirect()->route('admin.colors.index')
            ->with('success', 'Warna berhasil dihapus.');
    }
}
