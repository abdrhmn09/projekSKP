
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perilaku_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perilaku');
            $table->text('deskripsi');
            $table->integer('bobot_nilai')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perilaku_kerja');
    }
};
