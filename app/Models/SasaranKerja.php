<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SasaranKerja extends Model
{
    use HasFactory;

    protected $table = 'sasaran_kerja';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'kode_sasaran',
        'uraian_kegiatan',
        'target_kuantitas',
        'target_kualitas',
        'target_waktu',
        'bobot_persen',
        'status',
        'catatan',
    ];

    protected $casts = [
        'bobot_persen' => 'decimal:2',
    ];

    // Relationships
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function realisasi(): HasOne
    {
        return $this->hasOne(RealisasiKerja::class, 'sasaran_kerja_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
