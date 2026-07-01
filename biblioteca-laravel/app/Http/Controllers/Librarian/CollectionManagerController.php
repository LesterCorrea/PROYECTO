<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Collection;
use App\Services\DataStructures\DoublyLinkedList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CollectionManagerController extends Controller
{
    public function index()
    {
        $collections = Collection::withCount('books')
            ->orderBy('name')
            ->paginate(20);

        return view('librarian.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('librarian.collections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:collections,name',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $image     = Image::read($request->file('cover_image'));
            $image->scale(width: 400);
            $filename  = 'covers/collections/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $coverPath = $filename;
        }

        Collection::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'cover_image' => $coverPath,
        ]);

        Cache::forget('collections_featured_lists');

        return redirect()->route('librarian.colecciones.index')
            ->with('success', 'Colección creada correctamente.');
    }

    public function show(Collection $coleccione)
    {
        $coleccione->load('books.author');

        // Lista doblemente enlazada para mostrar orden de la saga
        $dll       = DoublyLinkedList::fromSaga($coleccione->books);
        $sagaBooks = $dll->toArray();

        $availableBooks = Book::whereNull('collection_id')
            ->orderBy('title')
            ->get();

        return view('librarian.collections.show', compact(
            'coleccione',
            'sagaBooks',
            'availableBooks'
        ));
    }

    public function edit(Collection $coleccione)
    {
        return view('librarian.collections.edit', compact('coleccione'));
    }

    public function update(Request $request, Collection $coleccione)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:collections,name,' . $coleccione->id,
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($coleccione->cover_image) {
                Storage::disk('public')->delete($coleccione->cover_image);
            }
            $image    = Image::read($request->file('cover_image'));
            $image->scale(width: 400);
            $filename = 'covers/collections/' . uniqid() . '.webp';
            Storage::disk('public')->put($filename, $image->toWebp());
            $coleccione->cover_image = $filename;
        }

        $coleccione->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'cover_image' => $coleccione->cover_image,
        ]);

        Cache::forget('collections_featured_lists');

        return redirect()->route('librarian.colecciones.index')
            ->with('success', 'Colección actualizada correctamente.');
    }

    public function destroy(Collection $coleccione)
    {
        if ($coleccione->books()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una colección que tiene libros asignados.');
        }

        if ($coleccione->cover_image) {
            Storage::disk('public')->delete($coleccione->cover_image);
        }

        $coleccione->delete();
        Cache::forget('collections_featured_lists');

        return redirect()->route('librarian.colecciones.index')
            ->with('success', 'Colección eliminada correctamente.');
    }

    // ── Añadir libro a la saga (lista doblemente enlazada) ───────────
    public function addBook(Request $request, Collection $collection)
    {
        $data = $request->json()->all() ?: $request->all();

        $validator = \Validator::make($data, [
            'book_id'    => 'required|exists:books,id',
            'saga_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book = Book::findOrFail($data['book_id']);

        if ($book->collection_id && $book->collection_id !== $collection->id) {
            return back()->with('error', "Este libro ya pertenece a otra colección.");
        }

        if ($book->collection_id === $collection->id) {
            return back()->with('error', "Este libro ya está en esta colección.");
        }

        $requestedOrder = (int) $request->saga_order;

        // Contar cuántos libros hay actualmente en la saga
        $totalBooks = Book::where('collection_id', $collection->id)->count();

        // Limitar el orden al siguiente disponible si es mayor
        $finalOrder = min($requestedOrder, $totalBooks + 1);

        // Si la posición está ocupada, desplazar los libros que estén en esa posición o después
        $occupied = Book::where('collection_id', $collection->id)
            ->where('saga_order', $finalOrder)
            ->exists();

        if ($occupied) {
            // Desplazar hacia abajo todos los libros desde esa posición
            Book::where('collection_id', $collection->id)
                ->where('saga_order', '>=', $finalOrder)
                ->orderByDesc('saga_order')
                ->each(function ($b) {
                    $b->update(['saga_order' => $b->saga_order + 1]);
                });
        }

        $book->update([
            'collection_id' => $collection->id,
            'saga_order'    => $finalOrder,
        ]);

        $this->rebuildSagaLinks($collection);
        Cache::forget('collections_featured_lists');

        return back()->with('success', "'{$book->title}' añadido en la posición {$finalOrder}.");
    }

    // ── Quitar libro de la saga ──────────────────────────────────────
    public function removeBook(Collection $collection, Book $book)
    {
        $book->update([
            'collection_id'    => null,
            'saga_order'       => null,
            'previous_book_id' => null,
            'next_book_id'     => null,
        ]);

        $this->rebuildSagaLinks($collection);
        Cache::forget('collections_featured_lists');

        return back()->with('success', 'Libro eliminado de la colección.');
    }

    // Reconstruir lista doblemente enlazada tras cambios en la saga
    private function rebuildSagaLinks(Collection $collection): void
    {
        $books = Book::where('collection_id', $collection->id)
            ->orderBy('saga_order')
            ->get();

        foreach ($books as $index => $book) {
            $book->update([
                'previous_book_id' => $index > 0
                    ? $books[$index - 1]->id
                    : null,
                'next_book_id' => $index < $books->count() - 1
                    ? $books[$index + 1]->id
                    : null,
            ]);
        }
    }


    // Búsqueda AJAX de libros sin colección (o de esta misma colección)
    public function searchBooks(Request $request, Collection $collection)
    {
        $query = $request->get('q', '');

        $books = Book::with('author')
            ->where(function ($q) use ($collection) {
                $q->whereNull('collection_id')
                    ->orWhere('collection_id', $collection->id);
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('isbn',  'like', "%{$query}%")
                    ->orWhereHas('author', fn($q) => $q->where('name', 'like', "%{$query}%"));
            })
            ->orderBy('title')
            ->limit(30)
            ->get()
            ->map(fn($b) => [
                'id'     => $b->id,
                'title'  => $b->title,
                'author' => $b->author?->name ?? '—',
                'isbn'   => $b->isbn,
                'cover'  => $b->cover_url,
                'in_collection' => $b->collection_id === $collection->id,
            ]);

        return response()->json($books);
    }

    // Reordenar libros de la saga via drag & drop
    public function reorderBooks(Request $request, Collection $collection)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*' => 'integer|exists:books,id',
        ]);

        foreach ($request->items as $order => $bookId) {
            Book::where('id', $bookId)
                ->where('collection_id', $collection->id)
                ->update(['saga_order' => $order + 1]);
        }

        $this->rebuildSagaLinks($collection);
        Cache::forget('collections_featured_lists');

        return response()->json(['success' => true]);
    }
}
