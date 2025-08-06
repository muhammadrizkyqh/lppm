<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'reviewer_id',
        'assigned_by',
        'relevance_score',
        'methodology_score',
        'originality_score',
        'feasibility_score',
        'total_score',
        'relevance_comment',
        'methodology_comment',
        'originality_comment',
        'feasibility_comment',
        'general_comment',
        'recommendation',
        'status',
        'assigned_at',
        'submitted_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'submitted_at' => 'datetime',
        'total_score' => 'decimal:2',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Mutators
    public function setTotalScoreAttribute()
    {
        if ($this->relevance_score && $this->methodology_score && $this->originality_score && $this->feasibility_score) {
            $this->attributes['total_score'] = ($this->relevance_score + $this->methodology_score + $this->originality_score + $this->feasibility_score) / 4;
        }
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByRecommendation($query, $recommendation)
    {
        return $query->where('recommendation', $recommendation);
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTotalScore(): float
    {
        if (!$this->relevance_score || !$this->methodology_score || !$this->originality_score || !$this->feasibility_score) {
            return 0;
        }

        return ($this->relevance_score + $this->methodology_score + $this->originality_score + $this->feasibility_score) / 4;
    }
}
