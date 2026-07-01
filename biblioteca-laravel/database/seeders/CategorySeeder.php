<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Libros
            ['name' => 'Novela',          'color' => '#6366F1', 'type' => 'book'],
            ['name' => 'Ciencia Ficción', 'color' => '#8B5CF6', 'type' => 'book'],
            ['name' => 'Historia',        'color' => '#D97706', 'type' => 'book'],
            ['name' => 'Filosofía',       'color' => '#059669', 'type' => 'book'],
            ['name' => 'Poesía',          'color' => '#EC4899', 'type' => 'book'],
            ['name' => 'Biografía',       'color' => '#0EA5E9', 'type' => 'book'],
            ['name' => 'Terror',          'color' => '#DC2626', 'type' => 'book'],
            ['name' => 'Romance',         'color' => '#F43F5E', 'type' => 'book'],
            ['name' => 'Aventura',        'color' => '#F59E0B', 'type' => 'book'],
            ['name' => 'Tecnología',      'color' => '#3B82F6', 'type' => 'book'],

            // Revistas
            ['name' => 'Ciencia',         'color' => '#10B981', 'type' => 'magazine'],
            ['name' => 'Cultura',         'color' => '#F97316', 'type' => 'magazine'],
            ['name' => 'Política',        'color' => '#EF4444', 'type' => 'magazine'],
            ['name' => 'Deportes',        'color' => '#84CC16', 'type' => 'magazine'],
            ['name' => 'Arte',            'color' => '#A855F7', 'type' => 'magazine'],

            // Ambos
            ['name' => 'Educación',       'color' => '#06B6D4', 'type' => 'both'],
            ['name' => 'Infantil',        'color' => '#FBBF24', 'type' => 'both'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}