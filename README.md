# Sistem Penilaian Kinerja Pegawai (SKP) Sekolah

## Identitas
- **Nama:** Abdurrahman Marzuki
- **NIM:** 2308107010020

## Demo Aplikasi
Lihat demo aplikasi ini di: [Link Video Demo]

Gunakan akun berikut untuk mencoba:

**Admin**
- nip: 123456789
- Password: admin123

**Kepala Sekolah**
- nip: 196501011990031001
- Password: kepala123

**Pegawai**
- nip: 198203152006011002
- Password: guru123

## Deskripsi Aplikasi
Sistem Penilaian Kinerja Pegawai (SKP) adalah aplikasi berbasis web yang dibangun menggunakan framework Laravel untuk membantu proses penilaian kinerja pegawai di lingkungan sekolah. Aplikasi ini memungkinkan pengelolaan sasaran kerja, realisasi, dan penilaian kinerja pegawai secara sistematis.

## Fitur Utama
1. **Manajemen Pengguna**
   - Multi-role user (Admin, Kepala Sekolah, Pegawai)
   - Autentikasi dan autorisasi berbasis role

2. **Manajemen Periode Penilaian**
   - Pengaturan periode aktif
   - Monitoring status periode

3. **Sasaran Kerja**
   - Input sasaran kerja oleh pegawai
   - Persetujuan sasaran oleh kepala sekolah
   - Tracking status sasaran

4. **Realisasi Kerja**
   - Input realisasi kerja
   - Monitoring pencapaian target

5. **Penilaian Kinerja**
   - Penilaian SKP oleh kepala sekolah
   - Perhitungan nilai akhir otomatis
   - Kategorisasi nilai

## Prasyarat
Sebelum menginstall sistem ini, pastikan komputer Anda memiliki:
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Git

## Cara Instalasi

1. **Clone repository ini:**
   ```bash
   git clone https://github.com/username/projekSKP2.git
   cd projekSKP2
   ```

2. **Install dependencies PHP:**
   ```bash
   composer install
   ```

3. **Install dependencies JavaScript:**
   ```bash
   npm install
   npm run build
   ```

4. **Salin file .env.example menjadi .env:**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Konfigurasi database di file .env:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=skp_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Jalankan migrasi dan seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```

## Menjalankan Aplikasi

1. **Jalankan server Laravel:**
   ```bash
   php artisan serve
   ```

2. **Di terminal terpisah, jalankan Vite:**
   ```bash
   npm run dev
   ```

3. **Akses aplikasi di browser:**
   ```
   http://localhost:8000
   ```

## Struktur Sistem
```
/app
    /Http
        /Controllers - Controller aplikasi
        /Middleware - Middleware aplikasi
    /Models - Model aplikasi
/database
    /migrations - File migrasi database
    /seeders - File seeder database
/resources
    /views - File blade template
    /js - File JavaScript
    /css - File CSS
/routes
    web.php - File routing web
/public - File publik
```

## Kontribusi
Jika ingin berkontribusi:
1. Fork repository
2. Buat branch baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request
