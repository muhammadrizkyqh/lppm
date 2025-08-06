<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $proposal_id
 * @property int $user_id
 * @property string $title
 * @property string $publication_type
 * @property string $journal_name
 * @property string $status
 * @property float $estimated_cost
 * @property float $approved_amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PublicationFunding extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'user_id',
        'title',
        'publication_type',
        'journal_name',
        'publisher',
        'abstract',
        'estimated_cost',
        'cost_breakdown',
        'submission_deadline',
        'expected_publication_date',
        'status',
        'reviewer_notes',
        'approved_amount',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'publication_url',
        'doi',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'submission_deadline' => 'date',
        'expected_publication_date' => 'date',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Helper methods
    public function getPublicationTypeLabel(): string
    {
        return match($this->publication_type) {
            'journal_national' => 'Jurnal Nasional',
            'journal_international' => 'Jurnal Internasional',
            'conference_national' => 'Konferensi Nasional',
            'conference_international' => 'Konferensi Internasional',
            'book' => 'Buku',
            'book_chapter' => 'Bab Buku',
            default => $this->publication_type,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'under_review' => 'Sedang Direview',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'published' => 'Diterbitkan',
            default => $this->status,
        };
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
