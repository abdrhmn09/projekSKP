
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nip',
        'phone',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function penilaianSkpSebagaiPenilai(): HasMany
    {
        return $this->hasMany(PenilaianSkp::class, 'penilai_id');
    }

    public function penilaianPerilakuSebagaiPenilai(): HasMany
    {
        return $this->hasMany(PenilaianPerilaku::class, 'penilai_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isPegawai(): bool
    {
        return in_array($this->role, ['guru', 'staff']);
    }
}
