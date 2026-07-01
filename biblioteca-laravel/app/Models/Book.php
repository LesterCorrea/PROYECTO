<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Book extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'cover_image',
        'pdf_path',
        'total_copies',
        'available_copies',
        'pages',
        'published_year',
        'language',
        'author_id',
        'publisher_id',
        'category_id',
        'previous_book_id',
        'next_book_id',
        'collection_id',
        'saga_order',
        'views',
        'loan_count',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'views'        => 'integer',
            'loan_count'   => 'integer',
            'is_featured'  => 'boolean',
            'available_copies' => 'integer',
            'total_copies' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['title', 'isbn', 'available_copies']);
    }

    // ─── Relaciones base ────────────────────────────────────────────
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    // ─── Lista doblemente enlazada (saga) ───────────────────────────
    public function previousBook()
    {
        return $this->belongsTo(Book::class, 'previous_book_id');
    }

    public function nextBook()
    {
        return $this->belongsTo(Book::class, 'next_book_id');
    }

    // Obtiene todos los libros de la saga en orden
    public function getSagaBooksAttribute()
    {
        if (!$this->collection_id) return collect();
        return Book::where('collection_id', $this->collection_id)
                   ->orderBy('saga_order')
                   ->get();
    }

    // ─── Otras relaciones ────────────────────────────────────────────
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function views()
    {
        return $this->morphMany(BookView::class, 'viewable');
    }

    public function readingProgress()
    {
        return $this->morphMany(ReadingProgress::class, 'readable');
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-cover.png');
    }
}