<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FeaturedList;
use App\Models\Book;
use App\Models\Magazine;
use App\Services\DataStructures\CircularDoublyLinkedList;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Carruseles de la sección home — cacheados 30 minutos
        $featuredLists = Cache::remember('home_featured_lists', 1800, function () {
            return FeaturedList::with(['items.itemable'])
                ->whereJsonContains('sections', 'home')
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(function ($list) {
                    // Convertir cada lista a lista circular doblemente enlazada
                    $circular = CircularDoublyLinkedList::fromFeaturedList($list->items);
                    return [
                        'id'    => $list->id,
                        'title' => $list->title,
                        'slug'  => $list->slug,
                        'items' => $circular->toArray(),
                    ];
                });
        });

        // Métricas generales para la home
        $stats = Cache::remember('home_stats', 3600, function () {
            return [
                'total_books'     => Book::count(),
                'total_magazines' => Magazine::count(),
            ];
        });

        return view('public.home', compact('featuredLists', 'stats'));
    }
}