<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamVacancy extends Model
{
    protected $table = 'team_vacancy';

    protected $fillable = [
        'team_id',
        'position',
        'description',
        'requirements',
        'status',
        'filled_by',
        'filled_at',
    ];

    protected $casts = [
        'filled_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function filledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by');
    }
}
