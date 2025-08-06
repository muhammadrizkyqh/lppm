<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Proposal Berubah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header.approved { background-color: #10b981; }
        .header.rejected { background-color: #ef4444; }
        .header.revision { background-color: #f59e0b; }
        .header.review { background-color: #3b82f6; }

        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .footer {
            background-color: #64748b;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px 0;
        }
        .button.approved { background-color: #10b981; }
        .button.rejected { background-color: #ef4444; }
        .button.revision { background-color: #f59e0b; }
        .button.review { background-color: #3b82f6; }

        .status-box {
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .status-box.approved {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
        }
        .status-box.rejected {
            background-color: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
        }
        .status-box.revision {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            color: #92400e;
        }
        .status-box.review {
            background-color: #dbeafe;
            border: 2px solid #3b82f6;
            color: #1e40af;
        }

        .info-box {
            background-color: #f3f4f6;
            border-left: 4px solid #6b7280;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    @php
        $statusClass = match($newStatus) {
            'approved' => 'approved',
            'rejected' => 'rejected',
            'revision_required' => 'revision',
            default => 'review'
        };

        $statusIcon = match($newStatus) {
            'approved' => '✅',
            'rejected' => '❌',
            'revision_required' => '🔄',
            default => '🔍'
        };

        $statusText = match($newStatus) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_required' => 'Perlu Revisi',
            'under_review' => 'Sedang Review',
            default => 'Status Berubah'
        };
    @endphp

    <div class="header {{ $statusClass }}">
        <h1>{{ $statusIcon }} Proposal {{ $statusText }}</h1>
        <p>LPPM STAI Ali bin Abi Thalib Surabaya</p>
    </div>

    <div class="content">
        <p>Yth. {{ $proposal->user->name }},</p>

        <p>Status proposal penelitian Anda telah berubah:</p>

        <div class="status-box {{ $statusClass }}">
            <h2>{{ $statusIcon }} {{ $statusText }}</h2>
            <p><strong>{{ $proposal->title }}</strong></p>
        </div>

        <div class="info-box">
            <h3>📋 Detail Proposal:</h3>
            <ul>
                <li><strong>Judul:</strong> {{ $proposal->title }}</li>
                <li><strong>Skema:</strong> {{ $proposal->researchScheme->name }}</li>
                <li><strong>Dana Diajukan:</strong> Rp {{ number_format($proposal->fund_amount, 0, ',', '.') }}</li>
                <li><strong>Status Sebelumnya:</strong> {{ ucfirst($oldStatus) }}</li>
                <li><strong>Status Saat Ini:</strong> <strong>{{ $statusText }}</strong></li>
                <li><strong>Tanggal Perubahan:</strong> {{ now()->format('d F Y, H:i') }} WIB</li>
            </ul>
        </div>

        @if($newStatus === 'approved')
            <div style="background-color: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>🎉 Selamat! Proposal Anda Disetujui</h3>
                <p><strong>Langkah Selanjutnya:</strong></p>
                <ol>
                    <li>Menunggu proses pencairan dana tahap pertama</li>
                    <li>Melaksanakan penelitian sesuai timeline yang disetujui</li>
                    <li>Menyampaikan laporan kemajuan secara berkala</li>
                    <li>Mengupload output penelitian ke sistem</li>
                </ol>
            </div>
        @elseif($newStatus === 'rejected')
            <div style="background-color: #fee2e2; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>😔 Proposal Tidak Disetujui</h3>
                <p>Mohon maaf, proposal Anda tidak dapat disetujui pada periode ini.</p>
                <p><strong>Anda dapat:</strong></p>
                <ul>
                    <li>Melihat komentar reviewer untuk perbaikan</li>
                    <li>Mempersiapkan proposal baru dengan perbaikan</li>
                    <li>Mengajukan proposal pada periode berikutnya</li>
                </ul>
            </div>
        @elseif($newStatus === 'revision_required')
            <div style="background-color: #fef3c7; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>🔄 Proposal Perlu Revisi</h3>
                <p>Proposal Anda memerlukan revisi berdasarkan masukan reviewer.</p>
                <p><strong>Langkah Selanjutnya:</strong></p>
                <ol>
                    <li>Baca komentar dan saran dari reviewer</li>
                    <li>Lakukan revisi sesuai masukan yang diberikan</li>
                    <li>Upload kembali proposal yang sudah direvisi</li>
                    <li>Submit ulang untuk review tahap berikutnya</li>
                </ol>
            </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/admin" class="button {{ $statusClass }}">
                🔗 Lihat Detail di Dashboard
            </a>
        </div>

        <p><em>Untuk informasi lebih lanjut, silakan hubungi tim LPPM atau akses dashboard sistem.</em></p>

        <p>Salam,<br>
        <strong>Tim LPPM STAI Ali bin Abi Thalib</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem LPPM STAI Ali bin Abi Thalib Surabaya</p>
        <p>Jl. Raya Surabaya, Surabaya | Email: lppm@stai-ali.ac.id | Tel: (031) 123-4567</p>
    </div>
</body>
</html>
