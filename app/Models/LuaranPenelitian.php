<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuaranPenelitian extends Model
{
    use HasFactory;

    protected $fillable = [
        'penelitian_id',
        'jenis_luaran',
        'judul_luaran',
        'media_publikasi',
        'tanggal_terbit',
        'file_luaran',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    /**
     * Relationship: Luaran milik penelitian tertentu
     */
    public function penelitian(): BelongsTo
    {
        return $this->belongsTo(Penelitian::class);
    }

    /**
     * Get jenis luaran options
     */
    public static function getJenisLuaranOptions(): array
    {
        return [
            'jurnal' => 'Jurnal Ilmiah',
            'prosiding' => 'Prosiding Seminar',
            'buku' => 'Buku',
            'hki' => 'Hak Kekayaan Intelektual',
            'paten' => 'Paten',
            'artikel' => 'Artikel Populer',
            'media' => 'Media Massa',
            'lainnya' => 'Lainnya'
        ];
    }

    /**
     * Get label jenis luaran
     */
    public function getJenisLuaranLabelAttribute(): string
    {
        $options = self::getJenisLuaranOptions();
        return $options[$this->jenis_luaran] ?? $this->jenis_luaran;
    }

    /**
     * Scope untuk filter berdasarkan jenis luaran
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_luaran', $jenis);
    }

    /**
     * Scope untuk luaran yang memiliki file
     */
    public function scopeHasFile($query)
    {
        return $query->whereNotNull('file_luaran');
    }
}
