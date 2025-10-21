<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamProjectApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'project_id',
        'applied_by',
        'message',
        'status',
        'applied_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isWithdrawn()
    {
        return $this->status === 'withdrawn';
    }

    public function approve($reviewerId)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);

        // Добавляем команду к проекту
        $this->project->addTeam(
            $this->team_id,
            'Команда присоединилась к проекту через заявку',
            null
        );
    }

    public function reject($reviewerId)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);
    }

    public function withdraw()
    {
        $this->update([
            'status' => 'withdrawn',
        ]);
    }

    public static function createApplication($teamId, $projectId, $appliedBy, $message = null)
    {
        return self::create([
            'team_id' => $teamId,
            'project_id' => $projectId,
            'applied_by' => $appliedBy,
            'message' => $message,
            'applied_at' => now(),
        ]);
    }
}
