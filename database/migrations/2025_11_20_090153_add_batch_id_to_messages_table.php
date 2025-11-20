<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Kolom nullable karena pesan manual tidak punya batch
            $table->foreignId('message_batch_id')->nullable()->constrained('message_batches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['message_batch_id']);
            $table->dropColumn('message_batch_id');
        });
    }
};