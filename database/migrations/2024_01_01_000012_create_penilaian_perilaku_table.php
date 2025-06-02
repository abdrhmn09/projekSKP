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
            $table->foreignId('penilaian_skp_id')->constrained('penilaian_skp')->onDelete('cascade');
            $table->string('aspek_perilaku');
            $table->integer('skor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_perilaku');
    }
};
