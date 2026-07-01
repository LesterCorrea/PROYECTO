<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('featured_lists', function (Blueprint $table) {
            // Reemplazar section (string) por sections (JSON)
            $table->dropColumn('section');
            $table->json('sections')->after('description');

            // Tipo de elementos que acepta
            $table->enum('type', [
                'books',
                'magazines',
                'authors',
                'collections',
                'books_magazines',
            ])->default('books')->after('sections');
        });
    }

    public function down(): void
    {
        Schema::table('featured_lists', function (Blueprint $table) {
            $table->dropColumn(['sections', 'type']);
            $table->string('section')->default('home');
        });
    }
};