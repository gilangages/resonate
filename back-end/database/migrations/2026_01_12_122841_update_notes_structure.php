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
        Schema::table('notes', function (Blueprint $table) {
            // 1. Tambah kolom baru 'initial_name' (nullable)
            $table->string('initial_name')->nullable()->after('recipient');

            // 2. Hapus kolom lama 'is_anonymous' karena sudah diganti fungsinya
            $table->dropColumn('is_anonymous');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Balikin lagi kalo di-rollback
            $table->dropColumn('initial_name');
            $table->boolean('is_anonymous')->default(false);
        });
    }
};
