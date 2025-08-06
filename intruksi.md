{
  "project_name": "Sistem Informasi LPPM STAI Ali bin Abi Thalib Surabaya",
  "objective": "Membangun platform digital terintegrasi menggunakan Laravel dan Filament untuk mengelola seluruh siklus kegiatan penelitian dan pengabdian kepada masyarakat, dengan fokus pada efisiensi alur kerja, manajemen dokumen, dan pelaporan internal. Sistem ini bertujuan untuk menggantikan proses manual yang ada.",
  "target_users": [
    {
      "role": "Dosen Peneliti",
      "permissions": [
        "Mengajukan proposal penelitian/pengabdian",
        "Melihat status proposal",
        "Mengunggah dokumen proposal lengkap (revisi jika diperlukan)",
        "Mengelola anggota tim proposal",
        "Mengunggah dokumen laporan akhir lengkap",
        "Mengunggah bukti luaran penelitian/pengabdian",
        "Mengajukan bantuan biaya publikasi",
        "Mengunggah materi presentasi seminar"
      ]
    },
    {
      "role": "Admin LPPM",
      "permissions": [
        "Akses penuh ke semua modul",
        "Manajemen skema penelitian/pengabdian (CRUD)",
        "Manajemen periode pendaftaran skema",
        "Verifikasi kelengkapan administratif proposal",
        "Menugaskan reviewer ke proposal",
        "Mengkonfirmasi status pencairan dana (Tahap 1, 2, 3)",
        "Mengkonfirmasi status monitoring (Pertama, Kedua)",
        "Verifikasi luaran penelitian/pengabdian",
        "Menjadwalkan seminar hasil penelitian",
        "Melihat dashboard statistik dan laporan",
        "Export data untuk pelaporan internal/eksternal"
      ]
    },
    {
      "role": "Reviewer Eksternal",
      "permissions": [
        "Mengakses dan mengunduh dokumen proposal yang ditugaskan",
        "Mengisi form penilaian substansi proposal",
        "Memberikan rekomendasi (disetujui tanpa revisi, disetujui dengan revisi, ditolak)"
      ]
    },
    {
      "role": "Pimpinan Fakultas",
      "permissions": [
        "Melihat dashboard progres penelitian/pengabdian",
        "Melihat laporan capaian",
        "Menyetujui alur kerja tertentu (jika ada)"
      ]
    }
  ],
  "core_features": [
    {
      "module_name": "Autentikasi dan Manajemen Pengguna",
      "description": "Mengelola akses pengguna berdasarkan peran.",
      "details": [
        "Sistem login untuk berbagai peran.",
        "Manajemen peran (roles) dan izin (permissions) menggunakan Filament's built-in features atau Spatie's Laravel Permission.",
        "Profil pengguna dengan informasi dasar, NIDN, jabatan fungsional, dan bidang keilmuan (untuk dosen)."
      ]
    },
    {
      "module_name": "Manajemen Skema Penelitian & Pengabdian",
      "description": "Admin dapat mengelola jenis-jenis skema penelitian/pengabdian secara dinamis.",
      "details": [
        "CRUD (Create, Read, Update, Delete) skema oleh Admin LPPM.",
        "Setiap skema memiliki: Nama, Deskripsi, Jumlah Dana Hibah Default, Periode Pendaftaran (Tanggal Mulai & Selesai), Jenis Luaran yang Diharapkan (pilihan: Jurnal Sinta 1-6, Buku ISBN, HKI, Produk Inovasi)."
      ]
    },
    {
      "module_name": "Pengajuan Proposal",
      "description": "Memfasilitasi dosen mengajukan proposal dengan fokus pada metadata dan unggah dokumen lengkap.",
      "details": [
        "Form pengajuan proposal online dengan field:",
        "  - Judul Penelitian/Pengabdian",
        "  - Pilihan Skema (hanya skema yang aktif pendaftarannya)",
        "  - Abstrak (maksimal 250 kata, 3-6 kata kunci)",
        "  - Anggaran Dana (otomatis terisi dari skema, bisa di-override jika diizinkan)",
        "  - Rencana Publikasi (teks singkat/jurnal tujuan)",
        "Manajemen Tim:",
        "  - Penambahan anggota tim (maksimal 4 orang: 1 ketua, 3 anggota).",
        "  - Validasi ketua tim: harus dosen tetap, jabatan fungsional minimal, bidang keilmuan sesuai judul proposal.",
        "  - Mahasiswa dapat menjadi anggota tim.",
        "Unggah Dokumen Proposal Lengkap (PDF/Word) sebagai satu file utama.",
        "Fitur 'Simpan Draft' dan 'Submit Proposal'.",
        "Dosen dapat melihat status proposal (diajukan, direview, disetujui, ditolak)."
      ]
    },
    {
      "module_name": "Review dan Penilaian Proposal",
      "description": "Mendukung proses review administratif dan substansi.",
      "details": [
        "Admin LPPM dapat menugaskan reviewer ke proposal.",
        "Reviewer dapat mengunduh dokumen proposal lengkap.",
        "Form penilaian online untuk reviewer dengan kriteria substansi (relevansi, metodologi, orisinalitas, kelayakan).",
        "Sistem skor dan rekomendasi (disetujui tanpa revisi, disetujui dengan revisi, ditolak).",
        "Dosen dapat mengunggah dokumen proposal revisi."
      ]
    },
    {
      "module_name": "Kontrak dan Pencairan Dana",
      "description": "Mengelola kontrak dan mengkonfirmasi status pencairan dana.",
      "details": [
        "Pembuatan dan penandatanganan kontrak digital.",
        "Tracking status kontrak.",
        "Admin LPPM dapat mengkonfirmasi status pencairan dana bertahap (Tahap 1: 50% setelah SK, Tahap 2: 25% setelah laporan hasil, Tahap 3: 25% setelah luaran).",
        "Sistem mencatat tanggal konfirmasi pencairan dan siapa yang mengkonfirmasi.",
        "Peneliti TIDAK mengunggah laporan penggunaan dana di sistem.",
        "Pencatatan sanksi pengembalian dana jika penelitian tidak selesai."
      ]
    },
    {
      "module_name": "Monitoring dan Evaluasi Pelaksanaan",
      "description": "Memantau kemajuan penelitian melalui konfirmasi status dan unggah laporan akhir.",
      "details": [
        "Admin LPPM dapat mengkonfirmasi 'Monitoring Pertama' dan 'Monitoring Kedua'.",
        "Sistem mencatat tanggal konfirmasi monitoring dan siapa yang mengkonfirmasi.",
        "Peneliti TIDAK mengunggah laporan kemajuan di sistem (penilaian kemajuan dilakukan secara offline oleh LPPM).",
        "Peneliti mengunggah Dokumen Laporan Akhir Lengkap (PDF/Word) setelah monitoring kedua dan seminar internal.",
        "Visualisasi timeline progres penelitian.",
        "Notifikasi untuk peneliti mengenai deadline unggah laporan akhir."
      ]
    },
    {
      "module_name": "Luaran dan Publikasi",
      "description": "Mendokumentasikan dan melacak luaran hasil penelitian/pengabdian.",
      "details": [
        "Peneliti mengunggah bukti luaran (file artikel, scan ISBN buku, sertifikat HKI, foto/video produk inovasi).",
        "Tracking status publikasi jurnal (Sinta 1-6, Scopus).",
        "Pengajuan dan persetujuan bantuan biaya publikasi.",
        "Verifikasi luaran oleh Admin LPPM sesuai jenis luaran yang diharapkan dari skema.",
        "Dashboard untuk memantau capaian target 40 publikasi tahunan."
      ]
    },
    {
      "module_name": "Seminar Hasil Penelitian",
      "description": "Mengelola jadwal dan pelaksanaan seminar.",
      "details": [
        "Admin LPPM menjadwalkan seminar internal dan publik.",
        "Fitur pendaftaran peserta untuk seminar publik.",
        "Peneliti mengunggah materi presentasi seminar.",
        "Pencatatan hasil dan masukan dari seminar."
      ]
    },
    {
      "module_name": "Pelaporan & Statistik",
      "description": "Menyediakan data akurat untuk pelaporan internal.",
      "details": [
        "Dashboard statistik: jumlah proposal, penelitian/pengabdian aktif, publikasi per jenis, capaian target publikasi.",
        "Fitur export data (CSV, Excel, PDF) untuk kebutuhan internal atau pelaporan manual ke pihak eksternal (termasuk SPMI Kemendikbud jika diperlukan).",
        "Audit trail untuk semua aktivitas penting dalam sistem."
      ]
    }
  ],
  "non_functional_requirements": {
    "performance": "Response time rata-rata < 3 detik; Mendukung 100 pengguna bersamaan; Uptime minimum 99.5%.",
    "security": "SSL/TLS encryption; Role-based access control (RBAC); Audit trail; Password policy compliance.",
    "usability": "Antarmuka responsif (desktop, tablet, mobile); Bahasa Indonesia sebagai bahasa utama; Navigasi intuitif (maksimal 3 klik untuk fungsi utama); Sistem bantuan dan tooltips.",
    "scalability": "Arsitektur yang dapat diskalakan untuk pertumbuhan data dan pengguna di masa depan."
  },
  "technical_stack": {
    "backend": "Laravel (PHP)",
    "admin_panel": "FilamentPHP",
    "database": "MySQL/PostgreSQL",
    "frontend": "Blade, Livewire (built-in with Filament)"
  },
  "out_of_scope_for_initial_phase": [
    "Integrasi API otomatis dengan sistem LPM (pelaporan dilakukan secara manual melalui export data).",
    "Pengembangan ulang aplikasi SPMI Kemendikbud.",
    "Integrasi dengan sistem keuangan institusi untuk pencairan dana (hanya konfirmasi status).",
    "Sistem penggajian dosen dan staf.",
    "Sistem pembelajaran online (LMS).",
    "Manajemen perpustakaan digital.",
    "Sistem informasi akademik mahasiswa (SIAKAD) - kecuali untuk data dosen/mahasiswa jika ada API yang mudah diakses.",
    "Akses langsung sistem untuk mitra eksternal.",
    "Manajemen kontrak eksternal yang kompleks.",
    "Pengadaan server dan hardware baru (asumsi infrastruktur IT institusi memadai)."
  ],
  "data_sources_for_context": [
    "BUKU PANDUAN PENELITIAN 2024-1.pdf",
    "Project Scope.pdf",
    "RENSTRA STAI ALI 2021-2026.pdf"
  ],
  "additional_notes": [
    "Fokus pada penyederhanaan alur kerja bagi peneliti, terutama dalam pengajuan proposal dan laporan kemajuan.",
    "Admin LPPM memiliki kontrol penuh atas konfigurasi skema dan konfirmasi status.",
    "Penting untuk menerapkan Laravel Policies untuk memastikan otorisasi yang tepat untuk setiap peran pengguna.",
    "Gaya kutipan Turabian 8th Edition Full Note dengan footnote dan ibid harus dipertimbangkan dalam panduan penulisan yang mungkin disediakan di sistem, meskipun konten proposal diunggah sebagai file."
  ]
}
