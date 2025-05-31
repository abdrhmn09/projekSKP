<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianPerilaku extends Model
{
    use HasFactory;

    protected $table = 'penilaian_perilaku';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'perilaku_kerja_id',
        'nilai_perilaku',
        'catatan_penilaian',
        'penilai_id',
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

    public function perilakuKerja(): BelongsTo
    {
        return $this->belongsTo(PerilakuKerja::class);
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }
}
