<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class PenilaianSkp extends Model
{
    use HasFactory;

    protected $table = 'penilaian_skp';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'sasaran_kerja_id',
        'nilai_rata_rata_realisasi',
        'detail_penilaian',
        'nilai_akhir',
        'kategori_nilai',
        'catatan_kepala_sekolah',
        'feedback_perilaku',
        'penilai_id',
        'status',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'nilai_rata_rata_realisasi' => 'decimal:2',
        'detail_penilaian' => 'array',
        'nilai_akhir' => 'decimal:2',
        'tanggal_penilaian' => 'datetime',
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

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function rencanaTindakLanjut(): HasMany
    {
        return $this->hasMany(RencanaTindakLanjut::class);
    }

    public function sasaranKerja(): BelongsTo
    {
        return $this->belongsTo(SasaranKerja::class, 'sasaran_kerja_id');
    }

    public function penilaianPerilaku(): HasMany
    {
        return $this->hasMany(PenilaianPerilaku::class, 'penilaian_skp_id');
    }

    // Scopes
    public function scopeForPegawai(Builder $query, int $pegawaiId): Builder
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    public function scopeForPeriode(Builder $query, int $periodeId): Builder
    {
        return $query->where('periode_id', $periodeId);
    }
}
