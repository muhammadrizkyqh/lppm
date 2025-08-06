<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Proposal extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'research_scheme_id',
        'user_id',
        'abstract',
        'keywords',
        'fund_amount',
        'publication_plan',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'fund_stage_1_confirmed',
        'fund_stage_1_confirmed_at',
        'fund_stage_1_confirmed_by',
        'fund_stage_2_confirmed',
        'fund_stage_2_confirmed_at',
        'fund_stage_2_confirmed_by',
        'fund_stage_3_confirmed',
        'fund_stage_3_confirmed_at',
        'fund_stage_3_confirmed_by',
        'monitoring_1_confirmed',
        'monitoring_1_confirmed_at',
        'monitoring_1_confirmed_by',
        'monitoring_2_confirmed',
        'monitoring_2_confirmed_at',
        'monitoring_2_confirmed_by',
        'has_penalty',
        'penalty_reason',
        'penalty_amount',
    ];

    protected $casts = [
        'fund_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'fund_stage_1_confirmed_at' => 'datetime',
        'fund_stage_2_confirmed_at' => 'datetime',
        'fund_stage_3_confirmed_at' => 'datetime',
        'monitoring_1_confirmed_at' => 'datetime',
        'monitoring_2_confirmed_at' => 'datetime',
        'fund_stage_1_confirmed' => 'boolean',
        'fund_stage_2_confirmed' => 'boolean',
        'fund_stage_3_confirmed' => 'boolean',
        'monitoring_1_confirmed' => 'boolean',
        'monitoring_2_confirmed' => 'boolean',
        'has_penalty' => 'boolean',
    ];

    // Relationships
    public function researchScheme()
    {
        return $this->belongsTo(ResearchScheme::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function teamMembers()
    {
        return $this->hasMany(ProposalTeamMember::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function researchOutputs()
    {
        return $this->hasMany(ResearchOutput::class);
    }

    public function seminars()
    {
        return $this->hasMany(Seminar::class);
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('proposal_documents')
              ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

        $this->addMediaCollection('final_reports')
              ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Helper methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'revision_required']);
    }

    public function getProgressPercentage(): int
    {
        $steps = [
            'fund_stage_1_confirmed',
            'monitoring_1_confirmed',
            'fund_stage_2_confirmed',
            'monitoring_2_confirmed',
            'fund_stage_3_confirmed'
        ];

        $completed = 0;
        foreach ($steps as $step) {
            if ($this->{$step}) {
                $completed++;
            }
        }

        return round(($completed / count($steps)) * 100);
    }
}
