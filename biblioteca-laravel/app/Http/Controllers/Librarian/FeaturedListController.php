<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Collection;
use App\Models\FeaturedList;
use App\Models\FeaturedListItem;
use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FeaturedListController extends Controller
{
    public function index()
    {
        $lists = FeaturedList::withCount('items')->orderBy('order')->get();
        return view('librarian.featured.index', compact('lists'));
    }

    public function create()
    {
        return view('librarian.featured.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections'    => 'required|array|min:1',
            'sections.*'  => 'in:home,books,magazines,collections,authors',
            'type'        => 'required|in:books,magazines,authors,collections,books_magazines',
            'is_active'   => 'boolean',
            'order'       => 'integer|min:0',
        ]);

        FeaturedList::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,
            'sections'    => $request->sections,
            'type'        => $request->type,
            'is_active'   => $request->boolean('is_active', true),
            'order'       => $request->order ?? 0,
        ]);

        $this->clearCarouselCache();

        return redirect()->route('librarian.carruseles.index')
            ->with('success', 'Carrusel creado correctamente.');
    }

    public function show(FeaturedList $carrusele)
    {
        $carrusele->load('items.itemable');

        // Cargar elementos disponibles según el tipo del carrusel
        $availableItems = $this->getAvailableItems($carrusele->type);

        // IDs ya en el carrusel para marcarlos
        $existingIds = $carrusele->items->mapWithKeys(function ($item) {
            return [$item->itemable_type . '_' . $item->itemable_id => true];
        });

        return view('librarian.featured.show', compact(
            'carrusele',
            'availableItems',
            'existingIds'
        ));
    }

    public function edit(FeaturedList $carrusele)
    {
        return view('librarian.featured.edit', compact('carrusele'));
    }

    public function update(Request $request, FeaturedList $carrusele)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections'    => 'required|array|min:1',
            'sections.*'  => 'in:home,books,magazines,collections,authors',
            'type'        => 'required|in:books,magazines,authors,collections,books_magazines',
            'is_active'   => 'boolean',
            'order'       => 'integer|min:0',
        ]);

        $carrusele->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,
            'sections'    => $request->sections,
            'type'        => $request->type,
            'is_active'   => $request->boolean('is_active'),
            'order'       => $request->order ?? 0,
        ]);

        $this->clearCarouselCache();

        return redirect()->route('librarian.carruseles.index')
            ->with('success', 'Carrusel actualizado correctamente.');
    }

    public function destroy(FeaturedList $carrusele)
    {
        $carrusele->items()->delete();
        $carrusele->delete();
        $this->clearCarouselCache();

        return redirect()->route('librarian.carruseles.index')
            ->with('success', 'Carrusel eliminado correctamente.');
    }

    // ── Gestión de items ────────────────────────────────────────────

    public function addItem(Request $request, FeaturedList $list)
    {
        $request->validate([
            'itemable_type' => 'required|in:book,magazine,author,collection',
            'itemable_id'   => 'required|integer|min:1',
        ]);

        $morphMap = [
            'book'       => Book::class,
            'magazine'   => Magazine::class,
            'author'     => Author::class,
            'collection' => Collection::class,
        ];

        $modelClass = $morphMap[$request->itemable_type];

        // Verificar que el elemento existe
        if (!$modelClass::find($request->itemable_id)) {
            return back()->with('error', 'El elemento no existe.');
        }

        // Verificar que no está duplicado
        $exists = FeaturedListItem::where('featured_list_id', $list->id)
            ->where('itemable_type', $modelClass)
            ->where('itemable_id', $request->itemable_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este elemento ya está en el carrusel.');
        }

        $lastOrder = $list->items()->max('order') ?? 0;

        FeaturedListItem::create([
            'featured_list_id' => $list->id,
            'itemable_type'    => $modelClass,
            'itemable_id'      => $request->itemable_id,
            'order'            => $lastOrder + 1,
        ]);

        $this->rebuildCircularLinks($list);
        $this->clearCarouselCache();

        return back()->with('success', 'Elemento añadido al carrusel.');
    }

    public function removeItem(FeaturedList $list, FeaturedListItem $item)
    {
        $item->delete();
        $this->rebuildCircularLinks($list);
        $this->clearCarouselCache();

        return back()->with('success', 'Elemento eliminado del carrusel.');
    }

    public function reorder(Request $request, FeaturedList $list)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*' => 'integer|exists:featured_list_items,id',
        ]);

        foreach ($request->items as $order => $itemId) {
            FeaturedListItem::where('id', $itemId)
                ->update(['order' => $order + 1]);
        }

        $this->rebuildCircularLinks($list);
        $this->clearCarouselCache();

        return response()->json(['success' => true]);
    }

    // Búsqueda AJAX de elementos para el panel derecho
    public function searchItems(Request $request, FeaturedList $list)
    {
        $query = $request->get('q', '');
        $results = collect();

        switch ($list->type) {
            case 'books':
                $results = Book::with('author')
                    ->where(fn($q) => $q
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('isbn', 'like', "%{$query}%"))
                    ->orderBy('title')
                    ->limit(20)
                    ->get()
                    ->map(fn($b) => [
                        'id'       => $b->id,
                        'type'     => 'book',
                        'title'    => $b->title,
                        'subtitle' => $b->author?->name ?? '',
                        'cover'    => $b->cover_url,
                        'isbn'     => $b->isbn,
                    ]);
                break;

            case 'magazines':
                $results = Magazine::with('authors')
                    ->where(fn($q) => $q
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('issn', 'like', "%{$query}%"))
                    ->orderBy('title')
                    ->limit(20)
                    ->get()
                    ->map(fn($m) => [
                        'id'       => $m->id,
                        'type'     => 'magazine',
                        'title'    => $m->title,
                        'subtitle' => $m->authors->pluck('name')->join(', '),
                        'cover'    => $m->cover_url,
                        'isbn'     => $m->issn ?? '—',
                    ]);
                break;

            case 'books_magazines':
                $books = Book::with('author')
                    ->where(fn($q) => $q
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('isbn', 'like', "%{$query}%"))
                    ->limit(10)->get()
                    ->map(fn($b) => [
                        'id' => $b->id,
                        'type' => 'book',
                        'title' => $b->title,
                        'subtitle' => $b->author?->name ?? '',
                        'cover' => $b->cover_url,
                        'isbn'  => $b->isbn,
                    ]);

                $magazines = Magazine::with('authors')
                    ->where('title', 'like', "%{$query}%")
                    ->limit(10)->get()
                    ->map(fn($m) => [
                        'id' => $m->id,
                        'type' => 'magazine',
                        'title' => $m->title,
                        'subtitle' => $m->authors->pluck('name')->join(', '),
                        'cover' => $m->cover_url,
                        'isbn'  => $m->issn ?? '—',
                    ]);

                $results = $books->concat($magazines)->sortBy('title')->values();
                break;

            case 'authors':
                $results = Author::where('name', 'like', "%{$query}%")
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(fn($a) => [
                        'id'       => $a->id,
                        'type'     => 'author',
                        'title'    => $a->name,
                        'subtitle' => $a->nationality ?? '',
                        'cover'    => $a->image_url,
                        'isbn'     => '',
                    ]);
                break;

            case 'collections':
                $results = Collection::where('name', 'like', "%{$query}%")
                    ->withCount('books')
                    ->orderBy('name')
                    ->limit(20)
                    ->get()
                    ->map(fn($c) => [
                        'id'       => $c->id,
                        'type'     => 'collection',
                        'title'    => $c->name,
                        'subtitle' => $c->books_count . ' libro(s)',
                        'cover'    => $c->cover_image
                            ? asset('storage/' . $c->cover_image)
                            : asset('images/default-cover.png'),
                        'isbn'     => '',
                    ]);
                break;
        }

        // Marcar los que ya están en el carrusel
        $existing = FeaturedListItem::where('featured_list_id', $list->id)
            ->get()
            ->mapWithKeys(fn($i) => [class_basename($i->itemable_type) . '_' . $i->itemable_id => true]);

        $results = $results->map(function ($item) use ($existing) {
            $key = ucfirst($item['type']) . '_' . $item['id'];
            $item['in_carousel'] = isset($existing[$key]);
            return $item;
        })->values();

        return response()->json($results);
    }

    // ── Helpers privados ───────────────────────────────────────────

    private function getAvailableItems(string $type): array
    {
        return match ($type) {
            'books'          => Book::with('author')->orderBy('title')->limit(30)->get()->toArray(),
            'magazines'      => Magazine::with('authors')->orderBy('title')->limit(30)->get()->toArray(),
            'authors'        => Author::orderBy('name')->limit(30)->get()->toArray(),
            'collections'    => Collection::withCount('books')->orderBy('name')->limit(30)->get()->toArray(),
            'books_magazines' => [
                'books'    => Book::with('author')->orderBy('title')->limit(15)->get()->toArray(),
                'magazines' => Magazine::orderBy('title')->limit(15)->get()->toArray(),
            ],
            default => [],
        };
    }

    private function rebuildCircularLinks(FeaturedList $list): void
    {
        $items = $list->items()->orderBy('order')->get();
        $count = $items->count();
        if ($count === 0) return;

        foreach ($items as $index => $item) {
            $item->update([
                'previous_item_id' => $items[($index - 1 + $count) % $count]->id,
                'next_item_id'     => $items[($index + 1) % $count]->id,
            ]);
        }
    }

    private function clearCarouselCache(): void
    {
        foreach (['home', 'books', 'magazines', 'collections', 'authors'] as $section) {
            Cache::forget("{$section}_featured_lists");
        }
    }
}
