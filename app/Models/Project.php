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
        'completion_file',
        'category',
        'subcategory',
        'goals',
        'target_audience',
        'complexity_level',
        'project_deadline',
        'team_join_deadline',
        'task_submission_deadline',
        'is_deadline_extended',
        'deadline_extension_reason',
    ];

    protected $casts = [
        'gallery' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'project_deadline' => 'datetime',
        'team_join_deadline' => 'datetime',
        'individual_join_deadline' => 'datetime',
        'task_submission_deadline' => 'datetime',
        'is_deadline_extended' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'project_team')
            ->withPivot(['joined_at', 'deadline', 'role_description', 'status'])
            ->withTimestamps();
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

    public function completedTasks(): HasMany
    {
        return $this->hasMany(Task::class)->where('status', 'completed');
    }

    public function pendingTasks(): HasMany
    {
        return $this->hasMany(Task::class)->where('status', 'open');
    }

    public function getCategoryOptions()
    {
        return [
            'it' => 'IT/Разработка',
            'business' => 'Бизнес',
            'design' => 'Дизайн',
            'research' => 'Исследования',
            'education' => 'Образование',
            'social' => 'Социальные проекты',
            'other' => 'Другое',
        ];
    }

    public function getComplexityOptions()
    {
        return [
            'easy' => 'Легкий',
            'medium' => 'Средний',
            'hard' => 'Сложный',
            'expert' => 'Экспертный',
        ];
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByComplexity($query, $complexity)
    {
        return $query->where('complexity_level', $complexity);
    }

    public function updateTeamRole($teamId, $role, $notes = null)
    {
        return $this->teams()->updateExistingPivot($teamId, [
            'role' => $role,
            'notes' => $notes,
        ]);
    }

    public function getParticipatingTeams()
    {
        return $this->teams()->wherePivot('role', 'participant');
    }

    public function getLeadingTeams()
    {
        return $this->teams()->wherePivot('role', 'leader');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(ProjectStage::class)->orderBy('order');
    }

    public function completedStages(): HasMany
    {
        return $this->hasMany(ProjectStage::class)->where('status', 'completed')->orderBy('order');
    }

    public function pendingStages(): HasMany
    {
        return $this->hasMany(ProjectStage::class)->where('status', 'pending')->orderBy('order');
    }

    public function inProgressStages(): HasMany
    {
        return $this->hasMany(ProjectStage::class)->where('status', 'in_progress')->orderBy('order');
    }

    public function getProgressPercentage()
    {
        $totalStages = $this->stages()->count();
        if ($totalStages === 0) return 0;
        
        $completedStages = $this->completedStages()->count();
        return round(($completedStages / $totalStages) * 100, 2);
    }

    public function addStage($name, $description = null, $deadline = null, $order = null)
    {
        if ($order === null) {
            $order = $this->stages()->max('order') + 1;
        }

        return $this->stages()->create([
            'name' => $name,
            'description' => $description,
            'deadline' => $deadline,
            'order' => $order,
        ]);
    }

    public function canUserAddTeam($userId, $teamId)
    {
        // Проверяем, является ли пользователь лидером команды
        $team = Team::find($teamId);
        if (!$team || $team->leader_id !== $userId) {
            return false;
        }

        // Проверяем, не добавлена ли уже эта команда
        if ($this->teams()->where('team_id', $teamId)->exists()) {
            return false;
        }

        return true;
    }

    public function getUserTeams($userId)
    {
        return Team::where('leader_id', $userId)->get();
    }

    public function isProjectDeadlinePassed()
    {
        return $this->project_deadline && now()->isAfter($this->project_deadline);
    }

    public function isTaskSubmissionDeadlinePassed()
    {
        return $this->task_submission_deadline && now()->isAfter($this->task_submission_deadline);
    }

    public function canTeamsJoin()
    {
        return !$this->isTeamJoinDeadlinePassed();
    }

    public function extendDeadline($newDeadline, $reason)
    {
        $this->update([
            'project_deadline' => $newDeadline,
            'is_deadline_extended' => true,
            'deadline_extension_reason' => $reason,
        ]);
    }

    public function getDeadlineStatus()
    {
        $status = [];
        
        if ($this->project_deadline) {
            $status['project'] = [
                'deadline' => $this->project_deadline,
                'is_overdue' => $this->isProjectDeadlinePassed(),
                'days_remaining' => now()->diffInDays($this->project_deadline, false),
            ];
        }

        if ($this->team_join_deadline) {
            $status['team_join'] = [
                'deadline' => $this->team_join_deadline,
                'is_overdue' => $this->isTeamJoinDeadlinePassed(),
                'days_remaining' => now()->diffInDays($this->team_join_deadline, false),
            ];
        }

        if ($this->task_submission_deadline) {
            $status['task_submission'] = [
                'deadline' => $this->task_submission_deadline,
                'is_overdue' => $this->isTaskSubmissionDeadlinePassed(),
                'days_remaining' => now()->diffInDays($this->task_submission_deadline, false),
            ];
        }

        return $status;
    }

    public function getTasksProgress()
    {
        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->completedTasks()->count();
        
        if ($totalTasks === 0) {
            return [
                'percentage' => 0,
                'completed' => 0,
                'total' => 0,
                'remaining' => 0
            ];
        }

        return [
            'percentage' => round(($completedTasks / $totalTasks) * 100, 2),
            'completed' => $completedTasks,
            'total' => $totalTasks,
            'remaining' => $totalTasks - $completedTasks
        ];
    }

    public function createBasicTasks()
    {
        // Создаем базовые задачи
        $basicTasks = [
            [
                'title' => 'Набор команды',
                'description' => 'Сформировать команду для работы над проектом',
                'type' => 'Организационная',
                'difficulty' => 'easy',
                'is_basic_task' => true,
                'order' => 1,
            ],
            [
                'title' => 'Завершение проекта',
                'description' => 'Завершить работу над проектом и представить результат',
                'type' => 'Финальная',
                'difficulty' => 'hard',
                'is_basic_task' => true,
                'order' => 999, // Последняя задача
            ]
        ];

        foreach ($basicTasks as $taskData) {
            $this->tasks()->create(array_merge($taskData, [
                'created_by' => $this->created_by,
                'status' => 'open',
                'project_id' => $this->id,
            ]));
        }
    }

    public function addTeam($teamId, $roleDescription = null, $deadline = null)
    {
        return $this->teams()->attach($teamId, [
            'joined_at' => now(),
            'deadline' => $deadline,
            'role_description' => $roleDescription,
            'status' => 'active'
        ]);
    }

    public function removeTeam($teamId)
    {
        return $this->teams()->detach($teamId);
    }

    public function updateTeamStatus($teamId, $status)
    {
        return $this->teams()->updateExistingPivot($teamId, [
            'status' => $status
        ]);
    }

    public function isTeamJoinDeadlinePassed()
    {
        return $this->team_join_deadline && now()->isAfter($this->team_join_deadline);
    }

    public function isIndividualJoinDeadlinePassed()
    {
        return $this->individual_join_deadline && now()->isAfter($this->individual_join_deadline);
    }

    public function teamApplications(): HasMany
    {
        return $this->hasMany(TeamProjectApplication::class);
    }

    public function pendingTeamApplications(): HasMany
    {
        return $this->hasMany(TeamProjectApplication::class)->where('status', 'pending');
    }

    public function approvedTeamApplications(): HasMany
    {
        return $this->hasMany(TeamProjectApplication::class)->where('status', 'approved');
    }

    public function canTeamApply($teamId)
    {
        // Проверяем, не участвует ли команда уже в проекте
        if ($this->teams()->where('team_id', $teamId)->exists()) {
            return false;
        }

        // Проверяем, не подавала ли команда уже заявку
        if ($this->teamApplications()->where('team_id', $teamId)->whereIn('status', ['pending', 'approved'])->exists()) {
            return false;
        }

        // Проверяем, не истек ли срок подачи заявок
        return $this->canTeamsJoin();
    }

    public function hasTeamApplied($teamId)
    {
        return $this->teamApplications()->where('team_id', $teamId)->exists();
    }

    public function getTeamApplication($teamId)
    {
        return $this->teamApplications()->where('team_id', $teamId)->first();
    }

}
