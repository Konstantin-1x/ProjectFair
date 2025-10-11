<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $tags = Tag::all();
        $technologies = Technology::all();

        $projects = [
            [
                'title' => 'Система управления студенческими проектами',
                'description' => 'Веб-приложение для управления и отслеживания студенческих проектов в университете. Включает в себя модули для создания проектов, назначения задач, отслеживания прогресса и генерации отчетов.',
                'short_description' => 'Веб-приложение для управления студенческими проектами с полным функционалом отслеживания и отчетности.',
                'type' => 'Веб-приложение',
                'institute' => 'ИИТУТ',
                'course' => 4,
                'demo_url' => 'https://demo.example.com',
                'github_url' => 'https://github.com/example/project-management',
                'status' => 'active',
                'tags' => ['Веб-разработка', 'Базы данных'],
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'Bootstrap', 'JavaScript'],
            ],
            [
                'title' => 'Мобильное приложение для изучения языков',
                'description' => 'Интерактивное мобильное приложение для изучения иностранных языков с использованием технологий машинного обучения для персонализации процесса обучения.',
                'short_description' => 'Мобильное приложение с ИИ для персонализированного изучения языков.',
                'type' => 'Мобильное приложение',
                'institute' => 'ИМО',
                'course' => 3,
                'demo_url' => 'https://app.example.com',
                'github_url' => 'https://github.com/example/language-app',
                'status' => 'active',
                'tags' => ['Мобильная разработка', 'ИИ и машинное обучение'],
                'technologies' => ['React Native', 'Python', 'TensorFlow', 'MongoDB'],
            ],
            [
                'title' => 'Система мониторинга IoT устройств',
                'description' => 'Комплексная система для мониторинга и управления IoT устройствами в умном доме. Включает веб-интерфейс, мобильное приложение и серверную часть.',
                'short_description' => 'Система мониторинга и управления IoT устройствами для умного дома.',
                'type' => 'IoT система',
                'institute' => 'ИИТУТ',
                'course' => 4,
                'demo_url' => 'https://iot.example.com',
                'github_url' => 'https://github.com/example/iot-monitoring',
                'status' => 'completed',
                'tags' => ['IoT', 'Веб-разработка', 'Мобильная разработка'],
                'technologies' => ['Node.js', 'React', 'MongoDB', 'Docker', 'AWS'],
            ],
            [
                'title' => 'Игра-платформер на Unity',
                'description' => '2D платформер с элементами головоломки, созданный на движке Unity. Игра включает в себя несколько уровней, систему достижений и мультиплеер.',
                'short_description' => '2D платформер с головоломками и мультиплеером на Unity.',
                'type' => 'Игра',
                'institute' => 'ИППИ',
                'course' => 2,
                'demo_url' => 'https://game.example.com',
                'github_url' => 'https://github.com/example/platformer-game',
                'status' => 'active',
                'tags' => ['Игровая разработка'],
                'technologies' => ['Unity', 'C#', 'JavaScript'],
            ],
            [
                'title' => 'Аналитическая система для торговли',
                'description' => 'Система анализа рыночных данных и прогнозирования трендов для трейдеров. Использует машинное обучение для анализа исторических данных и генерации торговых сигналов.',
                'short_description' => 'Система анализа рынка и прогнозирования для трейдинга с ИИ.',
                'type' => 'Аналитическая система',
                'institute' => 'ИЭиУ',
                'course' => 3,
                'demo_url' => 'https://trading.example.com',
                'github_url' => 'https://github.com/example/trading-analytics',
                'status' => 'active',
                'tags' => ['Анализ данных', 'ИИ и машинное обучение'],
                'technologies' => ['Python', 'Django', 'PostgreSQL', 'TensorFlow', 'Docker'],
            ],
            [
                'title' => 'Блокчейн система голосования',
                'description' => 'Децентрализованная система электронного голосования на блокчейне Ethereum. Обеспечивает прозрачность, безопасность и неизменность результатов голосования.',
                'short_description' => 'Децентрализованная система голосования на блокчейне Ethereum.',
                'type' => 'Блокчейн приложение',
                'institute' => 'ИИТУТ',
                'course' => 4,
                'demo_url' => 'https://voting.example.com',
                'github_url' => 'https://github.com/example/blockchain-voting',
                'status' => 'active',
                'tags' => ['Блокчейн', 'Кибербезопасность'],
                'technologies' => ['Solidity', 'Web3.js', 'React', 'Node.js'],
            ],
        ];

        foreach ($projects as $projectData) {
            $user = $users->random();
            
            $project = Project::create([
                'title' => $projectData['title'],
                'description' => $projectData['description'],
                'short_description' => $projectData['short_description'],
                'type' => $projectData['type'],
                'institute' => $projectData['institute'],
                'course' => $projectData['course'],
                'demo_url' => $projectData['demo_url'],
                'github_url' => $projectData['github_url'],
                'status' => $projectData['status'],
                'created_by' => $user->id,
                'started_at' => now()->subDays(rand(30, 180)),
            ]);

            // Привязываем теги
            $projectTags = $tags->whereIn('name', $projectData['tags']);
            $project->tags()->attach($projectTags->pluck('id'));

            // Привязываем технологии
            $projectTechnologies = $technologies->whereIn('name', $projectData['technologies']);
            $project->technologies()->attach($projectTechnologies->pluck('id'));
        }
    }
}