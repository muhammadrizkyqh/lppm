<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nim',
        'email',
        'phone',
        'education_level',
        'study_program',
        'department',
        'semester',
        'entry_year',
        'gpa',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'entry_year' => 'integer',
        'semester' => 'integer',
    ];

    // Relationships
    public function proposalTeamMembers()
    {
        return $this->hasMany(ProposalTeamMember::class, 'student_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }
}
