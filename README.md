# Sistem Informasi LPPM STAI Ali bin Abi Thalib Surabaya

## 🎯 Deskripsi Proyek

Platform digital terintegrasi untuk mengelola seluruh siklus kegiatan penelitian dan pengabdian kepada masyarakat menggunakan Laravel dan FilamentPHP. Sistem ini bertujuan untuk menggantikan proses manual yang ada dengan fokus pada efisiensi alur kerja, manajemen dokumen, dan pelaporan internal.

## 🚀 Quick Start

### **Mulai Testing (5 Menit)**
```bash
# Start server
php artisan serve

# Akses admin panel
http://localhost:8000/admin

# Login default users:
# Admin: admin@stai-ali.ac.id / password123
# Dosen: dosen@stai-ali.ac.id / password123
# Reviewer: reviewer@stai-ali.ac.id / password123
# Pimpinan: pimpinan@stai-ali.ac.id / password123
```

📖 **Panduan Lengkap:**
- 📋 [**FLOW_SISTEM_LPPM.md**](./FLOW_SISTEM_LPPM.md) - Flow lengkap sistem
- 🧪 [**TESTING_GUIDE.md**](./TESTING_GUIDE.md) - Panduan testing A-Z
- ⚡ [**QUICK_START.md**](./QUICK_START.md) - Quick start guide

## 🔄 Flow Sistem Overview

### **Alur Utama (8 Tahap)**
```
1. SETUP → 2. PROPOSAL → 3. REVIEW → 4. APPROVE → 5. KONTRAK → 6. MONITORING → 7. SEMINAR → 8. PUBLIKASI
```

### **Roles & Responsibilities**
- 👤 **Admin LPPM:** Master data, approval, konfirmasi dana/monitoring
- 📝 **Dosen:** Pengajuan proposal, kelola tim, upload dokumen/laporan
- 👨‍🏫 **Reviewer:** Review & nilai proposal, beri rekomendasi
- 🏛️ **Pimpinan:** Dashboard monitoring, laporan capaian

### **Status Workflow**
- **Proposal:** `draft → submitted → under_review → approved/rejected`
- **Kontrak:** `draft → signed → active → completed`
- **Pencairan:** `stage_1 (50%) → stage_2 (25%) → stage_3 (25%)`
- **Monitoring:** `monitoring_1 → monitoring_2 → laporan_akhir`

## ✨ Fitur Utama

### 1. Autentikasi dan Manajemen Pengguna
- Sistem login multi-role (Dosen, Admin LPPM, Reviewer, Pimpinan)
- Manajemen profil dengan NIDN, jabatan fungsional, dan bidang keilmuan
- Role-based access control menggunakan Spatie Laravel Permission

### 2. Manajemen Skema Penelitian & Pengabdian
- CRUD skema penelitian/pengabdian dinamis
- Konfigurasi dana hibah default dan periode pendaftaran
- Penentuan jenis luaran yang diharapkan (Jurnal Sinta, Buku ISBN, HKI, dll)

### 3. Pengajuan Proposal
- Form pengajuan online dengan validasi lengkap
- Manajemen tim maksimal 4 orang (1 ketua, 3 anggota)
- Upload dokumen proposal lengkap
- Fitur draft dan submit proposal
- Tracking status proposal real-time

### 4. Review dan Penilaian
- Assignment reviewer ke proposal
- Form penilaian online dengan kriteria substansi
- Sistem skor dan rekomendasi
- Upload dokumen revisi

### 5. Monitoring dan Evaluasi
- Konfirmasi status pencairan dana bertahap (3 tahap)
- Monitoring kemajuan penelitian (2 tahap)
- Upload laporan akhir
- Visualisasi timeline progress

### 6. Luaran dan Publikasi
- Dokumentasi dan tracking luaran penelitian
- Verifikasi luaran oleh Admin LPPM
- Pengajuan bantuan biaya publikasi
- Dashboard monitoring target 40 publikasi tahunan

### 7. Seminar Hasil Penelitian
- Penjadwalan seminar internal dan publik
- Pendaftaran peserta
- Upload materi presentasi
- Pencatatan feedback seminar

### 8. Pelaporan & Statistik
- Dashboard statistik komprehensif
- Export data untuk pelaporan internal
- Audit trail untuk aktivitas penting

## Tech Stack

- **Backend**: Laravel 12.x
- **Admin Panel**: FilamentPHP 3.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum + Spatie Permission
- **File Management**: Spatie Media Library
- **Export**: Maatwebsite Excel

## Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone <repository-url>
cd lppm-stai-ali
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Configuration**
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lppm_stai_ali
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Database Migration & Seeding**
```bash
php artisan migrate
php artisan db:seed
```

6. **Asset Compilation**
```bash
npm run build
```

7. **Storage Link**
```bash
php artisan storage:link
```

8. **Run Application**
```bash
php artisan serve
```

## Default Users

Setelah menjalankan seeder, tersedia user default:

| Role | Email | Password | Keterangan |
|------|-------|----------|------------|
| Admin LPPM | admin@lppm-stai-ali.ac.id | password123 | Akses penuh sistem |
| Dosen | ahmad.fulan@stai-ali.ac.id | password123 | Sample dosen peneliti |
| Reviewer | reviewer@example.com | password123 | Sample reviewer eksternal |
| Pimpinan | pimpinan@stai-ali.ac.id | password123 | Sample pimpinan fakultas |

## Panel Admin

Akses panel admin di: `http://localhost:8000/admin`

## Struktur Database

### Tabel Utama
- `users` - Data pengguna dengan profil lengkap
- `research_schemes` - Skema penelitian/pengabdian
- `proposals` - Data proposal dengan tracking status
- `proposal_team_members` - Anggota tim proposal
- `reviews` - Data review dan penilaian
- `research_outputs` - Luaran penelitian
- `seminars` - Data seminar hasil penelitian
- `publication_funding_requests` - Pengajuan bantuan publikasi

### Roles & Permissions
- **Dosen**: Pengajuan proposal, upload dokumen, manajemen tim
- **Admin LPPM**: Akses penuh, manajemen skema, konfirmasi status
- **Reviewer**: Review proposal, penilaian substansi
- **Pimpinan**: Dashboard monitoring, laporan capaian

## Kontribusi

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## License

[MIT License](LICENSE)

## Support

Untuk bantuan teknis, hubungi tim IT STAI Ali bin Abi Thalib Surabaya.

---

**© 2025 LPPM STAI Ali bin Abi Thalib Surabaya**

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
