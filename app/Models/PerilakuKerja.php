
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerilakuKerja extends Model
{
    use HasFactory;

    protected $table = 'perilaku_kerja';

    protected $fillable = [
        'nama_perilaku',
        'deskripsi',
        'bobot_nilai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function penilaianPerilaku(): HasMany
    {
        return $this->hasMany(PenilaianPerilaku::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
