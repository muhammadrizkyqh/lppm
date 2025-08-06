<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Berhasil Disubmit</title>
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
            background-color: #2563eb;
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
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 15px 0;
        }
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Proposal Berhasil Disubmit</h1>
        <p>LPPM STAI Ali bin Abi Thalib Surabaya</p>
    </div>

    <div class="content">
        <p>Yth. {{ $proposal->user->name }},</p>

        <p>Selamat! Proposal penelitian Anda telah berhasil disubmit ke sistem LPPM STAI Ali bin Abi Thalib Surabaya.</p>

        <div class="info-box">
            <h3>📋 Detail Proposal:</h3>
            <ul>
                <li><strong>Judul:</strong> {{ $proposal->title }}</li>
                <li><strong>Skema:</strong> {{ $proposal->researchScheme->name }}</li>
                <li><strong>Dana Diajukan:</strong> Rp {{ number_format($proposal->fund_amount, 0, ',', '.') }}</li>
                <li><strong>Tanggal Submit:</strong> {{ $proposal->submitted_at->format('d F Y, H:i') }} WIB</li>
                <li><strong>Status:</strong> <span style="color: #f59e0b;">{{ ucfirst($proposal->status) }}</span></li>
            </ul>
        </div>

        <p><strong>Langkah Selanjutnya:</strong></p>
        <ol>
            <li>Proposal akan direview oleh tim reviewer LPPM</li>
            <li>Proses review membutuhkan waktu 7-14 hari kerja</li>
            <li>Anda akan mendapat notifikasi hasil review via email</li>
            <li>Pantau status proposal melalui dashboard sistem</li>
        </ol>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/admin" class="button">
                🔗 Akses Dashboard
            </a>
        </div>

        <p><em>Terima kasih atas partisipasi Anda dalam penelitian di STAI Ali bin Abi Thalib Surabaya.</em></p>

        <p>Salam,<br>
        <strong>Tim LPPM STAI Ali bin Abi Thalib</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem LPPM STAI Ali bin Abi Thalib Surabaya</p>
        <p>Jl. Raya Surabaya, Surabaya | Email: lppm@stai-ali.ac.id | Tel: (031) 123-4567</p>
    </div>
</body>
</html>
