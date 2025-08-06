<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $title
 * @property string $abstract
 * @property string $keywords
 * @property string $status
 * @property int $user_id
 * @property int $research_scheme_id
 * @property float $fund_amount
 * @property string|null $publication_plan
 * @property \Carbon\Carbon|null $submitted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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

    public function user()
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

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function publicationFundings()
    {
        return $this->hasMany(PublicationFunding::class);
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('proposal_documents')
              ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

        $this->addMediaCollection('final_reports')
              ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(150)
              ->height(150)
              ->sharpen(10);
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

    public function getTitleWithResearcherAttribute(): string
    {
        return $this->title . ' - ' . $this->user->name;
    }

    // Business Rules Validation
    public static function validateLeaderRules($userId, $year = null)
    {
        $year = $year ?: date('Y');

        // Check if user is a permanent lecturer with functional position
        $user = User::find($userId);
        if (!$user || $user->user_type !== 'dosen' || $user->employment_status !== 'tetap') {
            return 'Ketua penelitian harus dosen tetap';
        }

        if (!$user->functional_position || in_array($user->functional_position, ['tidak_ada', 'non_fungsional'])) {
            return 'Ketua penelitian harus memiliki jabatan fungsional minimal';
        }

        // Check total proposals limit (as leader + member = max 4 per year)
        $totalProposals = self::getTotalProposalsForUser($userId, $year);
        if ($totalProposals >= 4) {
            return 'Dosen maksimal dapat terlibat dalam 4 proposal per tahun (sebagai ketua atau anggota)';
        }

        return null; // Valid
    }

    public static function validateMemberRules($userId, $year = null)
    {
        $year = $year ?: date('Y');

        // Check total proposals limit (as leader + member = max 4 per year)
        $totalProposals = self::getTotalProposalsForUser($userId, $year);
        if ($totalProposals >= 4) {
            return 'Dosen maksimal dapat terlibat dalam 4 proposal per tahun (sebagai ketua atau anggota)';
        }

        return null; // Valid
    }

    public static function getTotalProposalsForUser($userId, $year = null)
    {
        $year = $year ?: date('Y');

        // Count as leader
        $asLeader = self::where('user_id', $userId)
            ->whereYear('created_at', $year)
            ->where('status', '!=', 'rejected')
            ->count();

        // Count as member
        $asMember = ProposalTeamMember::where('user_id', $userId)
            ->whereHas('proposal', function($q) use ($year) {
                $q->whereYear('created_at', $year)
                  ->where('status', '!=', 'rejected');
            })
            ->count();

        return $asLeader + $asMember;
    }

    public static function validateSubmissionPeriod()
    {
        return \App\Models\SubmissionPeriod::getValidationMessage();
    }

    public static function validateStudentMemberRules($studentId, $year = null)
    {
        $year = $year ?: date('Y');

        // Check if student exists and is active
        $student = \App\Models\Student::find($studentId);
        if (!$student || !$student->is_active) {
            return 'Mahasiswa tidak ditemukan atau tidak aktif';
        }

        // Check if student is already in a proposal this year (max 1 proposal per year)
        $existingMembership = ProposalTeamMember::where('student_id', $studentId)
            ->whereHas('proposal', function($q) use ($year) {
                $q->whereYear('created_at', $year)
                  ->where('status', '!=', 'rejected');
            })
            ->count();

        if ($existingMembership >= 1) {
            return 'Mahasiswa sudah terdaftar dalam proposal lain tahun ini. Maksimal 1 proposal per tahun.';
        }

        return null; // Valid
    }

    public static function validateTeamComposition($currentLeaderId, $teamMembers = [])
    {
        // Check total team size (including leader)
        $totalMembers = count($teamMembers) + 1; // +1 for leader

        if ($totalMembers > 4) {
            return 'Tim maksimal terdiri dari 4 orang (1 ketua + 3 anggota)';
        }

        // Check for duplicate members
        $memberIds = [];
        $studentIds = [];

        foreach ($teamMembers as $member) {
            // Check if leader is not included as member
            if (isset($member['user_id']) && $member['user_id'] == $currentLeaderId) {
                return 'Ketua tim tidak boleh ditambahkan sebagai anggota';
            }

            // Check for duplicate dosen
            if (isset($member['user_id']) && in_array($member['user_id'], $memberIds)) {
                return 'Dosen tidak boleh diduplikasi dalam tim';
            }
            if (isset($member['user_id'])) {
                $memberIds[] = $member['user_id'];
            }

            // Check for duplicate student
            if (isset($member['student_id']) && in_array($member['student_id'], $studentIds)) {
                return 'Mahasiswa tidak boleh diduplikasi dalam tim';
            }
            if (isset($member['student_id'])) {
                $studentIds[] = $member['student_id'];
            }
        }

        return null; // Valid
    }
}
