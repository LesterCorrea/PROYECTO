<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Hacemos que book_id permita valores nulos
            $table->unsignedBigInteger('book_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Revertir a no nulo en caso de hacer rollback (opcional, pero buena práctica)
            $table->unsignedBigInteger('book_id')->nullable(false)->change();
        });
    }
};