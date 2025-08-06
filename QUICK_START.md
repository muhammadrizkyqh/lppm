# 🚀 QUICK START - TESTING FLOW SISTEM LPPM

## ⚡ 5 MENIT QUICK TEST

### 1. **START SERVER**
```bash
cd "c:\Users\muham\Documents\Coding\Laravel\lppm-stai-ali"
php artisan serve
```
✅ Buka: `http://localhost:8000/admin`

### 2. **LOGIN & TEST BASIC FLOW**

#### **👤 Login Admin** (admin@stai-ali.ac.id / password123)
- ✅ Dashboard → Lihat statistik
- ✅ "Skema Penelitian" → Pastikan ada skema aktif
- ✅ "Users" → Pastikan ada dosen, reviewer, pimpinan

#### **📝 Login Dosen** (dosen@stai-ali.ac.id / password123)  
- ✅ "Proposal" → Create proposal baru
- ✅ "Tim Anggota" → Tambah anggota tim
- ✅ Submit proposal

#### **👨‍🏫 Login Admin untuk Review**
- ✅ "Review" → Assign reviewer ke proposal
- ✅ Logout → Login reviewer (reviewer@stai-ali.ac.id / password123)
- ✅ Review → Isi penilaian → Submit

#### **✅ Approve Proposal**
- ✅ Login admin → "Proposal" → Approve proposal
- ✅ "Kontrak" → Generate kontrak
- ✅ "Pendanaan Publikasi" → Test funding

#### **📊 Generate Reports**
- ✅ Dashboard → Export Widget → Test semua PDF export

---

## 🎯 **FLOW TESTING SCENARIOS**

### **SCENARIO A: FULL RESEARCH CYCLE** 
```
Skema Aktif → Proposal Baru → Team → Review → Approve → Kontrak → Monitoring → Seminar → Luaran → Publikasi
```

### **SCENARIO B: REJECTION FLOW**
```
Proposal → Review → Reject → Revisi → Re-submit → Approve
```

### **SCENARIO C: FUNDING FLOW**
```
Proposal Approved → Stage 1 (50%) → Stage 2 (25%) → Stage 3 (25%) → Complete
```

---

## 📋 **KEY THINGS TO VERIFY**

### **✅ Data Flow:**
- Proposal status transitions
- Email notifications working
- File uploads successful
- Team member validation
- Review process complete

### **✅ Permissions:**
- Admin full access
- Dosen own data only
- Reviewer assigned proposals only
- Pimpinan dashboard access

### **✅ Business Rules:**
- Max 4 team members
- Abstract 250 words
- Active scheme only
- File type validation

---

## 📊 **SAMPLE TEST DATA**

### **Test Proposal:**
```
Judul: "Implementasi AI dalam Pendidikan Islam"
Skema: Penelitian Dasar
Abstract: "Penelitian ini mengkaji penggunaan AI..."
Keywords: "AI, pendidikan islam, teknologi"
Anggaran: Rp 75.000.000
```

### **Test Team:**
```
Ketua: Dr. Ahmad (Dosen Tetap)
Anggota 1: Dr. Siti (Dosen)
Anggota 2: Budi (Mahasiswa S2)
```

### **Test Review:**
```
Relevansi: 85
Metodologi: 80
Orisinalitas: 90
Kelayakan: 85
Rekomendasi: Disetujui tanpa revisi
```

---

## 🐛 **JIKA ADA MASALAH**

### **Error umum & solusi:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Reset database
php artisan migrate:fresh --seed

# Fix permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### **Check logs:**
```bash
tail -f storage/logs/laravel.log
```

---

## 📞 **BANTUAN CEPAT**

### **Default Users:**
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@stai-ali.ac.id | password123 |
| Dosen | dosen@stai-ali.ac.id | password123 |
| Reviewer | reviewer@stai-ali.ac.id | password123 |
| Pimpinan | pimpinan@stai-ali.ac.id | password123 |

### **Key URLs:**
- **Dashboard:** `/admin`
- **Proposal:** `/admin/proposals`
- **Review:** `/admin/reviews`
- **Kontrak:** `/admin/contracts`
- **Seminar:** `/admin/seminars`

---

## 🎉 **SUCCESS INDICATORS**

✅ **Level 1 (Basic):** Login semua role berhasil
✅ **Level 2 (Core):** Proposal cycle lengkap (submit → review → approve)
✅ **Level 3 (Advanced):** Kontrak, monitoring, seminar working
✅ **Level 4 (Complete):** Export, notifications, file uploads OK

**🏆 Target: Level 4 - Sistem 100% functional!**

---

**💡 Tip: Mulai dengan Level 1, lalu naik bertahap. Jika stuck di level mana pun, cek troubleshooting guide atau Laravel logs!**
