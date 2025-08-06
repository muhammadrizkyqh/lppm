<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResearchScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_fund_amount',
        'registration_start_date',
        'registration_end_date',
        'expected_outputs',
        'type',
        'is_active',
    ];

    protected $casts = [
        'expected_outputs' => 'array',
        'default_fund_amount' => 'decimal:2',
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpenForRegistration($query)
    {
        $now = now()->toDateString();
        return $query->where('registration_start_date', '<=', $now)
                     ->where('registration_end_date', '>=', $now)
                     ->where('is_active', true);
    }

    // Helper methods
    public function isOpenForRegistration(): bool
    {
        $now = now()->toDateString();
        return $this->is_active &&
               $this->registration_start_date <= $now &&
               $this->registration_end_date >= $now;
    }
}
