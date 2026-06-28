# TaniPantau

## Deskripsi Singkat

TaniPantau adalah sistem informasi berbasis web untuk monitoring dan manajemen lahan pertanian secara digital. Dibangun sebagai proyek Ujian Akhir Semester 4 Pemrograman Web. Sistem ini memungkinkan petugas lapangan mencatat kunjungan ke lahan, mengunggah foto dokumentasi, serta admin/manajer memantau seluruh aktivitas melalui dashboard analitik.

## Anggota Kelompok

1. Asmaratungga Avenda Darma Putra (24102006): Backend developer dokumentasi
2. Sandy Agustiya Saputra (24102037): Frontend developer
3. Veronica Ananda Eka Cristya (24102021): software tester & sistem analis
4. Aini Nur Azizah (24102003): laporan

## Fitur Aplikasi

- **Autentikasi & Otorisasi**: Login admin/petugas dengan role-based access control
- **Manajemen Petani**: CRUD data petani dengan validasi input
- **Manajemen Lahan**: CRUD data lahan dengan peta interaktif (Leaflet.js)
- **Manajemen Kunjungan**: Petugas mencatat kunjungan, upload foto, admin melihat semua
- **Dashboard Analitik**: Statistik global, grafik, dan ringkasan data
- **API RESTful**: Backend API untuk integrasi frontend
- **Frontend Responsif**: Antarmuka mobile-friendly dengan Bootstrap 5
- **Validasi Input**: Validasi di sisi frontend dan backend
- **Penyimpanan Foto**: Organisasi file dengan UUID dan direktori tahun/bulan

## Teknologi

### Backend
- Laravel 13
- PHP 8.3+
- MySQL 8.0+
- Laravel Sanctum (API Authentication)
- Laravel Breeze (Blade Auth)
- PhpSpreadsheet (Export Excel)

### Frontend
- PHP Native
- HTML5/CSS3
- Bootstrap 5.3.3
- Bootstrap Icons 1.11.3
- Leaflet.js 1.9.4 (Mapping)
- Chart.js (Analytics)

## Cara Instalasi

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd tanipantau
   ```

2. **Setup Backend**
   ```bash
   cd tanipantau-backend
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Konfigurasi Database**
   Edit file `.env` pada direktori `tanipantau-backend`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tanipantau
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migration & Seeder**
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Jalankan Server Backend**
   ```bash
   php artisan serve
   # Backend berjalan di http://127.0.0.1:8000
   ```

6. **Setup Frontend**
   Buka terminal baru:
   ```bash
   cd tanipantau-frontend
   php -S localhost:8080
   # Frontend berjalan di http://127.0.0.1:8080
   ```

## Akun Demo

| Email | Password | Role |
|-------|----------|------|
| admin@tanipantau.com | password | Admin |
| manajer@tanipantau.com | password | Manajer |
| petugas1@tanipantau.com | password | Petugas |
| petugas2@tanipantau.com | password | Petugas |

## Link Deploy

Frontend: tanipantau.gt.tc
Backend/Admin: user-tanipantau.gt.tc

## Endpoint API

**Base URL**: `http://127.0.0.1:8000/api`

| Method | Endpoint | Keterangan | Auth |
|--------|----------|------------|------|
| POST | /login | Login dan dapatkan token | No |
| POST | /logout | Logout dan hapus token | Yes |
| GET | /me | Info user yang login | Yes |
| GET | /dashboard | Statistik dashboard | Yes |
| GET | /petani | Daftar semua petani | Yes |
| POST | /petani | Tambah petani | Admin/Manajer |
| GET | /petani/{id} | Detail petani | Yes |
| PUT | /petani/{id} | Update petani | Admin/Manajer |
| DELETE | /petani/{id} | Hapus petani | Admin |
| GET | /lahan | Daftar semua lahan | Yes |
| POST | /lahan | Tambah lahan | Admin/Manajer |
| GET | /lahan/{id} | Detail lahan | Yes |
| PUT | /lahan/{id} | Update lahan | Admin/Manajer |
| DELETE | /lahan/{id} | Hapus lahan | Admin |
| GET | /kunjungan | Daftar semua kunjungan | Yes |
| POST | /kunjungan | Tambah kunjungan | Petugas |
| GET | /kunjungan/{id} | Detail kunjungan | Yes |
| PUT | /kunjungan/{id} | Update kunjungan | Yes |
| DELETE | /kunjungan/{id} | Hapus kunjungan | Admin |
| GET | /kunjungan-saya | Riwayat kunjungan petugas | Petugas |
| POST | /{lahan}/kunjungan | Tambah kunjungan ke lahan | Petugas |
| GET | /kabupaten | Daftar kabupaten | Yes |

## AI Usage Log

### AI Tools Used

Selama proses pengembangan **TaniPantau**, beberapa AI digunakan sebagai **development assistant** untuk membantu proses analisis, debugging, dokumentasi, serta peningkatan kualitas kode. Seluruh keputusan implementasi, integrasi, pengujian, dan validasi tetap dilakukan oleh developer.

| AI Tool                  | Penggunaan                                                                                                                                   |
| ------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------- |
| ChatGPT (OpenAI GPT-5.5) | Arsitektur sistem, debugging Laravel, refactor kode, optimasi upload file, middleware, deployment, dokumentasi, REST API, serta review kode. |
| Claude Code              | Membantu analisis kode, refactoring, dan validasi implementasi Laravel.                                                                      |
| OpenCode                 | Membantu proses coding dan eksplorasi implementasi fitur pada Visual Studio Code.                                                            |
| Blackbox AI              | Referensi implementasi kode, eksplorasi solusi, dan pencarian alternatif penyelesaian masalah.                                               |
| Antigravity AI           | Membantu pembuatan prompt desain antarmuka (UI/UX) dan aset visual.                                                                          |

### AI-Assisted Development

AI digunakan untuk membantu beberapa aktivitas berikut:

* Merancang struktur project Laravel.
* Refactor sistem upload foto menjadi lebih konsisten dan mudah dipelihara.
* Optimasi Service Layer dan Controller.
* Debugging error Laravel (419, 404, Session, Middleware, Storage).
* Optimasi deployment pada shared hosting (InfinityFree).
* Penyusunan REST API dan Resource.
* Penyusunan dokumentasi teknis.
* Review kode berdasarkan best practice Laravel.

### Human Responsibility

Walaupun AI digunakan selama proses pengembangan, seluruh implementasi akhir dilakukan melalui proses:

* Analisis kebutuhan.
* Modifikasi hasil AI agar sesuai dengan kebutuhan aplikasi.
* Pengujian manual setiap fitur.
* Debugging dan validasi hasil implementasi.
* Pengambilan keputusan teknis oleh developer.

AI berperan sebagai **alat bantu pengembangan (development assistant)**, sedangkan seluruh keputusan akhir, integrasi sistem, serta pengujian tetap menjadi tanggung jawab developer.

### Disclaimer

Dokumen ini disediakan sebagai bentuk transparansi proses pengembangan perangkat lunak. Penggunaan AI bertujuan meningkatkan produktivitas, kualitas dokumentasi, serta efisiensi debugging tanpa menggantikan proses rekayasa perangkat lunak yang dilakukan oleh developer.


## Pembagian Tugas

Asmaratungga Avenda Darma Putra : Backend developer dokumentasi
Sandy Agustiya Saputra : Frontend developer
Veronica Ananda Eka Cristya : software tester & sistem analis
Aini Nur Azizah : laporan
