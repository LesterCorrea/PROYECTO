<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\Magazine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessPdfUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Reintentos si falla
    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public string $modelType, // 'book' o 'magazine'
        public int    $modelId,
        public string $pdfPath
    ) {}

    public function handle(): void
    {
        $model = match($this->modelType) {
            'book'     => Book::find($this->modelId),
            'magazine' => Magazine::find($this->modelId),
            default    => null,
        };

        if (!$model) {
            Log::warning("ProcessPdfUpload: modelo no encontrado.", [
                'type' => $this->modelType,
                'id'   => $this->modelId,
            ]);
            return;
        }

        if (!Storage::disk('local')->exists($this->pdfPath)) {
            Log::error("ProcessPdfUpload: PDF no encontrado en disco.", [
                'path' => $this->pdfPath,
            ]);
            return;
        }

        // Obtener tamaño del archivo
        $sizeBytes = Storage::disk('local')->size($this->pdfPath);
        $sizeMB    = round($sizeBytes / 1048576, 2);

        Log::info("ProcessPdfUpload: PDF procesado correctamente.", [
            'model'   => $this->modelType,
            'id'      => $this->modelId,
            'size_mb' => $sizeMB,
            'path'    => $this->pdfPath,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessPdfUpload: Job falló.", [
            'model'     => $this->modelType,
            'id'        => $this->modelId,
            'error'     => $exception->getMessage(),
        ]);
    }
}