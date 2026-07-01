<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedList extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'sections',
        'type',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sections'  => 'array', // JSON → array automáticamente
        ];
    }

    public function items()
    {
        return $this->hasMany(FeaturedListItem::class)->orderBy('order');
    }

    public function firstItem()
    {
        return $this->hasOne(FeaturedListItem::class)->orderBy('order');
    }

    // Etiqueta legible del tipo
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'books'          => 'Libros',
            'magazines'      => 'Revistas',
            'authors'        => 'Autores',
            'collections'    => 'Colecciones',
            'books_magazines' => 'Libros y Revistas',
            default          => $this->type,
        };
    }

    // Etiquetas legibles de las secciones
    public function getSectionLabelsAttribute(): string
    {
        $map = [
            'home'        => 'Inicio',
            'books'       => 'Libros',
            'magazines'   => 'Revistas',
            'collections' => 'Colecciones',
            'authors'     => 'Autores',
        ];

        return collect($this->sections ?? [])
            ->map(fn($s) => $map[$s] ?? $s)
            ->join(', ');
    }

    // Verificar si aparece en una sección específica
    public function appearsIn(string $section): bool
    {
        return in_array($section, $this->sections ?? []);
    }
}
