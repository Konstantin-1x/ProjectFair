<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Веб-разработка', 'color' => '#3B82F6', 'description' => 'Проекты веб-приложений'],
            ['name' => 'Мобильная разработка', 'color' => '#10B981', 'description' => 'Мобильные приложения'],
            ['name' => 'ИИ и машинное обучение', 'color' => '#F59E0B', 'description' => 'Проекты с использованием ИИ'],
            ['name' => 'Игровая разработка', 'color' => '#EF4444', 'description' => 'Игры и игровые приложения'],
            ['name' => 'Базы данных', 'color' => '#8B5CF6', 'description' => 'Работа с базами данных'],
            ['name' => 'Кибербезопасность', 'color' => '#DC2626', 'description' => 'Безопасность и защита данных'],
            ['name' => 'IoT', 'color' => '#059669', 'description' => 'Интернет вещей'],
            ['name' => 'Блокчейн', 'color' => '#7C3AED', 'description' => 'Блокчейн технологии'],
            ['name' => 'Анализ данных', 'color' => '#0891B2', 'description' => 'Анализ и визуализация данных'],
            ['name' => 'DevOps', 'color' => '#EA580C', 'description' => 'DevOps и автоматизация'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}