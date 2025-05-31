
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
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->string('uraian_sasaran');
            $table->text('indikator_kinerja');
            $table->integer('target_kuantitas');
            $table->string('satuan_kuantitas');
            $table->decimal('target_kualitas', 5, 2);
            $table->date('target_waktu');
            $table->decimal('target_biaya', 15, 2)->nullable();
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
