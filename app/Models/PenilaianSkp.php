<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenilaianSkp extends Model
{
    use HasFactory;

    protected $table = 'penilaian_skp';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'nilai_skp',
        'nilai_perilaku',
        'nilai_akhir',
        'kategori_nilai',
        'catatan_penilaian',
        'penilai_id',
        'status',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'nilai_skp' => 'decimal:2',
        'nilai_perilaku' => 'decimal:2',
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
}
