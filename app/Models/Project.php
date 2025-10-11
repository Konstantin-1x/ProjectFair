<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'short_description',
        'status',
        'type',
        'institute',
        'course',
        'image',
        'gallery',
        'demo_url',
        'github_url',
        'team_id',
        'created_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'gallery' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
