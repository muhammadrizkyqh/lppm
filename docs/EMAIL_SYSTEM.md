# 📧 Sistem Email Notification LPPM

## Overview
Sistem email notification telah diimplementasikan untuk memberikan notifikasi otomatis kepada pengguna saat terjadi perubahan status dalam workflow penelitian.

## 🔧 Konfigurasi Email

### Environment Variables (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS="lppm@stai-ali.ac.id"
MAIL_FROM_NAME="LPPM STAI Ali bin Abi Thalib"
```

**Note**: Untuk Gmail, gunakan App Password, bukan password biasa.

## 📨 Jenis Email Notification

### 1. Proposal Submitted
- **Trigger**: Saat proposal berhasil disubmit
- **Penerima**: Peneliti yang mengajukan proposal
- **Template**: `emails.proposal-submitted`
- **Class**: `App\Mail\ProposalSubmitted`

### 2. Review Assigned
- **Trigger**: Saat reviewer ditugaskan untuk review proposal
- **Penerima**: Reviewer yang ditugaskan
- **Template**: `emails.review-assigned`
- **Class**: `App\Mail\ReviewAssigned`

### 3. Proposal Status Changed
- **Trigger**: Saat status proposal berubah (approved, rejected, revision_required)
- **Penerima**: Peneliti pemilik proposal
- **Template**: `emails.proposal-status-changed`
- **Class**: `App\Mail\ProposalStatusChanged`

## 🎯 Auto-Trigger System

Email dikirim otomatis menggunakan **Model Observers**:

### ProposalObserver
- Monitor perubahan pada model Proposal
- Trigger email saat `submitted_at` berubah atau status berubah

### ReviewObserver  
- Monitor pembuatan review baru
- Trigger email assignment ke reviewer

## 🚀 Queue System

Email menggunakan **Queue** untuk pengiriman asynchronous:
- Implementasi `ShouldQueue` pada semua mail classes
- Hindari blocking saat pengiriman email
- Dapat dimonitor melalui queue system Laravel

### Menjalankan Queue Worker
```bash
php artisan queue:work
```

## 🧪 Testing Email

### Command Testing
```bash
# Test specific email type
php artisan test:email proposal-submitted --email=test@example.com
php artisan test:email review-assigned --email=test@example.com
php artisan test:email status-changed --email=test@example.com

# Test all email types
php artisan test:email all --email=test@example.com
```

### Development Testing
Untuk development, gunakan:
```env
MAIL_MAILER=log
```
Email akan disimpan di `storage/logs/laravel.log`

## 📋 Template Email

### HTML Templates
- `resources/views/emails/proposal-submitted.blade.php`
- `resources/views/emails/review-assigned.blade.php`
- `resources/views/emails/proposal-status-changed.blade.php`

### Text Templates (Fallback)
- `resources/views/emails/proposal-submitted-text.blade.php`
- `resources/views/emails/review-assigned-text.blade.php`
- `resources/views/emails/proposal-status-changed-text.blade.php`

## 🎨 Customization

### Email Design
Template menggunakan inline CSS dengan design responsive:
- Header dengan branding STAI Ali bin Abi Thalib
- Color coding berdasarkan jenis notifikasi
- Call-to-action buttons
- Footer dengan informasi kontak

### Content Personalization
- Dynamic content berdasarkan data proposal/review
- Status-specific messaging
- Localized text dalam Bahasa Indonesia

## 🔒 Security

- Email content di-escape untuk mencegah XSS
- Sensitive data tidak disertakan dalam email
- Links mengarah ke dashboard yang secure

## 📊 Monitoring

### Queue Monitoring
```bash
# Lihat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Email Logs
Check `storage/logs/laravel.log` untuk email activities

## 🚨 Troubleshooting

### Common Issues

1. **Gmail Authentication Error**
   - Gunakan App Password, bukan password akun
   - Enable 2-factor authentication di Gmail
   
2. **Queue Not Processing**
   - Jalankan `php artisan queue:work`
   - Check database connection untuk queue table

3. **Email Template Error**
   - Pastikan semua variables tersedia
   - Check relationship loading di observer

### Debug Commands
```bash
# Clear config cache
php artisan config:clear

# Test mail configuration
php artisan tinker
> Mail::raw('Test email', function($msg) { $msg->to('test@example.com'); });
```

## 📝 Future Enhancements

- [ ] Email preferences per user
- [ ] Digest email untuk multiple notifications
- [ ] Email templates customizable via admin
- [ ] Email delivery tracking
- [ ] Multi-language support
- [ ] Email scheduling untuk reminder
