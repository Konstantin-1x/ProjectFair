# 🛠️ Руководство по разработке

## 📋 Настройка среды разработки

### 1. Требования для разработки

#### Обязательные инструменты:
- **PHP 8.2+** с расширениями
- **Composer** для управления зависимостями
- **Node.js 18+** и **npm**
- **Git** для контроля версий
- **IDE/редактор** (VS Code, PhpStorm, Sublime Text)

#### Рекомендуемые инструменты:
- **Laravel Telescope** для отладки
- **Laravel Debugbar** для профилирования
- **PHP CS Fixer** для форматирования кода
- **PHPStan** для статического анализа

### 2. Установка инструментов разработки

```bash
# Установка Laravel Telescope
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Установка Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev

# Установка PHP CS Fixer
composer require friendsofphp/php-cs-fixer --dev

# Установка PHPStan
composer require phpstan/phpstan --dev
```

## 🏗️ Архитектура проекта

### 1. Структура MVC

```
app/
├── Http/
│   ├── Controllers/          # Контроллеры
│   │   ├── AdminController.php
│   │   ├── ProjectController.php
│   │   ├── TeamController.php
│   │   ├── TaskController.php
│   │   └── TeamApplicationController.php
│   ├── Middleware/           # Middleware
│   │   ├── AdminMiddleware.php
│   │   └── SetLocale.php
│   └── Requests/             # Валидация запросов
├── Models/                   # Модели данных
│   ├── Project.php
│   ├── Team.php
│   ├── User.php
│   ├── Task.php
│   ├── Tag.php
│   ├── Technology.php
│   ├── TeamApplication.php
│   └── TeamProjectApplication.php
├── Policies/                 # Политики авторизации
│   └── ProjectPolicy.php
└── Providers/               # Сервис-провайдеры
    ├── AppServiceProvider.php
    └── VoltServiceProvider.php
```

### 2. Паттерны проектирования

#### Repository Pattern (рекомендуется):
```php
// app/Repositories/ProjectRepository.php
class ProjectRepository
{
    public function findByUser($userId)
    {
        return Project::where('created_by', $userId)->get();
    }
    
    public function create(array $data)
    {
        return Project::create($data);
    }
}
```

#### Service Pattern:
```php
// app/Services/ProjectService.php
class ProjectService
{
    protected $projectRepository;
    
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }
    
    public function createProject(array $data, $userId)
    {
        $data['created_by'] = $userId;
        return $this->projectRepository->create($data);
    }
}
```

## 🔧 Стандарты разработки

### 1. PSR-12 стандарт кодирования

#### Настройка PHP CS Fixer:
```json
// .php-cs-fixer.php
<?php

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_scalar' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude('vendor')
            ->exclude('node_modules')
            ->exclude('storage')
            ->exclude('bootstrap/cache')
    );
```

#### Запуск форматирования:
```bash
# Форматирование всех файлов
./vendor/bin/php-cs-fixer fix

# Форматирование конкретного файла
./vendor/bin/php-cs-fixer fix app/Models/Project.php
```

### 2. Типизация PHP 8+

#### Примеры типизации:
```php
// Строгая типизация
declare(strict_types=1);

class ProjectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'team_id' => 'required|exists:teams,id',
        ]);
        
        $project = Project::create($validated);
        
        return redirect()->route('projects.show', $project);
    }
}
```

### 3. Документирование кода

#### PHPDoc стандарт:
```php
/**
 * Создает новый проект
 *
 * @param Request $request HTTP запрос с данными проекта
 * @return RedirectResponse Редирект на страницу проекта
 * @throws ValidationException Если данные не прошли валидацию
 */
public function store(Request $request): RedirectResponse
{
    // ...
}
```

## 🧪 Тестирование

### 1. Настройка тестов

#### Установка PHPUnit:
```bash
composer require phpunit/phpunit --dev
```

#### Конфигурация phpunit.xml:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

### 2. Написание тестов

#### Unit тесты:
```php
// tests/Unit/ProjectTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\User;

class ProjectTest extends TestCase
{
    public function test_project_belongs_to_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $user->id]);
        
        $this->assertEquals($user->id, $project->creator->id);
    }
    
    public function test_project_has_teams()
    {
        $project = Project::factory()->create();
        $team = Team::factory()->create();
        
        $project->teams()->attach($team);
        
        $this->assertTrue($project->teams->contains($team));
    }
}
```

#### Feature тесты:
```php
// tests/Feature/ProjectTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class ProjectTest extends TestCase
{
    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'team_id' => 1,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'created_by' => $user->id,
        ]);
    }
    
    public function test_guest_cannot_create_project()
    {
        $response = $this->post('/projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);
        
        $response->assertRedirect('/login');
    }
}
```

### 3. Запуск тестов

```bash
# Запуск всех тестов
php artisan test

# Запуск конкретного теста
php artisan test tests/Unit/ProjectTest.php

# Запуск с покрытием кода
php artisan test --coverage
```

## 🔍 Отладка и профилирование

### 1. Laravel Telescope

#### Настройка:
```bash
# Установка
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Доступ к интерфейсу
# http://your-app.test/telescope
```

#### Основные функции:
- **Запросы** - просмотр всех HTTP запросов
- **Команды** - выполнение Artisan команд
- **Задачи** - работа с очередями
- **Логи** - системные логи
- **Модели** - работа с Eloquent

### 2. Laravel Debugbar

#### Настройка:
```bash
# Установка
composer require barryvdh/laravel-debugbar --dev

# Публикация конфигурации
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

#### Основные функции:
- **Запросы** - SQL запросы и время выполнения
- **Маршруты** - информация о маршрутах
- **Виджеты** - пользовательские виджеты
- **Логи** - логи приложения

### 3. Отладка в коде

#### Использование dd():
```php
public function store(Request $request)
{
    $data = $request->all();
    dd($data); // Остановка выполнения и вывод данных
    
    // Или
    dump($data); // Вывод данных без остановки
}
```

#### Логирование:
```php
use Illuminate\Support\Facades\Log;

public function store(Request $request)
{
    Log::info('Создание проекта', ['user_id' => auth()->id()]);
    
    try {
        $project = Project::create($request->validated());
        Log::info('Проект создан', ['project_id' => $project->id]);
    } catch (Exception $e) {
        Log::error('Ошибка создания проекта', ['error' => $e->getMessage()]);
        throw $e;
    }
}
```

## 🚀 Оптимизация производительности

### 1. Оптимизация запросов

#### Eager Loading:
```php
// Плохо - N+1 проблема
$projects = Project::all();
foreach ($projects as $project) {
    echo $project->creator->name; // Дополнительный запрос
}

// Хорошо - один запрос
$projects = Project::with('creator')->get();
foreach ($projects as $project) {
    echo $project->creator->name; // Нет дополнительных запросов
}
```

#### Индексы базы данных:
```php
// В миграции
Schema::table('projects', function (Blueprint $table) {
    $table->index('created_by');
    $table->index('status');
    $table->index(['created_by', 'status']);
});
```

### 2. Кэширование

#### Кэширование запросов:
```php
use Illuminate\Support\Facades\Cache;

public function index()
{
    $projects = Cache::remember('projects.index', 3600, function () {
        return Project::with('creator', 'teams')->get();
    });
    
    return view('projects.index', compact('projects'));
}
```

#### Кэширование конфигурации:
```bash
# Кэширование конфигурации
php artisan config:cache

# Кэширование маршрутов
php artisan route:cache

# Кэширование представлений
php artisan view:cache
```

### 3. Оптимизация фронтенда

#### Сжатие ресурсов:
```bash
# Сжатие CSS и JS
npm run production

# Оптимизация изображений
npm install --save-dev imagemin imagemin-pngquant imagemin-mozjpeg
```

## 🔒 Безопасность

### 1. Валидация данных

#### Строгая валидация:
```php
// app/Http/Requests/CreateProjectRequest.php
class CreateProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'description' => 'required|string|min:10',
            'team_id' => 'required|exists:teams,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => 'Название проекта обязательно',
            'name.min' => 'Название должно содержать минимум 3 символа',
            'image.max' => 'Размер изображения не должен превышать 2MB',
        ];
    }
}
```

### 2. Авторизация

#### Политики:
```php
// app/Policies/ProjectPolicy.php
class ProjectPolicy
{
    public function update(User $user, Project $project)
    {
        return $user->id === $project->created_by || $user->isAdmin();
    }
    
    public function delete(User $user, Project $project)
    {
        return $user->id === $project->created_by || $user->isAdmin();
    }
}
```

#### Middleware:
```php
// app/Http/Middleware/AdminMiddleware.php
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }
        
        return $next($request);
    }
}
```

### 3. Защита от атак

#### CSRF защита:
```php
// В формах обязательно
@csrf

// В AJAX запросах
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

#### XSS защита:
```php
// Автоматическая экранирование в Blade
{{ $user->name }}

// Ручное экранирование
{!! $user->name !!} // Осторожно!
```

## 📝 Документирование

### 1. API документация

#### Swagger/OpenAPI:
```bash
# Установка L5-Swagger
composer require darkaonline/l5-swagger

# Публикация конфигурации
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

#### Пример аннотации:
```php
/**
 * @OA\Post(
 *     path="/api/projects",
 *     summary="Создание проекта",
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Новый проект"),
 *             @OA\Property(property="description", type="string", example="Описание проекта")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Проект создан успешно"
 *     )
 * )
 */
public function store(Request $request)
{
    // ...
}
```

### 2. Документирование кода

#### README для модулей:
```markdown
# Модуль управления проектами

## Описание
Модуль для создания, редактирования и управления проектами.

## Основные функции
- Создание проектов
- Редактирование проектов
- Удаление проектов
- Поиск проектов

## API
- `GET /projects` - список проектов
- `POST /projects` - создание проекта
- `PUT /projects/{id}` - обновление проекта
- `DELETE /projects/{id}` - удаление проекта
```

## 🚀 Деплой в разработку

### 1. Настройка окружения разработки

```bash
# Клонирование репозитория
git clone https://github.com/your-username/project-fair.git
cd project-fair

# Установка зависимостей
composer install
npm install

# Настройка окружения
cp .env.example .env
php artisan key:generate

# Настройка базы данных
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Сборка фронтенда
npm run dev
```

### 2. Автоматизация разработки

#### npm скрипты:
```json
{
  "scripts": {
    "dev": "npm run development",
    "development": "mix",
    "watch": "mix watch",
    "watch-poll": "mix watch -- --watch-options-poll=1000",
    "hot": "mix watch --hot",
    "prod": "npm run production",
    "production": "mix --production"
  }
}
```

#### Makefile:
```makefile
# Makefile
.PHONY: install dev test

install:
	composer install
	npm install
	cp .env.example .env
	php artisan key:generate
	touch database/database.sqlite
	php artisan migrate
	php artisan db:seed
	php artisan storage:link

dev:
	php artisan serve &
	npm run watch

test:
	php artisan test

format:
	./vendor/bin/php-cs-fixer fix
```

---

**Удачной разработки! 🚀**
