<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir copias a magazines
        Schema::table('magazines', function (Blueprint $table) {
            $table->integer('total_copies')->default(1)->after('loan_count');
            $table->integer('available_copies')->default(1)->after('total_copies');
        });

        // Añadir magazine_id a loans
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('magazine_id')
                ->nullable()
                ->after('book_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->dropColumn(['total_copies', 'available_copies']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Magazine::class);
            $table->dropColumn('magazine_id');
        });
    }
};
