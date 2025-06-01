<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sasaran_kerja_id')->constrained('sasaran_kerja')->onDelete('cascade');
            $table->text('uraian_realisasi');
            $table->integer('realisasi_kuantitas');
            $table->decimal('realisasi_kualitas', 5, 2);
            $table->date('realisasi_waktu');
            $table->decimal('realisasi_biaya', 12, 2)->nullable();
            $table->decimal('nilai_capaian', 5, 2)->nullable();
            $table->text('bukti_dukung')->nullable();
            $table->string('bukti_pendukung')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_kerja');
    }
};
