<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Magazine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'issn',
        'description',
        'cover_image',
        'pdf_path',
        'volume',
        'issue_number',
        'published_date',
        'language',
        'publisher_id',
        'category_id',
        'views',
        'loan_count',
        'total_copies',
        'available_copies',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'views'          => 'integer',
            'loan_count'     => 'integer',
            'total_copies'     => 'integer',
            'available_copies' => 'integer',
        ];
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'magazine_author')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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

    // ── Helpers ─────────────────────────────────────────────────────
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
