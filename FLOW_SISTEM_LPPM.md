# FLOW SISTEM LPPM STAI ALI BIN ABI THALIB SURABAYA

## 🎯 OVERVIEW SISTEM
Sistem ini mengelola seluruh siklus kegiatan penelitian dan pengabdian kepada masyarakat dari proposal hingga publikasi dengan 4 peran pengguna dan 11 modul utama.

---

## 👥 ROLES & PERMISSIONS

### 1. **ADMIN LPPM** (Kontrol Penuh)
- ✅ Manajemen skema penelitian/pengabdian
- ✅ Verifikasi administratif proposal
- ✅ Assign reviewer ke proposal
- ✅ Konfirmasi pencairan dana (Tahap 1, 2, 3)
- ✅ Konfirmasi monitoring (Pertama & Kedua)
- ✅ Verifikasi luaran penelitian
- ✅ Jadwalkan seminar hasil
- ✅ Dashboard & export laporan

### 2. **DOSEN PENELITI**
- ✅ Ajukan proposal penelitian/pengabdian
- ✅ Kelola anggota tim (max 4 orang)
- ✅ Upload dokumen proposal & revisi
- ✅ Upload laporan akhir & bukti luaran
- ✅ Ajukan bantuan biaya publikasi
- ✅ Upload materi presentasi seminar

### 3. **REVIEWER EKSTERNAL**
- ✅ Review & nilai proposal yang ditugaskan
- ✅ Berikan rekomendasi (setuju/revisi/tolak)
- ✅ Form penilaian substansi lengkap

### 4. **PIMPINAN FAKULTAS**
- ✅ Dashboard monitoring progres
- ✅ Laporan capaian penelitian
- ✅ Approve workflow tertentu

---

## 🔄 FLOW UTAMA SISTEM

### **TAHAP 1: PERSIAPAN** 
```
Admin LPPM → Buat Skema Penelitian → Set Periode Pendaftaran → Aktivasi
```

### **TAHAP 2: PENGAJUAN PROPOSAL**
```
Dosen → Login → Pilih Skema Aktif → Isi Form Proposal → Kelola Tim → Upload Dokumen → Submit
```

### **TAHAP 3: REVIEW & PENILAIAN**
```
Admin → Assign Reviewer → Reviewer Login → Review Proposal → Nilai & Rekomendasi → Submit Review
```

### **TAHAP 4: KEPUTUSAN PROPOSAL**
```
Admin → Lihat Hasil Review → Approve/Reject Proposal → Notifikasi ke Dosen → Update Status
```

### **TAHAP 5: KONTRAK & PENCAIRAN**
```
Proposal Approved → Generate Kontrak → Tanda Tangan Digital → Admin Konfirmasi Pencairan:
- Tahap 1 (50%) setelah SK
- Tahap 2 (25%) setelah laporan hasil  
- Tahap 3 (25%) setelah luaran
```

### **TAHAP 6: MONITORING & EVALUASI**
```
Admin Konfirmasi:
- Monitoring Pertama (progress penelitian)
- Monitoring Kedua (hampir selesai)
→ Dosen Upload Laporan Akhir
```

### **TAHAP 7: SEMINAR & LUARAN**
```
Admin Jadwalkan Seminar → Dosen Upload Materi Presentasi → Seminar Selesai → Upload Bukti Luaran
```

### **TAHAP 8: PUBLIKASI & PELAPORAN**
```
Dosen Ajukan Bantuan Publikasi → Admin Review & Approve → Publikasi Selesai → Generate Laporan
```

---

## 📊 STATUS WORKFLOW

### **Status Proposal:**
- `draft` → `submitted` → `under_review` → `approved`/`rejected`

### **Status Kontrak:**
- `draft` → `signed` → `active` → `completed`

### **Status Seminar:**
- `scheduled` → `ongoing` → `completed`

### **Status Publikasi:**
- `draft` → `submitted` → `under_review` → `approved` → `published`

### **Status Pencairan Dana:**
- `fund_stage_1_confirmed` (50%)
- `fund_stage_2_confirmed` (25%) 
- `fund_stage_3_confirmed` (25%)

### **Status Monitoring:**
- `monitoring_1_confirmed`
- `monitoring_2_confirmed`

---

## 🎯 BUSINESS RULES

### **Proposal:**
- Max 4 anggota tim (1 ketua + 3 anggota)
- Ketua harus dosen tetap dengan jabatan fungsional
- Skema harus aktif saat pengajuan
- Abstract max 250 kata

### **Review:**
- Kriteria: Relevansi, Metodologi, Orisinalitas, Kelayakan
- Scale penilaian dengan rekomendasi
- Reviewer dapat download dokumen proposal

### **Tim Management:**
- Validasi otomatis ketua tim
- Mahasiswa boleh jadi anggota
- Auto-populate data dosen dari database

### **Luaran:**
- Sesuai jenis yang diharapkan dari skema
- Upload bukti file (PDF, gambar, video)
- Verifikasi admin required

---

## 📁 STRUKTUR DATABASE

### **Core Tables:**
- `users` - Data pengguna & profil
- `research_schemes` - Skema penelitian/pengabdian  
- `proposals` - Data proposal dengan tracking
- `proposal_team_members` - Anggota tim
- `reviews` - Penilaian reviewer
- `contracts` - Kontrak digital
- `seminars` - Jadwal & hasil seminar
- `research_outputs` - Luaran penelitian
- `publication_fundings` - Bantuan publikasi

### **Supporting Tables:**
- `roles` & `permissions` - RBAC system
- `media` - File uploads (Spatie Media Library)
- `notifications` - Email notifications

---

## 🔧 FITUR TAMBAHAN

### **Dashboard & Analytics:**
- Stats widget dengan metrics utama
- Timeline progress penelitian
- Target vs capaian publikasi (40/tahun)

### **Export System:**
- Laporan Proposal (PDF)
- Laporan Kontrak (PDF)  
- Laporan Seminar (PDF)
- Laporan Publikasi (PDF)
- Laporan Komprehensif (PDF)

### **Notification System:**
- Email otomatis untuk perubahan status
- Reminder deadline laporan
- Queue-based email processing

### **File Management:**
- Upload dokumen proposal (PDF/Word)
- Upload laporan akhir
- Upload bukti luaran (multiple format)
- Upload materi presentasi

---

## 🚀 GETTING STARTED

### **Admin Setup:**
1. Login sebagai admin@stai-ali.ac.id
2. Buat skema penelitian baru
3. Set periode pendaftaran
4. Aktivasi skema

### **Dosen Workflow:**
1. Login dengan akun dosen
2. Pilih "Proposal" → Create
3. Lengkapi form & upload dokumen
4. Kelola tim di "Tim Anggota"
5. Submit proposal

### **Reviewer Process:**
1. Login reviewer
2. Lihat proposal yang ditugaskan
3. Download & review dokumen
4. Isi form penilaian
5. Submit rekomendasi

---

## 📈 MONITORING & REPORTING

### **Key Metrics:**
- Total proposal per periode
- Approval rate
- Dana tersalurkan
- Publikasi tercapai
- Timeline compliance

### **Regular Reports:**
- Monthly progress report
- Annual research summary
- Financial disbursement tracking
- Publication achievement report

---

## ⚠️ IMPORTANT NOTES

### **Yang TIDAK ada di sistem:**
- Upload laporan kemajuan (offline process)
- Upload laporan keuangan (offline process)
- Integrasi sistem keuangan institusi
- API otomatis ke sistem eksternal

### **Yang perlu perhatian:**
- File upload size limits
- Email notification configuration
- Database backup strategy
- User training untuk semua roles

---

## 📞 SUPPORT & MAINTENANCE

### **Technical Stack:**
- Laravel 12.x (Backend)
- FilamentPHP 3.x (Admin Interface)
- MySQL (Database)
- Spatie Packages (Permissions, Media)
- DomPDF (Export)

### **Access URLs:**
- **Admin Panel:** `http://localhost:8000/admin`
- **Login:** Various role-based dashboards
- **API:** Future enhancement ready

### **Default Users:**
- Admin: admin@stai-ali.ac.id
- Dosen: dosen@stai-ali.ac.id  
- Reviewer: reviewer@stai-ali.ac.id
- Pimpinan: pimpinan@stai-ali.ac.id
- Password: password123

---

*Sistem ini telah dibangun sesuai dengan BUKU PANDUAN PENELITIAN 2024 dan RENSTRA STAI ALI 2021-2026*
