<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProposalTeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'user_id',
        'name',
        'nidn',
        'nim',
        'member_type',
        'role',
        'position',
        'department',
        'expertise',
        'phone',
        'email',
        'study_program',
        'semester',
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
}
