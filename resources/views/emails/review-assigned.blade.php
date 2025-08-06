<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Review Proposal</title>
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
            background-color: #7c3aed;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
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
            background-color: #7c3aed;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px 0;
        }
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .urgent {
            background-color: #fecaca;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📝 Tugas Review Proposal</h1>
        <p>LPPM STAI Ali bin Abi Thalib Surabaya</p>
    </div>

    <div class="content">
        <p>Yth. {{ $review->reviewer->name }},</p>

        <p>Anda telah ditugaskan sebagai reviewer untuk proposal penelitian berikut:</p>

        <div class="info-box">
            <h3>📋 Detail Proposal yang Harus Direview:</h3>
            <ul>
                <li><strong>Judul:</strong> {{ $review->proposal->title }}</li>
                <li><strong>Peneliti:</strong> {{ $review->proposal->user->name }}</li>
                <li><strong>Skema:</strong> {{ $review->proposal->researchScheme->name }}</li>
                <li><strong>Dana Diajukan:</strong> Rp {{ number_format($review->proposal->fund_amount, 0, ',', '.') }}</li>
                <li><strong>Tanggal Submit:</strong> {{ $review->proposal->submitted_at->format('d F Y') }}</li>
            </ul>
        </div>

        <div class="urgent">
            <h3>⏰ Deadline Review:</h3>
            <p><strong>Batas waktu review: {{ $review->assigned_at->addDays(14)->format('d F Y') }}</strong></p>
            <p>Mohon selesaikan review dalam waktu <strong>14 hari kerja</strong> sejak email ini dikirim.</p>
        </div>

        <p><strong>Kriteria Penilaian:</strong></p>
        <ol>
            <li><strong>Relevansi (1-5):</strong> Kesesuaian dengan bidang keilmuan</li>
            <li><strong>Metodologi (1-5):</strong> Kelayakan metode penelitian</li>
            <li><strong>Originalitas (1-5):</strong> Kebaruan dan inovasi penelitian</li>
            <li><strong>Feasibilitas (1-5):</strong> Kemampuan pelaksanaan</li>
        </ol>

        <p><strong>Langkah-langkah Review:</strong></p>
        <ol>
            <li>Login ke sistem LPPM menggunakan akun reviewer Anda</li>
            <li>Buka menu "Reviews" dan pilih proposal yang ditugaskan</li>
            <li>Baca proposal secara menyeluruh</li>
            <li>Berikan penilaian untuk setiap kriteria</li>
            <li>Tulis komentar dan saran perbaikan</li>
            <li>Submit hasil review</li>
        </ol>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/admin/reviews" class="button">
                📝 Mulai Review
            </a>
        </div>

        <p><em>Terima kasih atas kontribusi Anda sebagai reviewer dalam mendukung penelitian berkualitas di STAI Ali bin Abi Thalib Surabaya.</em></p>

        <p>Salam,<br>
        <strong>Tim LPPM STAI Ali bin Abi Thalib</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem LPPM STAI Ali bin Abi Thalib Surabaya</p>
        <p>Jl. Raya Surabaya, Surabaya | Email: lppm@stai-ali.ac.id | Tel: (031) 123-4567</p>
    </div>
</body>
</html>
