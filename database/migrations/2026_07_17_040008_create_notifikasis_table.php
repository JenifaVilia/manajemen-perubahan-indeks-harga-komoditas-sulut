<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', [
                'input_alasan',
                'approval_komoditas',
                'periode_buka',
                'periode_tutup',
                'revisi_alasan',
                'alasan_disetujui',
                'reminder_deadline',
                'umum',
            ])->default('umum');
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('referensi_tipe')->nullable(); // model class name
            $table->string('url')->nullable(); // direct link to action
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
