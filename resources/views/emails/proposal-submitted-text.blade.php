Proposal Berhasil Disubmit - LPPM STAI Ali bin Abi Thalib Surabaya

Yth. {{ $proposal->user->name }},

Selamat! Proposal penelitian Anda telah berhasil disubmit ke sistem LPPM STAI Ali bin Abi Thalib Surabaya.

Detail Proposal:
- Judul: {{ $proposal->title }}
- Skema: {{ $proposal->researchScheme->name }}
- Dana Diajukan: Rp {{ number_format($proposal->fund_amount, 0, ',', '.') }}
- Tanggal Submit: {{ $proposal->submitted_at->format('d F Y, H:i') }} WIB
- Status: {{ ucfirst($proposal->status) }}

Langkah Selanjutnya:
1. Proposal akan direview oleh tim reviewer LPPM
2. Proses review membutuhkan waktu 7-14 hari kerja
3. Anda akan mendapat notifikasi hasil review via email
4. Pantau status proposal melalui dashboard sistem

Akses Dashboard: {{ config('app.url') }}/admin

Terima kasih atas partisipasi Anda dalam penelitian di STAI Ali bin Abi Thalib Surabaya.

Salam,
Tim LPPM STAI Ali bin Abi Thalib

---
Email ini dikirim otomatis oleh sistem LPPM STAI Ali bin Abi Thalib Surabaya
Jl. Raya Surabaya, Surabaya | Email: lppm@stai-ali.ac.id | Tel: (031) 123-4567
