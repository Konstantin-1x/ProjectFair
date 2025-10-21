<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamApplication extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'status',
        'message',
        'rejection_reason',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approve($reviewerId)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);

        // Создаем или обновляем связь в pivot с корректными полями
        $existing = $this->team->members()->where('users.id', $this->user_id)->exists();

        $pivotData = [
            'role' => 'member',
            'application_status' => 'approved',
            'applied_at' => $this->applied_at,
            'approved_at' => now(),
            'joined_at' => now(),
        ];

        if ($existing) {
            $this->team->members()->updateExistingPivot($this->user_id, $pivotData);
        } else {
            $this->team->members()->attach($this->user_id, $pivotData);
        }
    }

    public function reject($reviewerId, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
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

    public static function createApplication($teamId, $userId, $message = null)
    {
        return self::create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'message' => $message,
            'applied_at' => now(),
        ]);
    }
}
