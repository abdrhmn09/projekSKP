
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
            $table->decimal('nilai_skp', 5, 2);
            $table->decimal('nilai_perilaku', 5, 2);
            $table->decimal('nilai_akhir', 5, 2);
            $table->enum('kategori_nilai', ['Sangat Baik', 'Baik', 'Butuh Perbaikan', 'Kurang', 'Sangat Kurang']);
            $table->text('catatan_penilaian')->nullable();
            $table->foreignId('penilai_id')->constrained('users');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamp('tanggal_penilaian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_skp');
    }
};
