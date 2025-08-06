<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ResearchOutput extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'proposal_id',
        'title',
        'type',
        'journal_name',
        'issn',
        'isbn',
        'hki_number',
        'description',
        'status',
        'publication_date',
        'publication_url',
        'verified_by_admin',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'verified_at' => 'datetime',
        'verified_by_admin' => 'boolean',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidence_files')
              ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'video/mp4']);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verified_by_admin', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Helper methods
    public function isVerified(): bool
    {
        return $this->verified_by_admin;
    }

    public function isJournal(): bool
    {
        return str_starts_with($this->type, 'jurnal');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'jurnal_sinta_1' => 'Jurnal Sinta 1',
            'jurnal_sinta_2' => 'Jurnal Sinta 2',
            'jurnal_sinta_3' => 'Jurnal Sinta 3',
            'jurnal_sinta_4' => 'Jurnal Sinta 4',
            'jurnal_sinta_5' => 'Jurnal Sinta 5',
            'jurnal_sinta_6' => 'Jurnal Sinta 6',
            'jurnal_scopus' => 'Jurnal Scopus',
            'buku_isbn' => 'Buku ISBN',
            'hki' => 'HKI',
            'produk_inovasi' => 'Produk Inovasi',
            default => $this->type,
        };
    }
}
