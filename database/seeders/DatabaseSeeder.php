<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PerilakuKerja;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sma.com',
            'nip' => 'ADM001',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Create Kepala Sekolah
        $kepala = User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepala@sma.com',
            'nip' => 'KS001',
            'phone' => '081234567891',
            'role' => 'kepala_sekolah',
            'password' => Hash::make('kepala123'),
        ]);

        // Create Jabatan
        $jabatanKepala = Jabatan::create([
            'nama_jabatan' => 'Kepala Sekolah',
            'kode_jabatan' => 'KS',
            'deskripsi' => 'Pimpinan sekolah',
            'tunjangan_jabatan' => 2000000,
        ]);

        $jabatanGuru = Jabatan::create([
            'nama_jabatan' => 'Guru',
            'kode_jabatan' => 'GR',
            'deskripsi' => 'Tenaga pendidik',
            'tunjangan_jabatan' => 500000,
        ]);

        $jabatanStaff = Jabatan::create([
            'nama_jabatan' => 'Staff Administrasi',
            'kode_jabatan' => 'SA',
            'deskripsi' => 'Tenaga kependidikan',
            'tunjangan_jabatan' => 300000,
        ]);

        // Create Pegawai for Kepala Sekolah
        Pegawai::create([
            'user_id' => $kepala->id,
            'jabatan_id' => $jabatanKepala->id,
            'nama_lengkap' => 'Dr. Kepala Sekolah, S.Pd., M.Pd.',
            'tanggal_lahir' => '1975-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pendidikan No. 1',
            'pendidikan_terakhir' => 'S2 Manajemen Pendidikan',
            'tanggal_masuk_kerja' => '2010-01-01',
            'status_kepegawaian' => 'PNS',
            'golongan' => 'IV/c',
        ]);

        // Create Sample Guru
        $guru = User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru@sma.com',
            'nip' => 'GR001',
            'phone' => '081234567892',
            'role' => 'guru',
            'password' => Hash::make('guru123'),
        ]);

        Pegawai::create([
            'user_id' => $guru->id,
            'jabatan_id' => $jabatanGuru->id,
            'nama_lengkap' => 'Guru Matematika, S.Pd.',
            'tanggal_lahir' => '1985-08-20',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Pendidikan No. 5',
            'pendidikan_terakhir' => 'S1 Pendidikan Matematika',
            'tanggal_masuk_kerja' => '2015-07-01',
            'status_kepegawaian' => 'PNS',
            'golongan' => 'III/b',
        ]);

        // Create Sample Staff
        $staff = User::create([
            'name' => 'Staff Administrasi',
            'email' => 'staff@sma.com',
            'nip' => 'SA001',
            'phone' => '081234567893',
            'role' => 'staff',
            'password' => Hash::make('staff123'),
        ]);

        Pegawai::create([
            'user_id' => $staff->id,
            'jabatan_id' => $jabatanStaff->id,
            'nama_lengkap' => 'Staff Administrasi',
            'tanggal_lahir' => '1990-03-10',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Administrasi No. 3',
            'pendidikan_terakhir' => 'D3 Administrasi',
            'tanggal_masuk_kerja' => '2018-02-15',
            'status_kepegawaian' => 'PPPK',
            'golongan' => 'II/c',
        ]);

        // Create Perilaku Kerja Standards
        PerilakuKerja::create([
            'nama_perilaku' => 'Integritas',
            'deskripsi' => 'Kemampuan untuk bertindak sesuai dengan nilai, norma dan etika dalam organisasi',
            'bobot_nilai' => 100,
        ]);

        PerilakuKerja::create([
            'nama_perilaku' => 'Kerjasama',
            'deskripsi' => 'Kemampuan untuk bekerja sama dengan rekan kerja dan pimpinan',
            'bobot_nilai' => 100,
        ]);

        PerilakuKerja::create([
            'nama_perilaku' => 'Komunikasi',
            'deskripsi' => 'Kemampuan untuk menyampaikan informasi secara efektif',
            'bobot_nilai' => 100,
        ]);

        PerilakuKerja::create([
            'nama_perilaku' => 'Orientasi Pelayanan',
            'deskripsi' => 'Kemampuan untuk memberikan pelayanan yang memuaskan kepada pengguna layanan',
            'bobot_nilai' => 100,
        ]);

        PerilakuKerja::create([
            'nama_perilaku' => 'Komitmen',
            'deskripsi' => 'Kemampuan untuk dapat menyelaraskan perilaku kerja dengan kepentingan organisasi',
            'bobot_nilai' => 100,
        ]);
    }
}
