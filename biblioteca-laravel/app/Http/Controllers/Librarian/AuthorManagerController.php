<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class AuthorManagerController extends Controller
{
    public function index()
    {
        $authors = Author::withCount(['books', 'magazines'])
            ->orderBy('name')
            ->paginate(20);

        return view('librarian.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('librarian.authors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Imagen opcional → si no se sube usa el avatar default
        if ($request->hasFile('image')) {
            $image = Image::read($request->file('image'));
            $image->scale(width: 300)->crop(300, 300);
            $filename = 'authors/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $validated['image'] = $filename;
        }

        Author::create($validated);

        return redirect()->route('librarian.autores.index')
            ->with('success', 'Autor creado correctamente.');
    }

    public function show(Author $autore)
    {
        $autore->load(['books', 'magazines']);
        return view('librarian.authors.show', compact('autore'));
    }

    public function edit(Author $autore)
    {
        return view('librarian.authors.edit', compact('autore'));
    }

    public function update(Request $request, Author $autore)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'bio'         => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($autore->image) {
                Storage::disk('public')->delete($autore->image);
            }
            $image    = Image::read($request->file('image'));
            $image->scale(width: 300)->crop(300, 300);
            $filename = 'authors/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $validated['image'] = $filename;
        }

        $autore->update($validated);

        return redirect()->route('librarian.autores.index')
            ->with('success', 'Autor actualizado correctamente.');
    }

    public function destroy(Author $autore)
    {
        if ($autore->image) {
            Storage::disk('public')->delete($autore->image);
        }
        $autore->delete();

        return redirect()->route('librarian.autores.index')
            ->with('success', 'Autor eliminado correctamente.');
    }
}
