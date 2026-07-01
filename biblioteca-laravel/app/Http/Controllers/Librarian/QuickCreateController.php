<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuickCreateController extends Controller
{
    public function author(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
        ]);

        $author = Author::create($request->only('name', 'nationality'));

        return response()->json(['id' => $author->id, 'name' => $author->name]);
    }

    public function category(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:categories,name',
            'color' => 'required|string|size:7',
            'type'  => 'required|in:book,magazine,both',
        ]);

        $category = Category::create([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'color' => $request->color,
            'type'  => $request->type,
        ]);

        return response()->json(['id' => $category->id, 'name' => $category->name]);
    }

    public function publisher(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:publishers,name',
            'country' => 'nullable|string|max:100',
        ]);

        $publisher = Publisher::create($request->only('name', 'country'));

        return response()->json(['id' => $publisher->id, 'name' => $publisher->name]);
    }

    public function collection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:collections,name',
        ]);

        $collection = Collection::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json(['id' => $collection->id, 'name' => $collection->name]);
    }
}
