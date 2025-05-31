<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
        'kode_jabatan',
        'deskripsi',
        'tunjangan_jabatan',
    ];

    protected $casts = [
        'tunjangan_jabatan' => 'decimal:2',
    ];

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
}
