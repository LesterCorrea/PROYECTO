<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class BookManagerController extends Controller
{
    public function index()
    {
        $books = Book::with(['author', 'category'])
            ->orderBy('title')
            ->paginate(20);

        return view('librarian.books.index', compact('books'));
    }

    public function create()
    {
        $authors     = Author::orderBy('name')->get();
        $categories  = Category::whereIn('type', ['book', 'both'])->orderBy('name')->get();
        $publishers  = Publisher::orderBy('name')->get();
        $collections = Collection::orderBy('name')->get();

        return view('librarian.books.create', compact(
            'authors',
            'categories',
            'publishers',
            'collections'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'isbn'           => 'required|string|unique:books,isbn',
            'description'    => 'nullable|string',
            'author_id'      => 'required|exists:authors,id',
            'category_id'    => 'required|exists:categories,id',
            'publisher_id'   => 'nullable|exists:publishers,id',
            'collection_id'  => 'nullable|exists:collections,id',
            'saga_order'     => 'nullable|integer|min:1',
            'total_copies'   => 'required|integer|min:1',
            'pages'          => 'nullable|integer|min:1',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'language'       => 'nullable|string',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'       => 'required|mimes:pdf|max:51200', // 50MB máx
        ]);

        // Procesar portada
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $image     = Image::read($request->file('cover_image'));
            $image->scale(width: 400);
            $filename  = 'covers/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $coverPath = $filename;
        }

        // Guardar PDF en disco privado
        $pdfPath = $request->file('pdf_file')
            ->store('pdfs/books', 'local');

        $book = Book::create([
            ...$validated,
            'cover_image'       => $coverPath,
            'pdf_path'          => $pdfPath,
            'available_copies'  => $validated['total_copies'],
        ]);

        // Actualizar lista doblemente enlazada de la saga
        if ($book->collection_id) {
            $this->updateSagaLinks($book);
        }

        \App\Jobs\ProcessPdfUpload::dispatch('book', $book->id, $pdfPath);

        // Limpiar caché
        Cache::forget('books_featured_lists');
        Cache::forget('home_featured_lists');

        return redirect()->route('librarian.libros.index')
            ->with('success', 'Libro creado correctamente.');
    }

    public function edit(Book $libro)
    {
        $authors     = Author::orderBy('name')->get();
        $categories  = Category::whereIn('type', ['book', 'both'])->orderBy('name')->get();
        $publishers  = Publisher::orderBy('name')->get();
        $collections = Collection::orderBy('name')->get();

        return view('librarian.books.edit', compact(
            'libro',
            'authors',
            'categories',
            'publishers',
            'collections'
        ));
    }

    public function update(Request $request, Book $libro)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'isbn'           => 'required|string|unique:books,isbn,' . $libro->id,
            'description'    => 'nullable|string',
            'author_id'      => 'required|exists:authors,id',
            'category_id'    => 'required|exists:categories,id',
            'publisher_id'   => 'nullable|exists:publishers,id',
            'collection_id'  => 'nullable|exists:collections,id',
            'saga_order'     => 'nullable|integer|min:1',
            'total_copies'   => 'required|integer|min:1',
            'pages'          => 'nullable|integer|min:1',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'language'       => 'nullable|string',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'       => 'nullable|mimes:pdf|max:51200',
            'delete_cover'   => 'nullable|boolean',
        ]);

        // Eliminar portada si se solicitó
        if ($request->boolean('delete_cover') && $libro->cover_image) {
            Storage::disk('public')->delete($libro->cover_image);
            $validated['cover_image'] = null;
        }

        // Nueva portada
        if ($request->hasFile('cover_image')) {
            if ($libro->cover_image) {
                Storage::disk('public')->delete($libro->cover_image);
            }
            $image    = Image::read($request->file('cover_image'));
            $image->scale(width: 400);
            $filename = 'covers/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $validated['cover_image'] = $filename;
        }

        // Nuevo PDF
        if ($request->hasFile('pdf_file')) {
            Storage::disk('local')->delete($libro->pdf_path);
            $validated['pdf_path'] = $request->file('pdf_file')
                ->store('pdfs/books', 'local');
        }

        $libro->update($validated);

        if ($libro->collection_id) {
            $this->updateSagaLinks($libro);
        }

        Cache::forget('books_featured_lists');
        Cache::forget('home_featured_lists');

        return redirect()->route('librarian.libros.index')
            ->with('success', 'Libro actualizado correctamente.');
    }

    public function destroy(Book $libro)
    {
        $libro->delete(); // Soft delete
        Cache::forget('books_featured_lists');

        return redirect()->route('librarian.libros.index')
            ->with('success', 'Libro eliminado correctamente.');
    }

    public function show(Book $libro)
    {
        $libro->load(['author', 'category', 'publisher', 'collection']);
        return view('librarian.books.show', compact('libro'));
    }

    // Actualizar prev/next_book_id (lista doblemente enlazada de saga)
    private function updateSagaLinks(Book $book): void
    {
        $sagaBooks = Book::where('collection_id', $book->collection_id)
            ->orderBy('saga_order')
            ->get();

        foreach ($sagaBooks as $index => $sagaBook) {
            $sagaBook->update([
                'previous_book_id' => $index > 0
                    ? $sagaBooks[$index - 1]->id
                    : null,
                'next_book_id' => $index < $sagaBooks->count() - 1
                    ? $sagaBooks[$index + 1]->id
                    : null,
            ]);
        }
    }
}
