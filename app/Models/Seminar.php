<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seminar extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'title',
        'description',
        'type',
        'scheduled_at',
        'location',
        'meeting_link',
        'status',
        'agenda',
        'notes',
        'feedback',
        'scheduled_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                    ->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('scheduled_at', now()->month)
                    ->whereYear('scheduled_at', now()->year);
    }
}
