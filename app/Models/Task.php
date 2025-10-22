<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'requirements',
        'difficulty',
        'status',
        'type',
        'deadline',
        'created_by',
        'project_id',
        'assigned_user_id',
        'assigned_at',
        'completed_at',
        'completion_text',
        'completion_file',
        'is_basic_task',
        'order',
        'is_rejected',
        'rejection_reason',
        'rejected_at',
        'rejected_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_basic_task' => 'boolean',
        'is_rejected' => 'boolean',
        'rejected_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at');
    }

    public function completeTask($completionText = null, $completionFile = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_text' => $completionText,
            'completion_file' => $completionFile,
        ]);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function hasCompletionFile()
    {
        return !is_null($this->completion_file);
    }

    public function hasCompletionText()
    {
        return !is_null($this->completion_text);
    }

    public function isRejected()
    {
        return $this->is_rejected;
    }

    public function reject($reason, $rejectedBy, $attachedFiles = [])
    {
        $this->update([
            'is_rejected' => true,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'rejected_by' => $rejectedBy,
            'status' => 'open', // Возвращаем задачу в статус "открыта"
        ]);

        // Создаем комментарий об отклонении
        $this->comments()->create([
            'user_id' => $rejectedBy,
            'comment' => $reason,
            'type' => 'rejection',
            'attached_files' => $attachedFiles,
        ]);
    }

    public function approve()
    {
        $this->update([
            'is_rejected' => false,
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ]);
    }
}
