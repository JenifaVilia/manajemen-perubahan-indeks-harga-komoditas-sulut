<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_komoditas', 20)->unique();
            $table->string('nama_komoditas');
            $table->string('satuan', 30)->default('unit');
            $table->string('kelompok')->nullable();
            $table->string('subkelompok')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
