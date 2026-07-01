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
        Schema::create('featured_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_list_id')->constrained()->cascadeOnDelete();
            $table->morphs('itemable'); // book, magazine, author, collection
            $table->integer('order')->default(0);
            // Lista circular doblemente enlazada
            $table->foreignId('previous_item_id')->nullable()->constrained('featured_list_items')->nullOnDelete();
            $table->foreignId('next_item_id')->nullable()->constrained('featured_list_items')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_list_items');
    }
};
