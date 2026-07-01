<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\Category;
use App\Models\FeaturedList;
use App\Services\DataStructures\CircularDoublyLinkedList;
use App\Services\DataStructures\SearchAlgorithms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MagazineController extends Controller
{
    public function index(Request $request)
    {
        $query      = $request->get('q');
        $categoryId = $request->get('categoria');
        $sortBy     = $request->get('ordenar', 'title');
        $sortDir    = $request->get('direccion', 'asc');
        $view       = $request->get('vista', 'grid');

        $featuredLists = Cache::remember('magazines_featured_lists', 1800, function () {
            return FeaturedList::with([
                'items.itemable.authors',
                'items.itemable.category'
            ])
                ->whereJsonContains('sections', 'magazines')
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

        $magazines = Magazine::with(['authors', 'category', 'publisher'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($query, fn($q) => $q->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('issn', 'like', "%{$query}%")
                    ->orWhereHas('authors', fn($q) => $q->where('name', 'like', "%{$query}%"));
            }))
            ->get()
            ->toArray();

        $allowed  = ['title', 'published_date', 'views', 'volume'];
        $sortKey  = in_array($sortBy, $allowed) ? $sortBy : 'title';
        $magazines = SearchAlgorithms::quickSort($magazines, $sortKey, $sortDir === 'asc');

        $categories = Cache::remember(
            'categories_magazines',
            3600,
            fn() =>
            Category::whereIn('type', ['magazine', 'both'])->get()
        );

        return view('public.magazines.index', compact(
            'magazines',
            'featuredLists',
            'categories',
            'query',
            'sortBy',
            'sortDir',
            'view'
        ));
    }

    public function show(Magazine $magazine)
    {
        $magazine->load(['authors', 'category', 'publisher', 'comments.user']);

        $magazine->increment('views');
        $magazine->views()->create([
            'user_id'    => auth()->id(),
            'ip_address' => request()->ip(),
            'viewed_at'  => now(),
        ]);

        $readingProgress = null;
        if (auth()->check()) {
            $readingProgress = $magazine->readingProgress()
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('public.magazines.show', compact('magazine', 'readingProgress'));
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        $query = $request->get('q');

        $magazines = Magazine::with(['authors', 'category'])
            ->where('title', 'like', "%{$query}%")
            ->orderBy('title')
            ->get()
            ->toArray();

        $sorted = SearchAlgorithms::quickSort($magazines, 'title');

        return response()->json([
            'results' => array_slice($sorted, 0, 10),
            'total'   => count($sorted),
        ]);
    }
}
