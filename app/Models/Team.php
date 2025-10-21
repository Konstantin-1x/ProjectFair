<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'max_members',
        'leader_id',
        'recruitment_start',
        'recruitment_end',
        'team_creation_deadline',
        'team_formation_deadline',
        'is_team_formation_closed',
    ];

    protected $casts = [
        'recruitment_start' => 'datetime',
        'recruitment_end' => 'datetime',
        'team_creation_deadline' => 'datetime',
        'team_formation_deadline' => 'datetime',
        'is_team_formation_closed' => 'boolean',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'joined_at', 'application_status', 'applied_at', 'approved_at', 'rejection_reason')
            ->withTimestamps();
    }

    

    public function approvedMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'joined_at', 'application_status', 'applied_at', 'approved_at', 'rejection_reason')
            ->wherePivot('application_status', 'approved')
            ->withTimestamps();
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(TeamVacancy::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_team')
            ->withPivot(['joined_at', 'deadline', 'role_description', 'status'])
            ->withTimestamps();
    }

    public function joinedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_team')
            ->withPivot(['joined_at', 'deadline', 'role_description', 'status'])
            ->withTimestamps();
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_team_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TeamApplication::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'team_tag');
    }

    public function pendingApplications(): HasMany
    {
        return $this->hasMany(TeamApplication::class)->where('status', 'pending');
    }

    public function approvedApplications(): HasMany
    {
        return $this->hasMany(TeamApplication::class)->where('status', 'approved');
    }

    public function rejectedApplications(): HasMany
    {
        return $this->hasMany(TeamApplication::class)->where('status', 'rejected');
    }

    public function approveApplication($userId)
    {
        return $this->members()->updateExistingPivot($userId, [
            'application_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function rejectApplication($userId, $reason = null)
    {
        return $this->members()->updateExistingPivot($userId, [
            'application_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function removeMember($userId)
    {
        // Отсоединяем пользователя от команды
        $this->members()->detach($userId);

        // Очищаем заявку пользователя (если есть)
        $this->applications()
            ->where('user_id', $userId)
            ->delete();

        return true;
    }

    public function kickMember($userId, $reason = null)
    {
        // Обновляем статус на "исключен" перед удалением
        $this->members()->updateExistingPivot($userId, [
            'application_status' => 'kicked',
            'rejection_reason' => $reason,
        ]);
        
        // Отсоединяем пользователя от команды
        $this->members()->detach($userId);

        // Очищаем заявку пользователя (если есть)
        $this->applications()
            ->where('user_id', $userId)
            ->delete();

        return true;
    }

    public function excludeMember($userId, $reason = null)
    {
        // Проверяем, что пользователь не является лидером команды
        if ($this->leader_id === $userId) {
            return false;
        }

        // Проверяем, что пользователь состоит в команде
        if (!$this->members->contains($userId)) {
            return false;
        }

        // Обновляем статус в pivot таблице
        $this->members()->updateExistingPivot($userId, [
            'application_status' => 'excluded',
            'rejection_reason' => $reason,
            'excluded_at' => now(),
        ]);

        // Отсоединяем пользователя от команды
        $this->members()->detach($userId);

        // Очищаем заявку пользователя (если есть)
        $this->applications()
            ->where('user_id', $userId)
            ->delete();

        return true;
    }

    public function isTeamCreationDeadlinePassed()
    {
        return $this->team_creation_deadline && now()->isAfter($this->team_creation_deadline);
    }

    public function isTeamFormationDeadlinePassed()
    {
        return $this->team_formation_deadline && now()->isAfter($this->team_formation_deadline);
    }

    public function canAcceptApplications()
    {
        return !$this->is_team_formation_closed && 
               !$this->isTeamFormationDeadlinePassed() && 
               $this->approvedMembers()->count() < $this->max_members;
    }

    public function canUserApply($userId)
    {
        // Проверяем, не является ли пользователь уже лидером команды
        if ($this->leader_id === $userId) {
            return false;
        }

        // Проверяем, не является ли пользователь уже членом команды
        if ($this->members()->where('user_id', $userId)->exists()) {
            return false;
        }

        // Проверяем, не подавал ли пользователь уже заявку
        if ($this->applications()->where('user_id', $userId)->whereIn('status', ['pending', 'approved'])->exists()) {
            return false;
        }

        // Проверяем, может ли команда принимать заявки
        return $this->canAcceptApplications();
    }

    public function hasUserApplied($userId)
    {
        return $this->applications()->where('user_id', $userId)->exists();
    }

    public function getUserApplication($userId)
    {
        return $this->applications()->where('user_id', $userId)->first();
    }

    public function projectApplications(): HasMany
    {
        return $this->hasMany(TeamProjectApplication::class);
    }

    public function pendingProjectApplications(): HasMany
    {
        return $this->hasMany(TeamProjectApplication::class)->where('status', 'pending');
    }

    public function approvedProjectApplications(): HasMany
    {
        return $this->hasMany(TeamProjectApplication::class)->where('status', 'approved');
    }

    public function canApplyToProject($projectId)
    {
        $project = Project::find($projectId);
        if (!$project) {
            return false;
        }

        return $project->canTeamApply($this->id);
    }

    public function hasAppliedToProject($projectId)
    {
        return $this->projectApplications()->where('project_id', $projectId)->exists();
    }

    public function getProjectApplication($projectId)
    {
        return $this->projectApplications()->where('project_id', $projectId)->first();
    }
}
