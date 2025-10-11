<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем администратора
        User::create([
            'name' => 'Администратор',
            'email' => 'admin@sevgu.ru',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'institute' => 'ИИТУТ',
            'course' => 4,
            'group' => 'ИС-20-1',
            'bio' => 'Системный администратор платформы',
        ]);

        // Создаем тестовых пользователей
        $users = [
            [
                'name' => 'Иван Петров',
                'email' => 'ivan.petrov@student.sevgu.ru',
                'password' => Hash::make('password'),
                'role' => 'user',
                'institute' => 'ИИТУТ',
                'course' => 3,
                'group' => 'ИС-21-1',
                'bio' => 'Студент 3 курса, увлекаюсь веб-разработкой',
            ],
            [
                'name' => 'Мария Сидорова',
                'email' => 'maria.sidorova@student.sevgu.ru',
                'password' => Hash::make('password'),
                'role' => 'user',
                'institute' => 'ИИТУТ',
                'course' => 2,
                'group' => 'ИС-22-1',
                'bio' => 'Студентка 2 курса, изучаю мобильную разработку',
            ],
            [
                'name' => 'Алексей Козлов',
                'email' => 'alexey.kozlov@student.sevgu.ru',
                'password' => Hash::make('password'),
                'role' => 'user',
                'institute' => 'ИМО',
                'course' => 4,
                'group' => 'ПИ-20-1',
                'bio' => 'Студент 4 курса, специализируюсь на ИИ',
            ],
            [
                'name' => 'Елена Волкова',
                'email' => 'elena.volkova@student.sevgu.ru',
                'password' => Hash::make('password'),
                'role' => 'user',
                'institute' => 'ИППИ',
                'course' => 3,
                'group' => 'ПИ-21-1',
                'bio' => 'Студентка 3 курса, интересуюсь кибербезопасностью',
            ],
            [
                'name' => 'Дмитрий Новиков',
                'email' => 'dmitry.novikov@student.sevgu.ru',
                'password' => Hash::make('password'),
                'role' => 'user',
                'institute' => 'ИЭиУ',
                'course' => 2,
                'group' => 'ЭК-22-1',
                'bio' => 'Студент 2 курса, изучаю экономическую информатику',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}