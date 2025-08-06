# 🧪 PANDUAN TESTING FLOW SISTEM LPPM

## 📋 CHECKLIST TESTING LENGKAP

### 🔧 **PERSIAPAN TESTING**

1. **Start Server**
   ```bash
   php artisan serve
   ```
   Akses: `http://localhost:8000/admin`

2. **Login Default Users**
   - **Admin:** admin@stai-ali.ac.id / password123
   - **Dosen:** dosen@stai-ali.ac.id / password123  
   - **Reviewer:** reviewer@stai-ali.ac.id / password123
   - **Pimpinan:** pimpinan@stai-ali.ac.id / password123

---

## 🎯 **SCENARIO TESTING A-Z**

### **STEP 1: SETUP ADMIN** ⚙️
**Login sebagai Admin LPPM**

1. **Buat Skema Penelitian**
   - Menu: "Manajemen Skema" → "Skema Penelitian" → "Create"
   - Isi: Nama, Deskripsi, Dana Default, Periode Pendaftaran
   - Set status: Active ✅
   - **Expected:** Skema tersimpan dan aktif untuk pendaftaran

2. **Verifikasi Users**
   - Menu: "User Management" → "Users"
   - Pastikan ada user dengan role: dosen, reviewer, pimpinan
   - **Expected:** Semua role tersedia dan aktif

---

### **STEP 2: PENGAJUAN PROPOSAL** 📝
**Login sebagai Dosen**

1. **Buat Proposal Baru**
   - Menu: "Manajemen Proposal" → "Proposal" → "Create"
   - Pilih skema yang aktif
   - Isi form lengkap:
     - Judul penelitian
     - Abstract (max 250 kata)
     - Keywords
     - Anggaran (auto-fill dari skema)
     - Rencana publikasi
   - **Expected:** Form tersimpan sebagai draft

2. **Kelola Tim Peneliti**
   - Menu: "Manajemen Proposal" → "Tim Anggota" → "Create"
   - Pilih proposal yang dibuat
   - Tambah anggota (max 4 total):
     - 1 Ketua (dosen tetap)
     - 2-3 Anggota (dosen/mahasiswa)
   - **Expected:** Tim tersimpan dengan validasi role

3. **Upload Dokumen**
   - Edit proposal yang dibuat
   - Upload file proposal (PDF/Word)
   - **Expected:** File terupload dan tersimpan

4. **Submit Proposal**
   - Edit proposal → Change status ke "Submitted"
   - **Expected:** Status berubah, notifikasi email terkirim

---

### **STEP 3: REVIEW PROCESS** 👨‍🏫
**Login sebagai Admin untuk assign reviewer**

1. **Assign Reviewer**
   - Menu: "Manajemen Review" → "Review" → "Create"
   - Pilih proposal yang submitted
   - Assign reviewer eksternal
   - **Expected:** Reviewer dapat mengakses proposal

**Login sebagai Reviewer**

2. **Lakukan Review**
   - Menu: "Review" → Lihat proposal assigned
   - Download dokumen proposal
   - Isi form penilaian:
     - Skor relevansi (1-100)
     - Skor metodologi (1-100) 
     - Skor orisinalitas (1-100)
     - Skor kelayakan (1-100)
     - Komentar untuk setiap aspek
     - Rekomendasi: Disetujui/Revisi/Ditolak
   - Submit review
   - **Expected:** Review tersimpan, admin dapat lihat hasil

---

### **STEP 4: KEPUTUSAN PROPOSAL** ✅
**Login sebagai Admin**

1. **Lihat Hasil Review**
   - Menu: "Manajemen Review" → "Review"
   - Cek skor dan rekomendasi reviewer
   - **Expected:** Data review lengkap tersedia

2. **Approve/Reject Proposal**
   - Menu: "Manajemen Proposal" → "Proposal" 
   - Edit proposal → Ubah status ke "Approved" atau "Rejected"
   - **Expected:** Status berubah, email notifikasi terkirim ke dosen

---

### **STEP 5: KONTRAK & PENCAIRAN** 💰
**Login sebagai Admin**

1. **Generate Kontrak**
   - Menu: "Manajemen Kontrak" → "Kontrak" → "Create"
   - Pilih proposal yang approved
   - Isi detail kontrak:
     - Nomor kontrak (auto-generate)
     - Nilai kontrak (dari proposal)
     - Periode kontrak
     - Deliverables
     - Payment schedule
   - **Expected:** Kontrak tersimpan dengan status draft

2. **Aktivasi Kontrak**
   - Edit kontrak → Ubah status ke "Active"
   - Set tanggal signature
   - **Expected:** Kontrak aktif, pencairan bisa dimulai

3. **Konfirmasi Pencairan Dana**
   - Edit proposal yang approved
   - Konfirmasi tahapan:
     - ✅ Fund Stage 1 (50%) - Setelah SK
     - ✅ Fund Stage 2 (25%) - Setelah laporan hasil
     - ✅ Fund Stage 3 (25%) - Setelah luaran
   - **Expected:** Status pencairan terupdate dengan timestamp

---

### **STEP 6: MONITORING** 📊
**Login sebagai Admin**

1. **Konfirmasi Monitoring**
   - Edit proposal yang sedang berjalan
   - Konfirmasi monitoring:
     - ✅ Monitoring 1 (Progress pertama)
     - ✅ Monitoring 2 (Progress kedua) 
   - **Expected:** Status monitoring terupdate

**Login sebagai Dosen**

2. **Upload Laporan Akhir**
   - Edit proposal yang sudah monitoring 2
   - Upload dokumen laporan akhir (PDF/Word)
   - **Expected:** File laporan terupload

---

### **STEP 7: SEMINAR** 🎤
**Login sebagai Admin**

1. **Jadwalkan Seminar**
   - Menu: "Manajemen Seminar" → "Seminar" → "Create"
   - Pilih proposal untuk seminar
   - Set jadwal, lokasi, agenda
   - **Expected:** Seminar terjadwal

**Login sebagai Dosen**

2. **Upload Materi Presentasi**
   - Menu: "Manajemen Seminar" → Edit seminar
   - Upload file presentasi
   - **Expected:** Materi terupload

**Login sebagai Admin**

3. **Selesaikan Seminar**
   - Edit seminar → Ubah status ke "Completed"
   - Isi feedback dan notes
   - **Expected:** Seminar selesai dengan dokumentasi

---

### **STEP 8: LUARAN PENELITIAN** 🏆
**Login sebagai Dosen**

1. **Upload Bukti Luaran**
   - Menu: "Manajemen Luaran" → "Luaran Penelitian" → "Create"
   - Pilih proposal
   - Pilih jenis luaran (Jurnal Sinta, Buku ISBN, HKI, dll)
   - Upload bukti file
   - **Expected:** Luaran tersimpan untuk verifikasi

**Login sebagai Admin**

2. **Verifikasi Luaran**
   - Menu: "Manajemen Luaran" → "Luaran Penelitian"
   - Review bukti yang diupload
   - ✅ Approve luaran
   - **Expected:** Luaran terverifikasi

---

### **STEP 9: PUBLIKASI FUNDING** 💸
**Login sebagai Dosen**

1. **Ajukan Bantuan Publikasi**
   - Menu: "Penelitian" → "Pendanaan Publikasi" → "Create"
   - Pilih proposal
   - Isi detail publikasi:
     - Jenis (Jurnal Nasional/Internasional/Konferensi/Buku)
     - Nama jurnal/penerbit
     - Abstract
     - Estimasi biaya
     - Breakdown biaya
   - **Expected:** Pengajuan tersimpan untuk review

**Login sebagai Admin**

2. **Review & Approve Funding**
   - Menu: "Penelitian" → "Pendanaan Publikasi"
   - Review pengajuan dosen
   - Approve/Reject dengan notes
   - Set approved amount
   - **Expected:** Status funding terupdate

3. **Mark as Published**
   - Setelah publish selesai
   - Edit funding → "Tandai Terbit"
   - Isi URL publikasi dan DOI
   - **Expected:** Status menjadi "Published"

---

### **STEP 10: REPORTING & EXPORT** 📊
**Login sebagai Admin/Pimpinan**

1. **Lihat Dashboard**
   - Menu: Dashboard
   - Cek widget statistik:
     - Total proposal
     - Proposal approved
     - Publikasi tercapai
     - Dana tersalurkan
   - **Expected:** Data real-time tersedia

2. **Export Laporan**
   - Menu: Dashboard → Export Widget
   - Test semua export:
     - ✅ Laporan Proposal (PDF)
     - ✅ Laporan Kontrak (PDF)
     - ✅ Laporan Seminar (PDF)
     - ✅ Laporan Publikasi (PDF)
     - ✅ Laporan Komprehensif (PDF)
   - **Expected:** PDF ter-generate dan ter-download

---

## ✅ **VALIDATION CHECKLIST**

### **Email Notifications:**
- [ ] Proposal submitted notification
- [ ] Review assigned notification  
- [ ] Proposal status changed notification
- [ ] Funding decision notification

### **File Uploads:**
- [ ] Proposal documents (PDF/Word)
- [ ] Final reports (PDF/Word)
- [ ] Research outputs (PDF/Images/Videos)
- [ ] Presentation materials (PDF/PPT)

### **Access Control:**
- [ ] Dosen only sees own data
- [ ] Reviewer only sees assigned proposals
- [ ] Admin has full access
- [ ] Pimpinan has read-only access

### **Business Rules:**
- [ ] Max 4 team members validation
- [ ] Team leader must be permanent lecturer
- [ ] Active scheme selection only
- [ ] Abstract 250 words limit
- [ ] File size limits working

### **Status Transitions:**
- [ ] Draft → Submitted → Under Review → Approved/Rejected
- [ ] Contract: Draft → Signed → Active → Completed
- [ ] Seminar: Scheduled → Ongoing → Completed
- [ ] Publication: Draft → Submitted → Under Review → Approved → Published

---

## 🐛 **COMMON ISSUES TO CHECK**

1. **Database Relationships**
   - Proposal → Team Members
   - Proposal → Reviews
   - Proposal → Contracts
   - Proposal → Seminars
   - Proposal → Research Outputs
   - Proposal → Publication Fundings

2. **File Upload Issues**
   - Storage permissions
   - File size limits
   - MIME type validation

3. **Email Configuration**
   - SMTP settings
   - Queue processing
   - Email templates

4. **Permission Issues**
   - Role assignments
   - Resource access
   - Action permissions

---

## 📞 **TROUBLESHOOTING**

### **Jika ada error:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database connection
3. Verify file permissions
4. Clear cache: `php artisan cache:clear`
5. Refresh config: `php artisan config:cache`

### **Reset testing data:**
```bash
php artisan migrate:fresh --seed
```

---

**🎉 Selamat! Jika semua step berhasil, sistem LPPM sudah siap digunakan production!**
