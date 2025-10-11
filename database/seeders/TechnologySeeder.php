<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            // Frontend
            ['name' => 'React', 'category' => 'frontend', 'icon' => 'fab fa-react', 'description' => 'JavaScript библиотека для UI'],
            ['name' => 'Vue.js', 'category' => 'frontend', 'icon' => 'fab fa-vuejs', 'description' => 'Прогрессивный JavaScript фреймворк'],
            ['name' => 'Angular', 'category' => 'frontend', 'icon' => 'fab fa-angular', 'description' => 'Платформа для веб-приложений'],
            ['name' => 'HTML/CSS', 'category' => 'frontend', 'icon' => 'fab fa-html5', 'description' => 'Основы веб-разработки'],
            ['name' => 'JavaScript', 'category' => 'frontend', 'icon' => 'fab fa-js', 'description' => 'Язык программирования'],
            ['name' => 'TypeScript', 'category' => 'frontend', 'icon' => 'fab fa-js', 'description' => 'Типизированный JavaScript'],
            
            // Backend
            ['name' => 'PHP', 'category' => 'backend', 'icon' => 'fab fa-php', 'description' => 'Серверный язык программирования'],
            ['name' => 'Laravel', 'category' => 'backend', 'icon' => 'fab fa-laravel', 'description' => 'PHP фреймворк'],
            ['name' => 'Node.js', 'category' => 'backend', 'icon' => 'fab fa-node-js', 'description' => 'JavaScript runtime'],
            ['name' => 'Python', 'category' => 'backend', 'icon' => 'fab fa-python', 'description' => 'Универсальный язык программирования'],
            ['name' => 'Django', 'category' => 'backend', 'icon' => 'fab fa-python', 'description' => 'Python веб-фреймворк'],
            ['name' => 'Express.js', 'category' => 'backend', 'icon' => 'fab fa-node-js', 'description' => 'Node.js фреймворк'],
            
            // Database
            ['name' => 'MySQL', 'category' => 'database', 'icon' => 'fas fa-database', 'description' => 'Реляционная СУБД'],
            ['name' => 'PostgreSQL', 'category' => 'database', 'icon' => 'fas fa-database', 'description' => 'Объектно-реляционная СУБД'],
            ['name' => 'MongoDB', 'category' => 'database', 'icon' => 'fas fa-database', 'description' => 'NoSQL база данных'],
            ['name' => 'Redis', 'category' => 'database', 'icon' => 'fas fa-database', 'description' => 'In-memory хранилище данных'],
            
            // Mobile
            ['name' => 'React Native', 'category' => 'mobile', 'icon' => 'fab fa-react', 'description' => 'Кроссплатформенная мобильная разработка'],
            ['name' => 'Flutter', 'category' => 'mobile', 'icon' => 'fab fa-android', 'description' => 'UI toolkit от Google'],
            ['name' => 'Swift', 'category' => 'mobile', 'icon' => 'fab fa-apple', 'description' => 'Язык для iOS разработки'],
            ['name' => 'Kotlin', 'category' => 'mobile', 'icon' => 'fab fa-android', 'description' => 'Язык для Android разработки'],
            
            // DevOps
            ['name' => 'Docker', 'category' => 'devops', 'icon' => 'fab fa-docker', 'description' => 'Контейнеризация приложений'],
            ['name' => 'Kubernetes', 'category' => 'devops', 'icon' => 'fab fa-docker', 'description' => 'Оркестрация контейнеров'],
            ['name' => 'AWS', 'category' => 'devops', 'icon' => 'fab fa-aws', 'description' => 'Облачная платформа Amazon'],
            ['name' => 'Git', 'category' => 'devops', 'icon' => 'fab fa-git-alt', 'description' => 'Система контроля версий'],
        ];

        foreach ($technologies as $tech) {
            Technology::create($tech);
        }
    }
}