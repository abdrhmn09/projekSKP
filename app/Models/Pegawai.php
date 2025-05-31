
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'jabatan_id',
        'nama_lengkap',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'pendidikan_terakhir',
        'tanggal_masuk_kerja',
        'status_kepegawaian',
        'golongan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk_kerja' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function sasaranKerja(): HasMany
    {
        return $this->hasMany(SasaranKerja::class);
    }

    public function penilaianPerilaku(): HasMany
    {
        return $this->hasMany(PenilaianPerilaku::class);
    }

    public function penilaianSkp(): HasMany
    {
        return $this->hasMany(PenilaianSkp::class);
    }
}
