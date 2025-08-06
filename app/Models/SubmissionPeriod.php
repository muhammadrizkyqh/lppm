<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SubmissionPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'year',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public static function isSubmissionOpen(): bool
    {
        $currentDate = now()->toDateString();

        return self::where('is_active', true)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->exists();
    }

    public static function getCurrentPeriod(): ?self
    {
        $currentDate = now()->toDateString();

        return self::where('is_active', true)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->first();
    }

    public static function getValidationMessage(): ?string
    {
        if (self::isSubmissionOpen()) {
            return null;
        }

        $nextPeriod = self::where('is_active', true)
            ->where('start_date', '>', now()->toDateString())
            ->orderBy('start_date')
            ->first();

        if ($nextPeriod) {
            return "Pengajuan proposal ditutup. Periode berikutnya: {$nextPeriod->name} ({$nextPeriod->start_date->format('d M Y')} - {$nextPeriod->end_date->format('d M Y')})";
        }

        return 'Pengajuan proposal saat ini ditutup. Silakan hubungi admin untuk informasi periode pengajuan.';
    }
}
