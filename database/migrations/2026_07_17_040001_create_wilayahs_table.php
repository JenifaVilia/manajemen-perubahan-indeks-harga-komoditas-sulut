<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_wilayah', 10)->unique();
            $table->string('nama_wilayah');
            $table->enum('tipe', ['provinsi', 'kabupaten', 'kota']);
            $table->foreignId('parent_id')->nullable()->constrained('wilayahs')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayahs');
    }
};
