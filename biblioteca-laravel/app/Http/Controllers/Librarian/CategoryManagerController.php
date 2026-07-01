<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryManagerController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['books', 'magazines'])
            ->orderBy('name')
            ->paginate(20);

        return view('librarian.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('librarian.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:categories,name',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'type'  => 'required|in:book,magazine,both',
        ]);

        Category::create([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'color' => $request->color,
            'type'  => $request->type,
        ]);

        Cache::forget('categories_books');
        Cache::forget('categories_magazines');

        return redirect()->route('librarian.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Category $categoria)
    {
        $categoria->load(['books', 'magazines']);
        return view('librarian.categories.show', compact('categoria'));
    }

    public function edit(Category $categoria)
    {
        return view('librarian.categories.edit', compact('categoria'));
    }

    public function update(Request $request, Category $categoria)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:categories,name,' . $categoria->id,
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'type'  => 'required|in:book,magazine,both',
        ]);

        $categoria->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'color' => $request->color,
            'type'  => $request->type,
        ]);

        Cache::forget('categories_books');
        Cache::forget('categories_magazines');

        return redirect()->route('librarian.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $categoria)
    {
        if ($categoria->books()->count() > 0 || $categoria->magazines()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una categoría que tiene libros o revistas asociados.');
        }

        $categoria->delete();
        Cache::forget('categories_books');
        Cache::forget('categories_magazines');

        return redirect()->route('librarian.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}