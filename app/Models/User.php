<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nidn',
        'phone',
        'user_type',
        'functional_position',
        'field_of_study',
        'address',
        'employment_status',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'dosen', 'reviewer', 'pimpinan']) && $this->is_active;
    }

    // Relationships
    public function proposalsAsLeader()
    {
        return $this->hasMany(Proposal::class);
    }

    public function teamMemberships()
    {
        return $this->hasMany(ProposalTeamMember::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function researchOutputs()
    {
        return $this->hasManyThrough(ResearchOutput::class, Proposal::class);
    }

    // Helper methods
    public function isDosen(): bool
    {
        return $this->user_type === 'dosen';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isReviewer(): bool
    {
        return $this->user_type === 'reviewer';
    }

    public function isPimpinan(): bool
    {
        return $this->user_type === 'pimpinan';
    }
}
