# 🚀 Полная инструкция по установке проекта с GitHub

## 📋 Предварительные требования

### Обязательные компоненты:
- **Git** - https://git-scm.com/downloads
- **PHP 8.2+** - https://www.php.net/downloads.php
- **Composer** - https://getcomposer.org/download/
- **Node.js 18+** - https://nodejs.org/
- **npm** (устанавливается с Node.js)

### Для Windows (рекомендуется):
- **XAMPP** - https://www.apachefriends.org/download.html
- Или **Laragon** - https://laragon.org/download/

### Для Linux:
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mysql php8.2-sqlite3 php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip composer nodejs npm git

# CentOS/RHEL
sudo yum install php82 php82-cli php82-mysql php82-sqlite3 php82-xml php82-mbstring php82-curl php82-zip composer nodejs npm git
```

## 🔧 Пошаговая установка

### Шаг 1: Клонирование репозитория
```bash
# Откройте терминал/командную строку в нужной папке
git clone https://github.com/your-username/project-fair.git
cd project-fair
```

### Шаг 2: Установка PHP зависимостей
```bash
composer install
```

**Если возникли ошибки:**
```bash
# Очистите кэш Composer
composer clear-cache

# Обновите Composer
composer self-update

# Попробуйте снова
composer install --no-dev
```

### Шаг 3: Установка Node.js зависимостей
```bash
npm install
```

**Если возникли ошибки:**
```bash
# Очистите кэш npm
npm cache clean --force

# Удалите node_modules и package-lock.json
rm -rf node_modules package-lock.json

# Установите заново
npm install
```

### Шаг 4: Настройка окружения
```bash
# Копируем файл конфигурации
cp .env.example .env

# Генерируем ключ приложения
php artisan key:generate
```

### Шаг 5: Настройка базы данных

#### Вариант A: SQLite (рекомендуется для разработки)
```bash
# Создаем файл базы данных
touch database/database.sqlite

# Убедитесь что в .env указано:
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

#### Вариант B: MySQL
```bash
# Создайте базу данных в MySQL
mysql -u root -p
CREATE DATABASE project_fair;
exit

# В .env укажите:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=project_fair
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

### Шаг 6: Запуск миграций
```bash
php artisan migrate
```

**Если возникли ошибки:**
```bash
# Сбросьте миграции
php artisan migrate:fresh
```

### Шаг 7: Заполнение тестовыми данными

#### Вариант A: Автоматическое заполнение
```bash
php artisan db:seed
```

#### Вариант B: Ручное заполнение SQL
1. Откройте файл `database_insert_updated.sql`
2. Скопируйте все SQL-запросы
3. Выполните их в вашей СУБД:
   - **SQLite**: `sqlite3 database/database.sqlite < database_insert_updated.sql`
   - **MySQL**: `mysql -u root -p project_fair < database_insert_updated.sql`

### Шаг 8: Настройка файлового хранилища
```bash
php artisan storage:link
```

### Шаг 9: Сборка фронтенда
```bash
# Для разработки
npm run dev

# Для продакшена
npm run build
```

### Шаг 10: Запуск сервера
```bash
php artisan serve
```

Откройте браузер и перейдите по адресу: **http://127.0.0.1:8000**

## 🔍 Проверка установки

### 1. Проверьте главную страницу
- Должна загрузиться без ошибок
- Должны отображаться проекты

### 2. Проверьте регистрацию/вход
- Перейдите на `/register`
- Создайте тестового пользователя
- Войдите в систему

### 3. Проверьте тестовые данные
- Войдите как администратор: `admin@sevgu.ru` / `password`
- Проверьте админ-панель: `/admin`
- Проверьте проекты и команды

## 🐛 Решение проблем

### Проблема: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Проблема: "Storage link not found"
```bash
php artisan storage:link
# На Windows может потребоваться права администратора
```

### Проблема: "Permission denied"
```bash
# Linux/macOS
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (запустите от имени администратора)
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

### Проблема: "Database connection failed"
1. Проверьте настройки в `.env`
2. Убедитесь что база данных создана
3. Проверьте права доступа к файлу SQLite
4. Для MySQL проверьте подключение

### Проблема: "npm install failed"
```bash
# Очистите кэш
npm cache clean --force

# Удалите node_modules
rm -rf node_modules package-lock.json

# Установите заново
npm install

# Если не помогает, попробуйте yarn
npm install -g yarn
yarn install
```

### Проблема: "Composer install failed"
```bash
# Обновите Composer
composer self-update

# Очистите кэш
composer clear-cache

# Установите без dev-зависимостей
composer install --no-dev --optimize-autoloader
```

## 🚀 Настройка для продакшена

### 1. Настройка веб-сервера

#### Apache
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/project-fair/public
    
    <Directory /path/to/project-fair/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/project-fair_error.log
    CustomLog ${APACHE_LOG_DIR}/project-fair_access.log combined
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

### 2. Оптимизация для продакшена
```bash
# Установка без dev-зависимостей
composer install --optimize-autoloader --no-dev

# Сборка фронтенда
npm run build

# Кэширование
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 3. Настройка cron задач
```bash
# Добавьте в crontab:
* * * * * cd /path/to/project-fair && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 Мониторинг

### Логи приложения
```bash
# Просмотр логов
tail -f storage/logs/laravel.log

# Очистка логов
php artisan log:clear
```

### Мониторинг производительности
```bash
# Установка Laravel Telescope (опционально)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## 🔧 Полезные команды

### Очистка кэша
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Сброс базы данных
```bash
# Полный сброс с тестовыми данными
php artisan migrate:fresh --seed
```

### Создание пользователя
```bash
php artisan tinker
>>> User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('password')])
```

### Просмотр маршрутов
```bash
php artisan route:list
```

## 📝 Дополнительные настройки

### Настройка почты (опционально)
В `.env` добавьте:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Настройка очередей (опционально)
```bash
# Установка Redis
sudo apt install redis-server

# В .env
QUEUE_CONNECTION=redis

# Запуск воркера очередей
php artisan queue:work
```

## ✅ Финальная проверка

После установки проверьте:

1. ✅ Главная страница загружается
2. ✅ Регистрация/вход работает
3. ✅ Админ-панель доступна
4. ✅ Проекты отображаются
5. ✅ Команды работают
6. ✅ Загрузка файлов работает
7. ✅ Поиск и фильтрация работают

## 🆘 Получение помощи

Если у вас возникли проблемы:

1. **Проверьте логи**: `storage/logs/laravel.log`
2. **Очистите кэш**: `php artisan cache:clear`
3. **Проверьте права доступа**: `chmod -R 755 storage bootstrap/cache`
4. **Переустановите зависимости**: `composer install && npm install`
5. **Создайте Issue** в GitHub репозитории

---

**Удачной установки! 🎉**