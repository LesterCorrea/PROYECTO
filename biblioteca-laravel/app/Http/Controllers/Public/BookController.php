<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\FeaturedList;
use App\Services\DataStructures\BinarySearchTree;
use App\Services\DataStructures\CircularDoublyLinkedList;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query      = $request->get('q');
        $categoryId = $request->get('categoria');
        $sortBy     = $request->get('ordenar', 'title');
        $sortDir    = $request->get('direccion', 'asc');
        $view       = $request->get('vista', 'grid');

        // Carruseles de libros — cacheados
        $featuredLists = Cache::remember('books_featured_lists', 1800, function () {
            return FeaturedList::with([
                'items.itemable.author',
                'items.itemable.category'
            ])
                ->whereJsonContains('sections', 'books')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($list) {
                    $circular = CircularDoublyLinkedList::fromFeaturedList($list->items);
                    return [
                        'id'    => $list->id,
                        'title' => $list->title,
                        'slug'  => $list->slug,
                        'items' => $circular->toArray(),
                    ];
                });
        });

        // Catálogo con filtros
        $booksQuery = Book::with(['author', 'category', 'publisher'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($query, fn($q) => $q->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('isbn', 'like', "%{$query}%")
                    ->orWhereHas('author', fn($q) => $q->where('name', 'like', "%{$query}%"));
            }));

        $books = $booksQuery->get()->toArray();

        // Aplicar QuickSort al catálogo
        $allowedSorts = ['title', 'published_year', 'views', 'loan_count'];
        $sortKey      = in_array($sortBy, $allowedSorts) ? $sortBy : 'title';
        $books        = SearchAlgorithms::quickSort($books, $sortKey, $sortDir === 'asc');

        // Si hay búsqueda, construir BST y usar búsqueda binaria
        $binaryResult = null;
        if ($query && !empty($books)) {
            $bst = new BinarySearchTree();
            foreach ($books as $book) {
                $bst->insert($book['id'], $book);
            }
            $sorted       = $bst->getSorted();
            $idx          = SearchAlgorithms::binarySearch($sorted, strtolower($query[0]), 'title');
            $binaryResult = $idx !== -1 ? $sorted[$idx] : null;
        }

        $categories = Cache::remember(
            'categories_books',
            3600,
            fn() =>
            Category::whereIn('type', ['book', 'both'])->get()
        );

        return view('public.books.index', compact(
            'books',
            'featuredLists',
            'categories',
            'query',
            'sortBy',
            'sortDir',
            'view',
            'binaryResult'
        ));
    }

    public function show(Book $book)
    {
        $book->load(['author', 'category', 'publisher', 'comments.user']);

        // Incrementar contador de vistas
        $book->increment('views');
        $book->views()->create([
            'user_id'    => auth()->id(),
            'ip_address' => request()->ip(),
            'viewed_at'  => now(),
        ]);

        // Lista doblemente enlazada de la saga
        $sagaList    = null;
        $sagaNeighbors = ['prev' => null, 'next' => null];

        if ($book->collection_id) {
            $sagaBooks = Book::where('collection_id', $book->collection_id)
                ->orderBy('saga_order')
                ->get();

            $dll = \App\Services\DataStructures\DoublyLinkedList::fromSaga($sagaBooks);
            $sagaNeighbors = $dll->getNeighbors($book->id);
            $sagaList      = $dll->toArray();
        }

        // Progreso de lectura del usuario autenticado
        $readingProgress = null;
        if (auth()->check()) {
            $readingProgress = $book->readingProgress()
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('public.books.show', compact(
            'book',
            'sagaList',
            'sagaNeighbors',
            'readingProgress'
        ));
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        $query = $request->get('q');

        // Búsqueda binaria sobre catálogo ordenado
        $books  = Book::with(['author', 'category'])
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('author', fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->orderBy('title')
            ->get()
            ->toArray();

        $sorted = SearchAlgorithms::quickSort($books, 'title');
        $index  = SearchAlgorithms::binarySearch($sorted, $query, 'title');

        return response()->json([
            'results'       => array_slice($sorted, 0, 10),
            'binary_result' => $index !== -1 ? $sorted[$index] : null,
            'total'         => count($sorted),
        ]);
    }
}
