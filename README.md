# Витрина студенческих проектов СевГУ

Веб-платформа для публикации, поиска и совместной работы над студенческими проектами Севастопольского государственного университета.

## 🚀 Быстрый старт

### Требования к системе
- **PHP 8.2+** 
- **Composer** (менеджер зависимостей PHP)
- **Node.js 18+** и **npm**
- **Git**

### Для Windows:
- **XAMPP** (рекомендуется) - https://www.apachefriends.org/
- Или **Laragon** - https://laragon.org/

### Для Linux/macOS:
- **LAMP/LEMP** стек
- **SQLite3** (встроен в PHP)

## 📦 Установка с нуля

### 1. Клонирование репозитория
```bash
git clone https://github.com/your-username/project-fair.git
cd project-fair
```

### 2. Установка зависимостей PHP
```bash
composer install
```

### 3. Установка зависимостей Node.js
```bash
npm install
```

### 4. Настройка окружения
```bash
# Копируем файл конфигурации
cp .env.example .env

# Генерируем ключ приложения
php artisan key:generate
```

### 5. Настройка базы данных

#### Вариант A: SQLite (рекомендуется для разработки)
```bash
# Создаем файл базы данных
touch database/database.sqlite

# В файле .env убедитесь что указано:
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

#### Вариант B: MySQL/PostgreSQL
```bash
# Создайте базу данных в вашей СУБД
# Затем в .env укажите:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=project_fair
# DB_USERNAME=root
# DB_PASSWORD=
```

### 6. Запуск миграций
```bash
php artisan migrate
```

### 7. Заполнение тестовыми данными
```bash
# Вариант A: Использование сидеров
php artisan db:seed

# Вариант B: Выполнение SQL-файла
# Откройте database_insert_updated.sql в вашем SQL-клиенте и выполните все запросы
```

### 8. Настройка файлового хранилища
```bash
# Создаем символическую ссылку для доступа к файлам
php artisan storage:link
```

### 9. Сборка фронтенда
```bash
# Для разработки
npm run dev

# Для продакшена
npm run build
```

### 10. Запуск сервера разработки
```bash
php artisan serve
```

Приложение будет доступно по адресу: http://127.0.0.1:8000

## 🔧 Настройка для продакшена

### 1. Настройка веб-сервера

#### Apache (.htaccess уже включен)
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/project-fair/public
    
    <Directory /path/to/project-fair/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/project-fair/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 2. Настройка окружения для продакшена
```bash
# Установка зависимостей без dev-пакетов
composer install --optimize-autoloader --no-dev

# Сборка фронтенда для продакшена
npm run build

# Кэширование конфигурации
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🗄️ База данных

### Структура основных таблиц:
- **users** - пользователи системы
- **projects** - проекты
- **teams** - команды
- **tasks** - задачи
- **tags** - теги проектов
- **technologies** - технологии
- **project_team** - связь проектов и команд
- **team_user** - связь команд и пользователей
- **team_applications** - заявки на вступление в команды
- **team_project_applications** - заявки команд на проекты

### Тестовые данные включают:
- 9 пользователей (включая администратора)
- 9 проектов различных типов
- 4 команды с участниками
- Теги и технологии
- Заявки и связи между сущностями

## 👥 Тестовые аккаунты

### Администратор
- **Email**: admin@sevgu.ru
- **Пароль**: password
- **Права**: полный доступ ко всем функциям

### Обычные пользователи
- **Email**: test@test.ru
- **Пароль**: password
- **Роль**: пользователь с командами

### Другие тестовые пользователи
- ivan.petrov@student.sevgu.ru
- maria.sidorova@student.sevgu.ru
- alexey.kozlov@student.sevgu.ru
- elena.volkova@student.sevgu.ru
- dmitry.novikov@student.sevgu.ru

**Все пароли**: `password`

## 🎯 Основная функциональность

### Для пользователей:
- ✅ **Создание и управление проектами**
- ✅ **Создание и управление командами**
- ✅ **Система заявок** (вступление в команды и проекты)
- ✅ **Управление задачами проектов**
- ✅ **Загрузка файлов** (изображения, отчеты, документы)
- ✅ **Поиск и фильтрация** проектов и команд
- ✅ **Профили пользователей** с портфолио

### Для администраторов:
- ✅ **Полная аналитика** системы
- ✅ **Управление пользователями**
- ✅ **Модерация контента**
- ✅ **Статистика по институтам и технологиям**
- ✅ **Управление всеми проектами и командами**

## 🛠️ Разработка

### Структура проекта
```
app/
├── Http/Controllers/           # Контроллеры
│   ├── AdminController.php    # Админ-панель
│   ├── ProjectController.php  # Управление проектами
│   ├── TeamController.php     # Управление командами
│   ├── TaskController.php     # Управление задачами
│   └── TeamApplicationController.php # Заявки команд
├── Models/                    # Модели данных
│   ├── Project.php
│   ├── Team.php
│   ├── User.php
│   ├── Task.php
│   └── TeamApplication.php
└── Policies/                  # Политики авторизации

resources/
├── views/                     # Blade шаблоны
│   ├── layouts/
│   ├── projects/
│   ├── teams/
│   └── admin/
└── lang/                     # Локализация

database/
├── migrations/               # Миграции БД
└── seeders/                  # Сидеры данных
```

### Добавление новой функциональности

1. **Создайте миграцию**:
```bash
php artisan make:migration create_new_table
```

2. **Создайте модель**:
```bash
php artisan make:model NewModel
```

3. **Создайте контроллер**:
```bash
php artisan make:controller NewController
```

4. **Добавьте маршруты** в `routes/web.php`

5. **Создайте представления** в `resources/views/`

6. **Добавьте тесты**:
```bash
php artisan make:test NewFeatureTest
```

### Полезные команды

```bash
# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Сброс базы данных
php artisan migrate:fresh --seed

# Создание пользователя
php artisan tinker
>>> User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('password')])

# Просмотр маршрутов
php artisan route:list

# Запуск очередей (если используются)
php artisan queue:work
```

## 🔒 Безопасность

- **CSRF защита** на всех формах
- **Валидация данных** на всех входах
- **Авторизация** через Policies
- **Middleware** для проверки ролей
- **Хеширование паролей** через bcrypt
- **Защита файлов** через Laravel Storage

## 📊 Мониторинг и логи

### Логи приложения
```bash
tail -f storage/logs/laravel.log
```

### Мониторинг производительности
- Используйте Laravel Telescope для отладки
- Настройте Laravel Horizon для очередей
- Мониторинг через Laravel Debugbar

## 🚀 Деплой

### 1. Подготовка сервера
```bash
# Установка зависимостей
composer install --optimize-autoloader --no-dev
npm run build

# Настройка прав доступа
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 2. Настройка окружения
```bash
# Копирование .env для продакшена
cp .env.example .env

# Генерация ключа
php artisan key:generate

# Запуск миграций
php artisan migrate --force

# Кэширование
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Настройка cron задач
```bash
# Добавьте в crontab:
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 🐛 Решение проблем

### Частые проблемы:

1. **Ошибка "Class not found"**
```bash
composer dump-autoload
```

2. **Ошибка "Storage link not found"**
```bash
php artisan storage:link
```

3. **Ошибка "Permission denied"**
```bash
chmod -R 755 storage bootstrap/cache
```

4. **Ошибка "Database connection"**
- Проверьте настройки в `.env`
- Убедитесь что база данных создана
- Проверьте права доступа к файлу SQLite

## 📝 Лицензия

Этот проект создан для образовательных целей Севастопольского государственного университета.

## 🤝 Вклад в проект

1. Форкните репозиторий
2. Создайте ветку для новой функции
3. Внесите изменения
4. Создайте Pull Request

## 📞 Поддержка

Для вопросов и предложений:
- Создайте Issue в GitHub
- Обратитесь к администраторам системы
- Email: support@sevgu.ru

---

**Удачной разработки! 🚀**