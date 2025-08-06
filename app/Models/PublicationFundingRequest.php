<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PublicationFundingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_output_id',
        'user_id',
        'requested_amount',
        'justification',
        'journal_name',
        'publication_fee',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'publication_fee' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function researchOutput()
    {
        return $this->belongsTo(ResearchOutput::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
