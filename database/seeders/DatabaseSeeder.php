<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PerilakuKerja;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\Hash;

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
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create jabatan
        $jabatanKepala = Jabatan::create([
            'nama_jabatan' => 'Kepala Sekolah',
            'kode_jabatan' => 'KS',
            'deskripsi' => 'Pimpinan sekolah',
            'tunjangan_jabatan' => 2000000,
        ]);

        $jabatanWakil = Jabatan::create([
            'nama_jabatan' => 'Wakil Kepala Sekolah',
            'kode_jabatan' => 'WK',
            'deskripsi' => 'Wakil kepala sekolah',
            'tunjangan_jabatan' => 1500000,
        ]);

        $jabatanGuru = Jabatan::create([
            'nama_jabatan' => 'Guru',
            'kode_jabatan' => 'GR',
            'deskripsi' => 'Tenaga pendidik',
            'tunjangan_jabatan' => 500000,
        ]);

        $jabatanStaff = Jabatan::create([
            'nama_jabatan' => 'Staff Tata Usaha',
            'kode_jabatan' => 'SA',
            'deskripsi' => 'Tenaga administrasi',
            'tunjangan_jabatan' => 300000,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sma.com',
            'nip' => '123456789',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Create kepala sekolah
        $kepala = User::create([
            'name' => 'Dr. Ahmad Susanto, M.Pd',
            'email' => 'kepala@sma.com',
            'nip' => '196501011990031001',
            'phone' => '081234567891',
            'role' => 'kepala_sekolah',
            'password' => Hash::make('kepala123'),
        ]);

        Pegawai::create([
            'user_id' => $kepala->id,
            'jabatan_id' => $jabatanKepala->id,
            'nama_lengkap' => 'Dr. Ahmad Susanto, M.Pd',
            'tanggal_lahir' => '1965-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'pendidikan_terakhir' => 'S2 Pendidikan',
            'tanggal_masuk_kerja' => '1990-03-01',
            'status_kepegawaian' => 'PNS',
            'golongan' => 'IV/a'
        ]);

        // Create sample guru
        $guru1 = User::create([
            'name' => 'Siti Nurhaliza, S.Pd',
            'email' => 'guru1@sma.com',
            'nip' => '198005052005012001',
            'phone' => '081234567892',
            'role' => 'guru',
            'password' => Hash::make('guru123'),
        ]);

        Pegawai::create([
            'user_id' => $guru1->id,
            'jabatan_id' => $jabatanGuru->id,
            'nama_lengkap' => 'Siti Nurhaliza, S.Pd',
            'tanggal_lahir' => '1980-05-05',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Pendidikan No. 45, Jakarta',
            'pendidikan_terakhir' => 'S1 Matematika',
            'tanggal_masuk_kerja' => '2005-01-02',
            'status_kepegawaian' => 'PNS',
            'golongan' => 'III/c'
        ]);

        $guru2 = User::create([
            'name' => 'Budi Santoso, S.Pd',
            'email' => 'guru2@sma.com',
            'nip' => '198203152006011002',
            'phone' => '081234567893',
            'role' => 'guru',
            'password' => Hash::make('guru123'),
        ]);

        Pegawai::create([
            'user_id' => $guru2->id,
            'jabatan_id' => $jabatanGuru->id,
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'tanggal_lahir' => '1982-03-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Cendekia No. 67, Jakarta',
            'pendidikan_terakhir' => 'S1 Bahasa Indonesia',
            'tanggal_masuk_kerja' => '2006-01-01',
            'status_kepegawaian' => 'PNS',
            'golongan' => 'III/b'
        ]);

        // Create staff
        $staff = User::create([
            'name' => 'Dewi Lestari, S.Kom',
            'email' => 'staff@sma.com',
            'nip' => '198507202010012003',
            'phone' => '081234567894',
            'role' => 'staff',
            'password' => Hash::make('staff123'),
        ]);

        Pegawai::create([
            'user_id' => $staff->id,
            'jabatan_id' => $jabatanStaff->id,
            'nama_lengkap' => 'Dewi Lestari, S.Kom',
            'tanggal_lahir' => '1985-07-20',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Teknologi No. 89, Jakarta',
            'pendidikan_terakhir' => 'S1 Sistem Informasi',
            'tanggal_masuk_kerja' => '2010-01-02',
            'status_kepegawaian' => 'PPPK',
            'golongan' => 'III/a'
        ]);

        // Create periode penilaian
        $periode = PeriodePenilaian::create([
            'nama_periode' => 'Semester I Tahun 2024',
            'jenis_periode' => 'semester',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-06-30',
            'is_active' => true,
            'deskripsi' => 'Periode penilaian semester pertama tahun 2024'
        ]);

        // Create perilaku kerja indicators
        $perilakuKerja = [
            ['nama_perilaku' => 'Integritas', 'bobot' => 20],
            ['nama_perilaku' => 'Komitmen', 'bobot' => 20],
            ['nama_perilaku' => 'Disiplin', 'bobot' => 20],
            ['nama_perilaku' => 'Kerjasama', 'bobot' => 20],
            ['nama_perilaku' => 'Kepemimpinan', 'bobot' => 20],
        ];

        foreach ($perilakuKerja as $perilaku) {
            PerilakuKerja::create($perilaku);
        }
    }
}