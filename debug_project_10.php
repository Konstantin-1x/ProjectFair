<?php

require_once 'vendor/autoload.php';

// Загружаем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Project;
use App\Models\Team;

echo "Проверка проекта с ID 10...\n";

$project = Project::find(10);
if ($project) {
    echo "Проект: {$project->title}\n";
    echo "Создатель: {$project->created_by}\n";
    echo "Статус: {$project->status}\n";
    
    // Проверяем команды проекта (старая система)
    echo "\nКоманды проекта (старая система):\n";
    echo "team_id: " . ($project->team_id ?: 'нет') . "\n";
    if ($project->team_id) {
        $oldTeam = Team::find($project->team_id);
        if ($oldTeam) {
            echo "Команда: {$oldTeam->name}\n";
        }
    }
    
    // Проверяем команды проекта (новая система)
    echo "\nКоманды проекта (новая система):\n";
    $newTeams = $project->teams;
    echo "Команд в новой системе: " . $newTeams->count() . "\n";
    foreach ($newTeams as $team) {
        echo "  - {$team->name} (ID: {$team->id}, статус: {$team->pivot->status})\n";
    }
    
    // Проверяем связи в project_team
    echo "\nСвязи в project_team:\n";
    $projectTeamRecords = \Illuminate\Support\Facades\DB::table('project_team')
        ->where('project_id', $project->id)
        ->get();
    echo "Записей в project_team: " . $projectTeamRecords->count() . "\n";
    foreach ($projectTeamRecords as $record) {
        echo "  - team_id: {$record->team_id}, status: {$record->status}\n";
    }
} else {
    echo "Проект с ID 10 не найден\n";
}
