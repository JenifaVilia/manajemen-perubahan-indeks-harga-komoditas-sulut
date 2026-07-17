<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayahs')->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained('komoditas')->cascadeOnDelete();
            $table->enum('tipe_indeks', ['IHK', 'IHPB', 'IPP', 'IPH'])->default('IHK');

            // Nilai harga level
            $table->decimal('harga_level', 12, 4)->nullable();

            // Inflasi / perubahan
            $table->decimal('inflasi_mtm', 8, 4)->nullable(); // Month-to-Month
            $table->decimal('inflasi_ytd', 8, 4)->nullable(); // Year-to-Date
            $table->decimal('inflasi_yoy', 8, 4)->nullable(); // Year-on-Year

            // Andil
            $table->decimal('andil_mtm', 8, 4)->nullable();
            $table->decimal('andil_ytd', 8, 4)->nullable();
            $table->decimal('andil_yoy', 8, 4)->nullable();

            // Metadata upload
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sumber_file')->nullable(); // nama file Excel yang diupload
            $table->timestamps();

            $table->unique(['periode_id', 'wilayah_id', 'komoditas_id', 'tipe_indeks'], 'data_harga_unique');
            $table->index(['periode_id', 'wilayah_id']);
            $table->index(['komoditas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_hargas');
    }
};
