<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'proposal_id',
        'title',
        'type',
        'description',
        'contract_value',
        'start_date',
        'end_date',
        'duration_months',
        'first_party',
        'first_party_representative',
        'first_party_position',
        'second_party_user_id',
        'second_party_position',
        'status',
        'signed_at',
        'signed_by_first_party',
        'second_party_signed_at',
        'deliverables',
        'payment_schedule',
        'terms_and_conditions',
        'special_conditions',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'signed_at' => 'datetime',
        'second_party_signed_at' => 'datetime',
        'approved_at' => 'datetime',
        'deliverables' => 'array',
        'payment_schedule' => 'array',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function secondParty()
    {
        return $this->belongsTo(User::class, 'second_party_user_id');
    }

    public function signedByFirstParty()
    {
        return $this->belongsTo(User::class, 'signed_by_first_party');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSigned($query)
    {
        return $query->whereIn('status', ['signed', 'active', 'completed']);
    }

    public function scopePendingSignature($query)
    {
        return $query->where('status', 'pending_signature');
    }

    // Helper methods
    public function generateContractNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('LPPM/%s/%s/%04d', $year, $month, $count);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'research' => 'Penelitian',
            'service' => 'Pengabdian',
            'collaboration' => 'Kerjasama',
            default => $this->type,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending_signature' => 'Menunggu Tanda Tangan',
            'signed' => 'Ditandatangani',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'terminated' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function isFullySigned(): bool
    {
        return $this->signed_at && $this->second_party_signed_at;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    public function getDaysRemaining(): int
    {
        if ($this->end_date < now()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }

    public function getProgressPercentage(): int
    {
        if ($this->status !== 'active') {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $daysPassed = $this->start_date->diffInDays(now());

        if ($totalDays === 0) {
            return 100;
        }

        return min(100, round(($daysPassed / $totalDays) * 100));
    }
}
