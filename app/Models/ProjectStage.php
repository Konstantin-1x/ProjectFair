<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectStage extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'order',
        'started_at',
        'completed_at',
        'deadline',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'deadline' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function startStage()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function completeStage()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancelStage()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->deadline && now()->isAfter($this->deadline) && !$this->isCompleted();
    }
}
