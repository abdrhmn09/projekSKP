<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SasaranKerja extends Model
{
    use HasFactory;

    protected $table = 'sasaran_kerja';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'uraian_sasaran',
        'indikator_kinerja',
        'target_kuantitas',
        'satuan_kuantitas',
        'target_kualitas',
        'target_waktu',
        'target_biaya',
        'bobot_persen',
        'status',
        'catatan',
    ];

    protected $casts = [
        'target_kualitas' => 'decimal:2',
        'target_waktu' => 'date',
        'target_biaya' => 'decimal:2',
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

    public function realisasiKerja(): HasMany
    {
        return $this->hasMany(RealisasiKerja::class);
    }

    // Scopes
    public function scopeByStatus(Builder $query, $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForPegawai(Builder $query, int $pegawaiId): Builder
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    public function scopeForPeriode(Builder $query, int $periodeId): Builder
    {
        return $query->where('periode_id', $periodeId);
    }
}
