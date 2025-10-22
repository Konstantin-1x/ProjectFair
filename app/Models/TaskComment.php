<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
        'type',
        'attached_files',
    ];

    protected $casts = [
        'attached_files' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompletion()
    {
        return $this->type === 'completion';
    }

    public function isRejection()
    {
        return $this->type === 'rejection';
    }

    public function isApproval()
    {
        return $this->type === 'approval';
    }
}