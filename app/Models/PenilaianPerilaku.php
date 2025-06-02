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
        'penilaian_skp_id',
        'aspek_perilaku',
        'skor',
    ];

    // Relationships
    public function penilaianSkp(): BelongsTo
    {
        return $this->belongsTo(PenilaianSkp::class, 'penilaian_skp_id');
    }
}
