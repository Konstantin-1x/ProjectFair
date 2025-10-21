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
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_basic_task' => 'boolean',
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

    public function files()
    {
        return $this->hasMany(TaskFile::class);
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
}
