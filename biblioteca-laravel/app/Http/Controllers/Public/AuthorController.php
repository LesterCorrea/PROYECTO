<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\FeaturedList;
use App\Services\DataStructures\CircularDoublyLinkedList;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query   = $request->get('q');
        $sortBy  = $request->get('ordenar', 'name');
        $sortDir = $request->get('direccion', 'asc');

        $featuredLists = Cache::remember('authors_featured_lists', 1800, function () {
            return FeaturedList::with(['items.itemable'])
                ->whereJsonContains('sections', 'authors')
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

        $authors = Author::withCount(['books', 'magazines'])
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%")
                ->orWhere('nationality', 'like', "%{$query}%"))
            ->get()
            ->toArray();

        $allowed = ['name', 'nationality', 'views', 'books_count'];
        $sortKey = in_array($sortBy, $allowed) ? $sortBy : 'name';
        $authors = SearchAlgorithms::quickSort($authors, $sortKey, $sortDir === 'asc');

        return view('public.authors.index', compact(
            'authors', 'featuredLists', 'query', 'sortBy', 'sortDir'
        ));
    }

    public function show(Author $author)
    {
        $author->load(['books.category', 'magazines.category']);
        $author->increment('views');

        $books    = SearchAlgorithms::quickSort(
            $author->books->toArray(), 'title'
        );
        $magazines = SearchAlgorithms::quickSort(
            $author->magazines->toArray(), 'title'
        );

        return view('public.authors.show', compact('author', 'books', 'magazines'));
    }
}