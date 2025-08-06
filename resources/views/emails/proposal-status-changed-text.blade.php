@php
    $statusText = match($newStatus) {
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'revision_required' => 'Perlu Revisi',
        'under_review' => 'Sedang Review',
        default => 'Status Berubah'
    };
@endphp

Proposal {{ $statusText }} - LPPM STAI Ali bin Abi Thalib Surabaya

Yth. {{ $proposal->user->name }},

Status proposal penelitian Anda telah berubah:

PROPOSAL {{ strtoupper($statusText) }}
{{ $proposal->title }}

Detail Proposal:
- Judul: {{ $proposal->title }}
- Skema: {{ $proposal->researchScheme->name }}
- Dana Diajukan: Rp {{ number_format($proposal->fund_amount, 0, ',', '.') }}
- Status Sebelumnya: {{ ucfirst($oldStatus) }}
- Status Saat Ini: {{ $statusText }}
- Tanggal Perubahan: {{ now()->format('d F Y, H:i') }} WIB

@if($newStatus === 'approved')
SELAMAT! PROPOSAL ANDA DISETUJUI

Langkah Selanjutnya:
1. Menunggu proses pencairan dana tahap pertama
2. Melaksanakan penelitian sesuai timeline yang disetujui
3. Menyampaikan laporan kemajuan secara berkala
4. Mengupload output penelitian ke sistem

@elseif($newStatus === 'rejected')
PROPOSAL TIDAK DISETUJUI

Mohon maaf, proposal Anda tidak dapat disetujui pada periode ini.

Anda dapat:
- Melihat komentar reviewer untuk perbaikan
- Mempersiapkan proposal baru dengan perbaikan
- Mengajukan proposal pada periode berikutnya

@elseif($newStatus === 'revision_required')
PROPOSAL PERLU REVISI

Proposal Anda memerlukan revisi berdasarkan masukan reviewer.

Langkah Selanjutnya:
1. Baca komentar dan saran dari reviewer
2. Lakukan revisi sesuai masukan yang diberikan
3. Upload kembali proposal yang sudah direvisi
4. Submit ulang untuk review tahap berikutnya

@endif

Lihat Detail: {{ config('app.url') }}/admin

Untuk informasi lebih lanjut, silakan hubungi tim LPPM atau akses dashboard sistem.

Salam,
Tim LPPM STAI Ali bin Abi Thalib

---
Email ini dikirim otomatis oleh sistem LPPM STAI Ali bin Abi Thalib Surabaya
Jl. Raya Surabaya, Surabaya | Email: lppm@stai-ali.ac.id | Tel: (031) 123-4567
