<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherManagerController extends Controller
{
    public function index()
    {
        $publishers = Publisher::withCount(['books', 'magazines'])
            ->orderBy('name')
            ->paginate(20);

        return view('librarian.publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('librarian.publishers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:publishers,name',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
        ]);

        Publisher::create($request->only('name', 'country', 'website'));

        return redirect()->route('librarian.editoriales.index')
            ->with('success', 'Editorial creada correctamente.');
    }

    public function show(Publisher $editorial)
    {
        $editorial->load(['books', 'magazines']);
        return view('librarian.publishers.show', compact('editorial'));
    }

    public function edit(Publisher $editorial)
    {
        return view('librarian.publishers.edit', compact('editorial'));
    }

    public function update(Request $request, Publisher $editorial)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:publishers,name,' . $editorial->id,
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
        ]);

        $editorial->update($request->only('name', 'country', 'website'));

        return redirect()->route('librarian.editoriales.index')
            ->with('success', 'Editorial actualizada correctamente.');
    }

    public function destroy(Publisher $editorial)
    {
        if ($editorial->books()->count() > 0 || $editorial->magazines()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una editorial que tiene libros o revistas asociados.');
        }

        $editorial->delete();

        return redirect()->route('librarian.editoriales.index')
            ->with('success', 'Editorial eliminada correctamente.');
    }
}