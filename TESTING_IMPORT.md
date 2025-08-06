# PANDUAN TESTING FITUR IMPORT

## 🎯 LANGKAH-LANGKAH TESTING IMPORT MAHASISWA

### 1. **Persiapan File**
- Template sudah tersedia di: `public/templates/template_import_mahasiswa.csv`
- Berisi 50 data mahasiswa sample dengan jenjang S1, S2, S3
- Format CSV dengan header bahasa Indonesia

### 2. **Akses Admin Panel**
- Login sebagai admin: `admin@lppm.ac.id` / `password`
- Atau gunakan user lain yang memiliki akses admin

### 3. **Navigasi ke Import**
- Buka menu **"Data Master"** → **"Mahasiswa"**
- Klik tombol **"Import Data Mahasiswa"** (warna hijau dengan icon upload)

### 4. **Upload File**
- Pilih file `template_import_mahasiswa.csv`
- Sistem akan membaca header dan menampilkan preview

### 5. **Mapping Kolom**
- Sistem akan otomatis mapping kolom berdasarkan label
- Periksa mapping berikut:
  - `Nama Lengkap` → `name`
  - `NIM` → `nim`
  - `Email` → `email`
  - `Jenjang Pendidikan` → `education_level`
  - dst.

### 6. **Validasi Data**
- Sistem akan melakukan validasi:
  - ✅ Email unik
  - ✅ NIM unik
  - ✅ Jenjang pendidikan (S1/S2/S3)
  - ✅ IPK dalam range 0-4
  - ✅ Tahun masuk valid

### 7. **Jalankan Import**
- Klik **"Import"**
- Monitor progress bar
- Tunggu notifikasi sukses

### 8. **Verifikasi Hasil**
- Cek total data: harus bertambah 50 mahasiswa
- Filter berdasarkan jenjang untuk memastikan distribusi benar
- Periksa beberapa record secara detail

## 🎯 TESTING IMPORT DOSEN

### Template Dosen
- File: `public/templates/template_import_dosen.csv`
- Akses: **"Data Master"** → **"Dosen"** → **"Import Data Dosen"**

## ⚠️ TROUBLESHOOTING

### Error yang Mungkin Muncul:
1. **Duplicate Email/NIM**: Data sudah ada di database
2. **Invalid Enum Value**: Nilai tidak sesuai dengan pilihan yang tersedia
3. **File Format Error**: Pastikan file CSV dengan encoding UTF-8

### Solusi:
- Bersihkan data duplicate sebelum import
- Periksa format data sesuai template
- Gunakan editor yang mendukung UTF-8

## 📊 EXPECTED RESULTS

Setelah import berhasil:
- **50 mahasiswa baru** ditambahkan
- **Distribusi jenjang**:
  - S1: 35 mahasiswa (70%)
  - S2: 9 mahasiswa (18%)
  - S3: 6 mahasiswa (12%)
- **Program studi** beragam
- **IPK** bervariasi 3.12 - 3.94
- **Status aktif** semua True

Happy Testing! 🚀
