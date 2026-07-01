<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class MagazineManagerController extends Controller
{
    public function index()
    {
        $magazines = Magazine::with(['authors', 'category'])
            ->orderBy('title')
            ->paginate(20);

        return view('librarian.magazines.index', compact('magazines'));
    }

    public function create()
    {
        $authors    = Author::orderBy('name')->get();
        $categories = Category::whereIn('type', ['magazine', 'both'])->orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();

        return view('librarian.magazines.create', compact('authors', 'categories', 'publishers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'issn'           => 'nullable|string|unique:magazines,issn',
            'description'    => 'nullable|string',
            'category_id'    => 'required|exists:categories,id',
            'publisher_id'   => 'nullable|exists:publishers,id',
            'volume'         => 'nullable|integer|min:1',
            'issue_number'   => 'nullable|integer|min:1',
            'published_date' => 'nullable|date',
            'language'       => 'nullable|string',
            'author_ids'     => 'required|array|min:1',
            'author_ids.*'   => 'exists:authors,id',
            'author_roles'   => 'nullable|array',
            'author_roles.*' => 'nullable|string|max:100',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'       => 'required|mimes:pdf|max:51200',
            'total_copies'   => 'required|integer|min:1',
        ]);

        // Procesar portada
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $image     = Image::read($request->file('cover_image'));
            $image->scale(width: 400);
            $filename  = 'covers/magazines/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $coverPath = $filename;
        }

        // Guardar PDF en disco privado
        $pdfPath = $request->file('pdf_file')
            ->store('pdfs/magazines', 'local');

        $magazine = Magazine::create([
            'title'          => $validated['title'],
            'issn'           => $validated['issn'] ?? null,
            'description'    => $validated['description'] ?? null,
            'category_id'    => $validated['category_id'],
            'publisher_id'   => $validated['publisher_id'] ?? null,
            'volume'         => $validated['volume'] ?? null,
            'issue_number'   => $validated['issue_number'] ?? null,
            'published_date' => $validated['published_date'] ?? null,
            'language'       => $validated['language'] ?? 'Español',
            'cover_image'    => $coverPath,
            'pdf_path'       => $pdfPath,
            'total_copies'   => $validated['total_copies'],
            'available_copies' => $validated['total_copies'],
        ]);

        // Asociar múltiples autores con su rol (pivot)
        $authorsSync = [];
        foreach ($validated['author_ids'] as $index => $authorId) {
            $authorsSync[$authorId] = [
                'role' => $request->author_roles[$index] ?? 'Autor',
            ];
        }
        $magazine->authors()->sync($authorsSync);

        \App\Jobs\ProcessPdfUpload::dispatch('magazine', $magazine->id, $pdfPath);

        Cache::forget('magazines_featured_lists');
        Cache::forget('home_featured_lists');

        return redirect()->route('librarian.revistas.index')
            ->with('success', 'Revista creada correctamente.');
    }

    public function show(Magazine $revista)
    {
        $revista->load(['authors', 'category', 'publisher']);
        return view('librarian.magazines.show', compact('revista'));
    }

    public function edit(Magazine $revista)
    {
        $authors    = Author::orderBy('name')->get();
        $categories = Category::whereIn('type', ['magazine', 'both'])->orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();

        return view('librarian.magazines.edit', compact(
            'revista',
            'authors',
            'categories',
            'publishers'
        ));
    }

    public function update(Request $request, Magazine $revista)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'issn'           => 'nullable|string|unique:magazines,issn,' . $revista->id,
            'description'    => 'nullable|string',
            'category_id'    => 'required|exists:categories,id',
            'publisher_id'   => 'nullable|exists:publishers,id',
            'volume'         => 'nullable|integer|min:1',
            'issue_number'   => 'nullable|integer|min:1',
            'published_date' => 'nullable|date',
            'language'       => 'nullable|string',
            'author_ids'     => 'required|array|min:1',
            'author_ids.*'   => 'exists:authors,id',
            'author_roles'   => 'nullable|array',
            'author_roles.*' => 'nullable|string|max:100',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'       => 'nullable|mimes:pdf|max:51200',
            'delete_cover' => 'nullable|boolean',
            'total_copies' => 'required|integer|min:1',
        ]);

        // Eliminar portada si se solicitó
        if ($request->boolean('delete_cover') && $revista->cover_image) {
            Storage::disk('public')->delete($revista->cover_image);
            $revista->cover_image = null;
            $revista->save();
        }

        // Nueva portada
        if ($request->hasFile('cover_image')) {
            if ($revista->cover_image) {
                Storage::disk('public')->delete($revista->cover_image);
            }
            $image    = Image::read($request->file('cover_image'));
            $image->scale(width: 400);
            $filename = 'covers/magazines/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $validated['cover_image'] = $filename;
        }

        // Nuevo PDF
        if ($request->hasFile('pdf_file')) {
            Storage::disk('local')->delete($revista->pdf_path);
            $validated['pdf_path'] = $request->file('pdf_file')
                ->store('pdfs/magazines', 'local');
        }

        $revista->update([
            'title'          => $validated['title'],
            'issn'           => $validated['issn'] ?? null,
            'description'    => $validated['description'] ?? null,
            'category_id'    => $validated['category_id'],
            'publisher_id'   => $validated['publisher_id'] ?? null,
            'volume'         => $validated['volume'] ?? null,
            'issue_number'   => $validated['issue_number'] ?? null,
            'published_date' => $validated['published_date'] ?? null,
            'language'       => $validated['language'] ?? 'Español',
            'cover_image'    => $validated['cover_image'] ?? $revista->cover_image,
            'pdf_path'       => $validated['pdf_path'] ?? $revista->pdf_path,
            'total_copies'   => $validated['total_copies'],
        ]);

        // Actualizar autores con sus roles
        $authorsSync = [];
        foreach ($validated['author_ids'] as $index => $authorId) {
            $authorsSync[$authorId] = [
                'role' => $request->author_roles[$index] ?? 'Autor',
            ];
        }
        $revista->authors()->sync($authorsSync);

        Cache::forget('magazines_featured_lists');
        Cache::forget('home_featured_lists');

        return redirect()->route('librarian.revistas.index')
            ->with('success', 'Revista actualizada correctamente.');
    }

    public function destroy(Magazine $revista)
    {
        if ($revista->cover_image) {
            Storage::disk('public')->delete($revista->cover_image);
        }
        Storage::disk('local')->delete($revista->pdf_path);

        $revista->delete();
        Cache::forget('magazines_featured_lists');

        return redirect()->route('librarian.revistas.index')
            ->with('success', 'Revista eliminada correctamente.');
    }
}
