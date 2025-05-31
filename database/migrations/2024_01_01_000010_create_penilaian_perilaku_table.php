
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_perilaku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai');
            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->foreignId('perilaku_kerja_id')->constrained('perilaku_kerja');
            $table->integer('nilai_perilaku');
            $table->text('catatan_penilaian')->nullable();
            $table->foreignId('penilai_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_perilaku');
    }
};
