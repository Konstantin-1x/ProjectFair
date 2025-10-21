<?php

require_once 'vendor/autoload.php';

// Загружаем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Team;
use App\Models\Project;
use App\Models\User;

echo "Проверка команды с ID 5...\n";

$team = Team::find(5);
if ($team) {
    echo "Команда: {$team->name}\n";
    echo "Лидер: {$team->leader_id}\n";
    echo "Статус: {$team->status}\n";
    echo "Участников: " . $team->members->count() . "\n";
    
    // Проверяем проекты команды
    echo "\nПроекты команды (старая система):\n";
    $oldProjects = Project::where('team_id', $team->id)->get();
    echo "Проектов в старой системе: " . $oldProjects->count() . "\n";
    foreach ($oldProjects as $project) {
        echo "  - {$project->title} (ID: {$project->id})\n";
    }
    
    // Проверяем проекты команды (новая система)
    echo "\nПроекты команды (новая система):\n";
    $newProjects = $team->projects;
    echo "Проектов в новой системе: " . $newProjects->count() . "\n";
    foreach ($newProjects as $project) {
        echo "  - {$project->title} (ID: {$project->id})\n";
    }
    
    // Проверяем связи в project_team
    echo "\nСвязи в project_team:\n";
    $projectTeamRecords = \Illuminate\Support\Facades\DB::table('project_team')
        ->where('team_id', $team->id)
        ->get();
    echo "Записей в project_team: " . $projectTeamRecords->count() . "\n";
    foreach ($projectTeamRecords as $record) {
        echo "  - project_id: {$record->project_id}, status: {$record->status}\n";
    }
} else {
    echo "Команда с ID 5 не найдена\n";
}

echo "\nПроверка пользователя с ID 10...\n";
$user = User::find(10);
if ($user) {
    echo "Пользователь: {$user->name}\n";
    echo "Команд где лидер: " . $user->ledTeams->count() . "\n";
    echo "Проектов созданных: " . $user->projects->count() . "\n";
} else {
    echo "Пользователь с ID 10 не найден\n";
}
