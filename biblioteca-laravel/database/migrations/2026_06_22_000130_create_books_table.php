<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pdf_path'); // obligatorio
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->integer('pages')->nullable();
            $table->year('published_year')->nullable();
            $table->string('language')->default('Español');
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            // Lista doblemente enlazada para sagas
            $table->foreignId('previous_book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('next_book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('saga_order')->nullable(); // posición dentro de la saga
            // Métricas
            $table->integer('views')->default(0);
            $table->integer('loan_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
