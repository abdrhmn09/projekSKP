<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiKerja extends Model
{
    use HasFactory;

    protected $table = 'realisasi_kerja';

    protected $fillable = [
        'sasaran_kerja_id',
        'uraian_realisasi',
        'realisasi_kuantitas',
        'realisasi_kualitas',
        'realisasi_waktu',
        'realisasi_biaya',
        'nilai_capaian',
        'bukti_dukung',
        'status',
    ];

    protected $casts = [
        'realisasi_kualitas' => 'decimal:2',
        'realisasi_waktu' => 'date',
        'realisasi_biaya' => 'decimal:2',
        'nilai_capaian' => 'decimal:2',
    ];

    // Relationships
    public function sasaranKerja(): BelongsTo
    {
        return $this->belongsTo(SasaranKerja::class);
    }
}
