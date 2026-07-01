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
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('readable'); // book o magazine
            $table->integer('current_page')->default(1);
            $table->integer('total_pages')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->timestamp('last_read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'readable_id', 'readable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
