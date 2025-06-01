<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sasaran_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periode_penilaian')->onDelete('cascade');
            $table->string('kode_sasaran');
            $table->text('uraian_kegiatan');
            $table->text('target_kuantitas');
            $table->text('target_kualitas');
            $table->date('target_waktu');
            $table->decimal('bobot_persen', 5, 2);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sasaran_kerja');
    }
};
