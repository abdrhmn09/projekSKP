
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_skp_id')->constrained('penilaian_skp');
            $table->text('rencana_perbaikan');
            $table->text('strategi_pencapaian');
            $table->date('target_penyelesaian');
            $table->text('indikator_keberhasilan');
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->text('catatan_progress')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_tindak_lanjut');
    }
};
