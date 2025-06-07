<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPenelitian extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relationship: Kategori memiliki banyak penelitian
     */
    public function penelitians(): HasMany
    {
        return $this->hasMany(Penelitian::class);
    }

    /**
     * Get jumlah penelitian per kategori
     */
    public function getJumlahPenelitianAttribute(): int
    {
        return $this->penelitians()->count();
    }

    /**
     * Scope untuk kategori yang aktif (memiliki penelitian)
     */
    public function scopeActive($query)
    {
        return $query->has('penelitians');
    }
}
