<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alasan_perubahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayahs')->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained('komoditas')->cascadeOnDelete();

            $table->text('alasan');
            $table->json('faktor_pendorong')->nullable(); // array of factors
            $table->text('rekomendasi')->nullable();

            $table->enum('status', ['draft', 'submitted', 'disetujui', 'revisi'])->default('draft');

            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('catatan_provinsi')->nullable();

            $table->timestamps();

            $table->unique(['periode_id', 'wilayah_id', 'komoditas_id'], 'alasan_unique');
            $table->index(['periode_id', 'wilayah_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alasan_perubahans');
    }
};
