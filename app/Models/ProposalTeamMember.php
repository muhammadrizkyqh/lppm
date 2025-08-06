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
        'student_id',
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper methods
    public function getMemberName()
    {
        if ($this->member_type === 'dosen' && $this->user) {
            return $this->user->name;
        } elseif ($this->member_type === 'mahasiswa' && $this->student) {
            return $this->student->name;
        }
        return $this->name;
    }

    public function getMemberIdentifier()
    {
        if ($this->member_type === 'dosen' && $this->user) {
            return $this->user->nidn;
        } elseif ($this->member_type === 'mahasiswa' && $this->student) {
            return $this->student->nim;
        }
        return $this->nidn ?: $this->nim;
    }
}
