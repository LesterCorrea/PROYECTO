<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Author extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'nationality',
        'bio',
        'image',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'views' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'nationality']);
    }

    // Imagen con fallback al avatar predeterminado
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(storage_path('app/public/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-author.png');
    }

    // Relaciones
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function magazines()
    {
        return $this->belongsToMany(Magazine::class, 'magazine_author')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}