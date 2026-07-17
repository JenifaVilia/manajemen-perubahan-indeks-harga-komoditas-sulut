<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komoditas_wilayah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komoditas_id')->constrained('komoditas')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayahs')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'pending_tambah', 'pending_hapus', 'nonaktif'])->default('aktif');
            $table->timestamp('requested_at')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('alasan_pengajuan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_approval')->nullable();
            $table->timestamps();

            $table->unique(['komoditas_id', 'wilayah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komoditas_wilayah');
    }
};
