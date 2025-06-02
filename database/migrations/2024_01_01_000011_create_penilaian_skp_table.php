<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_skp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->foreignId('sasaran_kerja_id')->nullable()->constrained('sasaran_kerja')->onDelete('cascade');
            $table->json('detail_penilaian')->nullable();
            $table->decimal('nilai_rata_rata_realisasi', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('kategori_nilai', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang'])->nullable();
            $table->text('catatan_kepala_sekolah')->nullable();
            $table->text('feedback_perilaku')->nullable();
            $table->foreignId('penilai_id')->nullable()->constrained('users');
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamp('tanggal_penilaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_skp');
    }
};
