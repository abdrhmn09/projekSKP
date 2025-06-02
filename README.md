## Sistem Pengelolaan SKP (Sasaran Kinerja Pegawai) - projekSKP

Sistem berbasis web untuk pengelolaan Sasaran Kinerja Pegawai (SKP) secara komprehensif, mulai dari perencanaan, pelaksanaan, penilaian, hingga pelaporan kinerja pegawai.

## Demo Aplikasi

Lihat demo aplikasi ini di: https://youtu.be/kcsjyhvxpTU

Gunakan akun berikut untuk mencoba:
Admin: `123456789` / `admin123`
kepala sekolah : `196501011990031001`/ `kepala123`
Pegawai: `198203152006011002` / `guru123`

## Fitur Utama

*   Manajemen Data Pegawai dan Atasan penilai.
*   Penyusunan Rencana SKP (Sasaran Kinerja Pegawai) tahunan dan bulanan.
*   Pengisian Realisasi Kegiatan Tugas Jabatan dan Target Kinerja.
*   Proses Penilaian SKP oleh Atasan Penilai.
*   Validasi dan Persetujuan SKP oleh Pejabat yang Berwenang.
*   Monitoring dan Evaluasi Kinerja Pegawai.
*   Pencetakan Dokumen SKP (Formulir Rencana SKP, Pengukuran, Penilaian, dll.).
*   Manajemen Pengguna dengan berbagai level akses (Admin, Pegawai, kepala sekolah).
*   Notifikasi terkait progres dan tenggat waktu SKP.
*   Laporan dan rekapitulasi kinerja individu maupun unit kerja.

## Prasyarat

Sebelum menginstall sistem ini, pastikan komputer Anda telah terinstall:

*   PHP >= 8.1 (Sesuaikan dengan versi PHP yang didukung proyek Anda)
*   Composer (Dependency Manager untuk PHP)
*   Node.js & NPM (Untuk manajemen package JavaScript dan kompilasi aset)
*   MySQL/MariaDB (atau database lain yang Anda konfigurasikan)
*   Git (Untuk version control)

## Cara Instalasi

1.  **Clone repository ini dan masuk ke direktori:**
    ```bash
    git clone https://github.com/abdrhmn09/projekSKP.git
    cd projekSKP
    ```

2.  **Install dependencies PHP melalui Composer:**
    ```bash
    composer install
    ```

3.  **Install dependencies JavaScript melalui NPM:**
    ```bash
    npm install
    ```

4.  **Salin file `.env.example` menjadi `.env`:**
    Pada Windows (PowerShell/CMD):
    ```bash
    copy .env.example .env
    ```
    Pada Linux/macOS:
    ```bash
    cp .env.example .env
    ```

5.  **Generate application key Laravel:**
    ```bash
    php artisan key:generate
    ```

6.  **Konfigurasi database di file `.env`:**
    Buka file `.env` dan sesuaikan variabel berikut dengan konfigurasi database Anda. Pastikan Anda sudah membuat database kosong untuk proyek ini.
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_projekskp2 # Ganti dengan nama database Anda
    DB_USERNAME=root          # Ganti dengan username database Anda
    DB_PASSWORD=              # Ganti dengan password database Anda (kosongkan jika tidak ada)
    ```

7.  **Jalankan migrasi database dan seeder:**
    Untuk membuat struktur tabel dan mengisi data awal.
    ```bash
    php artisan migrate:fresh --seed
    ```
    Jika Anda hanya ingin menjalankan migrasi tanpa menghapus data yang sudah ada (jika tabel sudah ada dan Anda hanya menambahkan migrasi baru):
    ```bash
    php artisan migrate --seed
    ```

## Menjalankan Aplikasi

1.  **Jalankan server pengembangan Laravel:**
    ```bash
    php artisan serve
    ```
    Biasanya aplikasi akan berjalan di `http://localhost:8000`.

2.  **Kompilasi aset frontend (jika menggunakan Vite/Mix):**
    Buka terminal baru, masuk ke direktori proyek, dan jalankan:
    ```bash
    npm run dev
    ```
    Ini akan mengkompilasi aset CSS dan JavaScript Anda dan menjalankannya dalam mode watch.

3.  **Akses aplikasi di browser:**
    Buka browser dan kunjungi alamat yang ditampilkan oleh `php artisan serve` (biasanya `http://localhost:8000`).


## Struktur Sistem

*   `/app` - Core aplikasi
*   `/database` - Migrasi dan seeder
*   `/resources` - Views dan assets
*   `/routes` - File routing
*   `/public` - File publik
*   `/tests` - Unit dan feature tests

## Kontribusi

Jika Anda ingin berkontribusi pada pengembangan proyek ini, silakan ikuti langkah-langkah berikut:

1.  **Fork** repository ini.
2.  Buat **branch baru** untuk fitur atau perbaikan Anda (`git checkout -b fitur/NamaFiturAnda` atau `git checkout -b fix/DeskripsiPerbaikan`).
3.  Lakukan **perubahan** dan **commit** pekerjaan Anda (`git commit -am 'Menambahkan fitur X'`).
4.  **Push** ke branch Anda (`git push origin fitur/NamaFiturAnda`).
5.  Buat **Pull Request** baru ke branch `main` (atau `master`) dari repository asli.

---
