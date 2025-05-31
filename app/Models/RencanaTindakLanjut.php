<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RencanaTindakLanjut extends Model
{
    use HasFactory;

    protected $table = 'rencana_tindak_lanjut';

    protected $fillable = [
        'penilaian_skp_id',
        'rencana_perbaikan',
        'strategi_pencapaian',
        'target_penyelesaian',
        'indikator_keberhasilan',
        'status',
        'catatan_progress',
    ];

    protected $casts = [
        'target_penyelesaian' => 'date',
    ];

    public function penilaianSkp(): BelongsTo
    {
        return $this->belongsTo(PenilaianSkp::class);
    }
}
