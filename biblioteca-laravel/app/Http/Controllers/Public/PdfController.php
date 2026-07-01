<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Magazine;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    /**
     * Servir el PDF de forma protegida.
     * Nunca se expone la URL real del archivo.
     */
    public function serve(string $type, int $id)
    {
        $model = match ($type) {
            'libro'   => Book::findOrFail($id),
            'revista' => Magazine::findOrFail($id),
            default   => abort(404),
        };

        if (!Storage::disk('local')->exists($model->pdf_path)) {
            abort(404, 'Archivo PDF no encontrado.');
        }

        // Registrar progreso si no existe
        ReadingProgress::firstOrCreate(
            [
                'user_id'       => auth()->id(),
                'readable_id'   => $model->id,
                'readable_type' => get_class($model),
            ],
            [
                'current_page' => 1,
                'total_pages'  => 0,
                'percentage'   => 0,
                'last_read_at' => now(),
            ]
        );

        $path     = Storage::disk('local')->path($model->pdf_path);
        $filename = $model->title . '.pdf';

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'X-Frame-Options'     => 'SAMEORIGIN',
        ]);
    }

    /**
     * Actualizar progreso de lectura (llamado por AJAX desde el visor).
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'type'         => 'required|in:libro,revista',
            'id'           => 'required|integer',
            'current_page' => 'required|integer|min:1',
            'total_pages'  => 'required|integer|min:1',
        ]);

        $modelClass = $request->type === 'libro' ? Book::class : Magazine::class;

        $percentage = round(($request->current_page / $request->total_pages) * 100, 2);

        ReadingProgress::updateOrCreate(
            [
                'user_id'       => auth()->id(),
                'readable_id'   => $request->id,
                'readable_type' => $modelClass,
            ],
            [
                'current_page' => $request->current_page,
                'total_pages'  => $request->total_pages,
                'percentage'   => $percentage,
                'last_read_at' => now(),
            ]
        );

        return response()->json(['percentage' => $percentage]);
    }

    public function reader(string $type, int $id)
    {
        $book = match ($type) {
            'libro'   => \App\Models\Book::with(['author'])->findOrFail($id),
            'revista' => \App\Models\Magazine::with(['authors'])->findOrFail($id),
            default   => abort(404),
        };

        $readingProgress = \App\Models\ReadingProgress::where('user_id', auth()->id())
            ->where('readable_id', $id)
            ->where('readable_type', get_class($book))
            ->first();

        return view('public.books.reader', compact('book', 'readingProgress', 'type'));
    }
}
