<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Penelitian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kategori_penelitian_id',
        'judul',
        'bidang_ilmu',
        'mitra',
        'tahun_usulan',
        'tanggal_mulai',
        'tanggal_selesai',
        'dana_diajukan',
        'file_proposal',
        'status',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'dana_diajukan' => 'decimal:2',
        'tahun_usulan' => 'integer',
    ];

    /**
     * Relationship: Penelitian dimiliki oleh User (Dosen)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Penelitian memiliki kategori
     */
    public function kategoriPenelitian(): BelongsTo
    {
        return $this->belongsTo(KategoriPenelitian::class);
    }

    /**
     * Relationship: Penelitian memiliki banyak luaran
     */
    public function luaranPenelitians(): HasMany
    {
        return $this->hasMany(LuaranPenelitian::class);
    }

    /**
     * Get warna badge berdasarkan status
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'menunggu' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            default => 'gray'
        };
    }

    /**
     * Get label status yang user-friendly
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'menunggu' => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => 'Unknown'
        };
    }

    /**
     * Get durasi penelitian dalam bulan
     */
    public function getDurasiAttribute(): int
    {
        return $this->tanggal_mulai->diffInMonths($this->tanggal_selesai);
    }

    /**
     * Format dana diajukan
     */
    public function getDanaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->dana_diajukan, 0, ',', '.');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeByTahun(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun_usulan', $tahun);
    }

    /**
     * Scope untuk penelitian milik dosen tertentu
     */
    public function scopeByDosen(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk penelitian yang sudah disetujui
     */
    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }
}
