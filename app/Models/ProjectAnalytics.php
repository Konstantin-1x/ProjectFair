<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAnalytics extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'action',
        'description',
        'metadata',
        'performed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'performed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction($projectId, $userId, $action, $description = null, $metadata = null)
    {
        return self::create([
            'project_id' => $projectId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'performed_at' => now(),
        ]);
    }

    public static function getProjectProgress($projectId)
    {
        $project = Project::find($projectId);
        if (!$project) return null;

        return [
            'project' => $project,
            'total_stages' => $project->stages()->count(),
            'completed_stages' => $project->completedStages()->count(),
            'in_progress_stages' => $project->inProgressStages()->count(),
            'pending_stages' => $project->pendingStages()->count(),
            'progress_percentage' => $project->getProgressPercentage(),
            'teams_count' => $project->teams()->count(),
            'tasks_count' => $project->tasks()->count(),
            'completed_tasks' => $project->completedTasks()->count(),
        ];
    }

    public static function getUserActivity($userId, $projectId = null)
    {
        $query = self::where('user_id', $userId);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query->with(['project', 'user'])
                    ->orderBy('performed_at', 'desc')
                    ->get();
    }

    public static function getProjectActivity($projectId)
    {
        return self::where('project_id', $projectId)
                   ->with(['user'])
                   ->orderBy('performed_at', 'desc')
                   ->get();
    }
}
