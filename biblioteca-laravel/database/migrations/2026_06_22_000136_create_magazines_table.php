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
        Schema::create('magazines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('issn')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pdf_path'); // obligatorio
            $table->integer('volume')->nullable();
            $table->integer('issue_number')->nullable();
            $table->date('published_date')->nullable();
            $table->string('language')->default('Español');
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            // Métricas
            $table->integer('views')->default(0);
            $table->integer('loan_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazines');
    }
};
