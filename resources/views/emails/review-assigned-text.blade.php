Tugas Review Proposal - LPPM STAI Ali bin Abi Thalib Surabaya

Yth. {{ $review->reviewer->name }},

Anda telah ditugaskan sebagai reviewer untuk proposal penelitian berikut:

Detail Proposal yang Harus Direview:
- Judul: {{ $review->proposal->title }}
- Peneliti: {{ $review->proposal->user->name }}
- Skema: {{ $review->proposal->researchScheme->name }}
- Dana Diajukan: Rp {{ number_format($review->proposal->fund_amount, 0, ',', '.') }}
- Tanggal Submit: {{ $review->proposal->submitted_at->format('d F Y') }}

DEADLINE REVIEW: {{ $review->assigned_at->addDays(14)->format('d F Y') }}
Mohon selesaikan review dalam waktu 14 hari kerja sejak email ini dikirim.

Kriteria Penilaian:
1. Relevansi (1-5): Kesesuaian dengan bidang keilmuan
2. Metodologi (1-5): Kelayakan metode penelitian
3. Originalitas (1-5): Kebaruan dan inovasi penelitian
4. Feasibilitas (1-5): Kemampuan pelaksanaan

Langkah-langkah Review:
1. Login ke sistem LPPM menggunakan akun reviewer Anda
2. Buka menu "Reviews" dan pilih proposal yang ditugaskan
3. Baca proposal secara menyeluruh
4. Berikan penilaian untuk setiap kriteria
5. Tulis komentar dan saran perbaikan
6. Submit hasil review

Mulai Review: {{ config('app.url') }}/admin/reviews

Terima kasih atas kontribusi Anda sebagai reviewer dalam mendukung penelitian berkualitas di STAI Ali bin Abi Thalib Surabaya.

Salam,
Tim LPPM STAI Ali bin Abi Thalib

---
Email ini dikirim otomatis oleh sistem LPPM STAI Ali bin Abi Thalib Surabaya
Jl. Raya Surabaya, Surabaya | Email: lppm@stai-ali.ac.id | Tel: (031) 123-4567
