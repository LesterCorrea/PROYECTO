<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Magazine;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, string $type, int $id)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'rating'  => 'nullable|integer|min:1|max:5',
        ]);

        $model = match($type) {
            'libro'   => Book::findOrFail($id),
            'revista' => Magazine::findOrFail($id),
            default   => abort(404),
        };

        $model->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'rating'  => $request->rating,
        ]);

        return back()->with('success', 'Comentario publicado correctamente.');
    }

    public function destroy(Comment $comment)
    {
        // Solo el autor o admin/bibliotecario pueden eliminar
        if (auth()->id() !== $comment->user_id && !auth()->user()->hasRole(['admin', 'librarian'])) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comentario eliminado.');
    }
}