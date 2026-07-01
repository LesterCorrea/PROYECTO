<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\FeaturedList;
use App\Services\DataStructures\CircularDoublyLinkedList;
use App\Services\DataStructures\DoublyLinkedList;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query   = $request->get('q');
        $sortBy  = $request->get('ordenar', 'name');
        $sortDir = $request->get('direccion', 'asc');

        $featuredLists = Cache::remember('collections_featured_lists', 1800, function () {
            return FeaturedList::with(['items.itemable.books'])
                ->whereJsonContains('sections', 'collections')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($list) {
                    $circular = CircularDoublyLinkedList::fromFeaturedList($list->items);
                    return [
                        'id'    => $list->id,
                        'title' => $list->title,
                        'items' => $circular->toArray(),
                    ];
                });
        });

        $collections = Collection::withCount('books')
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->get()
            ->toArray();

        $allowed     = ['name', 'books_count', 'views'];
        $sortKey     = in_array($sortBy, $allowed) ? $sortBy : 'name';
        $collections = SearchAlgorithms::quickSort($collections, $sortKey, $sortDir === 'asc');

        return view('public.collections.index', compact(
            'collections', 'featuredLists', 'query', 'sortBy', 'sortDir'
        ));
    }

    public function show(Collection $collection)
    {
        $collection->load('books.author');
        $collection->increment('views');

        // Lista doblemente enlazada para navegar la saga
        $dll       = DoublyLinkedList::fromSaga($collection->books);
        $sagaBooks = $dll->toArray();

        return view('public.collections.show', compact('collection', 'sagaBooks'));
    }
}