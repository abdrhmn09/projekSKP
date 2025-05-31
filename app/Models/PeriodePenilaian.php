<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodePenilaian extends Model
{
    use HasFactory;

    protected $table = 'periode_penilaian';

    protected $fillable = [
        'nama_periode',
        'jenis_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function sasaranKerja(): HasMany
    {
        return $this->hasMany(SasaranKerja::class, 'periode_id');
    }

    public function penilaianPerilaku(): HasMany
    {
        return $this->hasMany(PenilaianPerilaku::class, 'periode_id');
    }

    public function penilaianSkp(): HasMany
    {
        return $this->hasMany(PenilaianSkp::class, 'periode_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
