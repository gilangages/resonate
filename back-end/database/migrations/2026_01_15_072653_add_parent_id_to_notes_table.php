<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Self-referencing foreign key.
            // Nullable artinya jika kosong, dia adalah Main Note. Jika terisi, dia adalah Reply.
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('notes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
