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
        'institute',
        'course',
        'max_team_size',
        'deadline',
        'created_by',
        'assigned_team_id',
        'assigned_at',
        'completed_at',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }
}
