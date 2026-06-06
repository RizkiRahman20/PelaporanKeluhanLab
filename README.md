<div align="center">

# Lab Reporting System and Maintenance

**Sistem Pelaporan Keluhan dan Maintenance Laboratorium**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.3-F59E0B?style=flat-square)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=flat-square)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![DomPDF](https://img.shields.io/badge/DomPDF-3.1-4A90D9?style=flat-square)](https://github.com/barryvdh/laravel-dompdf)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

</div>

---

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Akses Aplikasi](#akses-aplikasi)
- [Akun Seeder](#akun-seeder)
- [Alur Sistem](#alur-sistem)
- [Hak Akses Role](#hak-akses-role)
- [Struktur Proyek](#struktur-proyek)
- [Catatan Pengembangan](#catatan-pengembangan)

---

## Tentang Proyek

**LRS (Lab Reporting System)** adalah aplikasi web berbasis Laravel untuk mengelola pelaporan keluhan laboratorium, validasi laporan oleh supervisor, proses perbaikan oleh admin lab, monitoring status perbaikan, dan ekspor riwayat perbaikan ke PDF.

Proyek ini memiliki dua area utama:

- **Portal publik mahasiswa** untuk membuat laporan keluhan dan memantau status laporan.
- **Dashboard internal Filament** untuk SPV, admin lab, dan pengelola data master.

---

## Fitur Utama

- Form laporan keluhan publik dengan validasi NIM, nama, fakultas, kategori kerusakan, laboratorium, catatan, dan upload foto bukti.
- Nomor laporan otomatis dengan format `LPR-YYYYMMDD-XXXX`.
- Halaman status publik untuk mencari laporan berdasarkan nomor laporan serta melihat daftar laporan dengan filter lab, kategori, dan status approval.
- Validasi laporan oleh SPV: laporan dapat disetujui atau ditolak dengan alasan.
- Pembuatan data perbaikan otomatis setelah laporan disetujui.
- Manajemen perbaikan dengan status `antrean`, `dikerjakan`, `menunggu_sparepart`, dan `selesai`.
- Kanban Board berbasis Livewire untuk admin lab dalam memindahkan status pekerjaan.
- Upload foto bukti perbaikan dan catatan penyelesaian.
- Validasi hasil perbaikan oleh SPV: `menunggu`, `divalidasi`, atau `dikembalikan`.
- Riwayat perubahan status perbaikan.
- Cetak PDF riwayat perbaikan berdasarkan filter lab dan rentang tanggal.
- Manajemen data master laboratorium, pengguna, dan penugasan user ke lab.
- Dashboard monitoring dengan ringkasan dan chart laporan/perbaikan.

---

## Teknologi

| Teknologi | Versi Proyek | Keterangan |
| --- | --- | --- |
| Laravel Framework | `^12.0` / locked `v12.61.0` | Framework utama aplikasi |
| Filament | `^3.3` / locked `v3.3.52` | Panel admin dan resource CRUD |
| Livewire | locked `v3.8.0` | Komponen interaktif Kanban |
| PHP | `^8.2` | Bahasa backend |
| DomPDF | `^3.1` / locked `v3.1.2` | Generate laporan PDF |
| Vite | `^7.0.7` | Build asset frontend |
| Tailwind CSS | `^4.3.0` | Styling frontend |

---

## Persyaratan Sistem

Pastikan perangkat sudah memiliki:

- PHP `8.2` atau lebih baru.
- Composer.
- Node.js `18` atau lebih baru dan NPM.
- Database MySQL `8.0+` atau MariaDB. Proyek ini menggunakan query sorting lab yang cocok untuk MySQL/MariaDB.
- Ekstensi PHP umum Laravel: `BCMath`, `Ctype`, `Fileinfo`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`.

---

## Instalasi

### 1. Clone repository

```bash
git clone <url-repository>
cd pelaporankeluhanlab
```

### 2. Install dependency backend

```bash
composer install
```

### 3. Siapkan environment

Jika tersedia file `.env.example`, salin menjadi `.env`:

```bash
cp .env.example .env
```

Jika file `.env.example` tidak tersedia, buat file `.env` dan sesuaikan konfigurasi dasar berikut:

```env
APP_NAME="Lab Reporting System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pelaporan_keluhan_lab
DB_USERNAME=root
DB_PASSWORD=
```

Lalu generate application key:

```bash
php artisan key:generate
```

### 4. Migrasi dan seed database

```bash
php artisan migrate --seed
```

Seeder akan membuat:

- 11 data laboratorium aktif.
- User SPV Kedisiplinan dan SPV Jaringan.
- 11 user Admin Lab.
- 11 user Asisten Lab.
- Penugasan awal user ke masing-masing laboratorium.

### 5. Aktifkan storage publik

```bash
php artisan storage:link
```

Langkah ini diperlukan agar foto keluhan dan bukti perbaikan yang disimpan di disk `public` dapat diakses dari browser.

### 6. Install dan build asset frontend

```bash
npm install
npm run build
```

Untuk development, gunakan:

```bash
npm run dev
```

### 7. Jalankan aplikasi

```bash
php artisan serve
```

Aplikasi berjalan di `http://localhost:8000`.

---

## Akses Aplikasi

| Halaman | URL | Keterangan |
| --- | --- | --- |
| Beranda mahasiswa | `/` | Landing page publik mahasiswa |
| Form laporan | `/buat-laporan` | Form pengajuan keluhan |
| Status laporan | `/status` | Cek dan filter status laporan |
| Status spesifik | `/status/{no_laporan}` | Redirect ke detail status berdasarkan nomor laporan |
| Dashboard internal | `/dashboard` | Panel Filament untuk user aktif |
| Cetak PDF riwayat | `/pdf/riwayat` | Endpoint PDF, wajib login |

Contoh nomor laporan:

```text
LPR-20260605-0001
```

---

## Akun Seeder

Semua akun hasil seeder menggunakan password:

```text
password
```

| Role | Email Contoh | Keterangan |
| --- | --- | --- |
| SPV Kedisiplinan | `spv.kedisiplinan@lab.test` | Akses validasi laporan dan master data |
| SPV Jaringan | `spv.jaringan@lab.test` | Akses laporan/perbaikan sesuai penugasan lab |
| Admin Lab | `admin.lab1@lab.test` sampai `admin.lab11@lab.test` | Akses perbaikan dan Kanban sesuai lab |
| Asisten Lab | `asisten.lab1@lab.test` sampai `asisten.lab11@lab.test` | Data awal asisten per lab |

---

## Alur Sistem

1. Mahasiswa membuka `/buat-laporan` dan mengisi form keluhan.
2. Sistem membuat nomor laporan otomatis dan menyimpan status approval awal `menunggu`.
3. SPV membuka dashboard `/dashboard` dan memvalidasi laporan.
4. Jika laporan ditolak, SPV mengisi alasan penolakan.
5. Jika laporan disetujui, sistem membuat data perbaikan dengan status awal `antrean`.
6. Admin Lab memproses perbaikan melalui menu Kelola Perbaikan atau Kanban Board.
7. Saat pekerjaan selesai, Admin Lab mengunggah foto bukti dan catatan perbaikan.
8. SPV melakukan validasi akhir hasil perbaikan.
9. Riwayat perubahan dapat dilihat dan diekspor ke PDF.

---

## Hak Akses Role

| Role | Hak Akses Utama |
| --- | --- |
| `spv_kedisiplinan` | Validasi laporan, monitoring, kelola lab, kelola user, kelola penugasan, validasi perbaikan |
| `spv_jaringan` dan role `spv_*` lain | Validasi laporan/perbaikan sesuai lab yang ditugaskan |
| `admin_lab` | Kelola perbaikan, Kanban Board, riwayat perbaikan sesuai lab yang ditugaskan |
| `asisten_lab` | Disediakan sebagai data user dan penugasan lab |
| `calon_asisten` | Tersedia sebagai opsi role dengan akses terbatas sesuai implementasi aplikasi |

User hanya dapat mengakses panel jika `status_aktif` bernilai `aktif`.

---

## Struktur Proyek

```text
pelaporankeluhanlab/
+-- app/
|   +-- Filament/
|   |   +-- Pages/              # Dashboard, Kanban Board, Cetak PDF, login custom
|   |   +-- Resources/          # Lab, User, Penugasan, Laporan, Perbaikan, Riwayat
|   |   +-- Widgets/            # Chart monitoring laporan dan perbaikan
|   +-- Http/Controllers/       # Controller publik mahasiswa dan PDF
|   +-- Livewire/               # Komponen Kanban perbaikan
|   +-- Models/                 # Model Eloquent
|   +-- Providers/Filament/     # Konfigurasi panel Filament
+-- database/
|   +-- migrations/             # Skema users, labs, laporan, perbaikan, riwayat
|   +-- seeders/                # Seeder lab, user, dan penugasan
+-- resources/
|   +-- views/mahasiswa/        # Portal publik mahasiswa
|   +-- views/filament/pages/   # View custom panel Filament
|   +-- views/livewire/         # View Kanban Livewire
|   +-- views/pdf/              # Template PDF riwayat perbaikan
+-- routes/
|   +-- web.php                 # Route publik, dashboard, dan PDF
+-- public/
|   +-- images/logoict.jpeg     # Logo aplikasi
+-- config/dompdf.php           # Konfigurasi DomPDF
```

---

## Catatan Pengembangan

- Panel Filament dikonfigurasi pada path `/dashboard` di `app/Providers/Filament/AdminPanelProvider.php`.
- Upload foto keluhan disimpan ke `storage/app/public/laporan`.
- Upload bukti perbaikan disimpan ke `storage/app/public/perbaikan`.
- Template PDF berada di `resources/views/pdf/riwayat-perbaikan.blade.php`.
- DomPDF menerima data langsung melalui array pada `Pdf::loadView(...)`.
- Untuk menjalankan test Laravel:

```bash
php artisan test
```

Atau gunakan script Composer:

```bash
composer test
```

---

<div align="center">

Dibuat untuk sistem pelaporan keluhan dan maintenance laboratorium.

**Universitas Budi Luhur**

</div>
