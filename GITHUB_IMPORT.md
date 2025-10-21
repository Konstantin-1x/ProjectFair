# 📥 Импорт проекта с GitHub

## 🚀 Быстрый старт

### 1. Клонирование репозитория

```bash
# Клонирование в текущую директорию
git clone https://github.com/your-username/project-fair.git

# Или клонирование в конкретную папку
git clone https://github.com/your-username/project-fair.git my-project

# Переход в директорию проекта
cd project-fair
```

### 2. Автоматическая установка

#### Для Linux/macOS:
```bash
# Сделать скрипт исполняемым
chmod +x install.sh

# Запустить установку
./install.sh
```

#### Для Windows:
```cmd
# Запустить batch файл
install.bat
```

### 3. Ручная установка

Если автоматическая установка не работает, выполните шаги вручную:

```bash
# 1. Установка PHP зависимостей
composer install

# 2. Установка Node.js зависимостей
npm install

# 3. Настройка окружения
cp .env.example .env
php artisan key:generate

# 4. Создание базы данных
touch database/database.sqlite

# 5. Запуск миграций
php artisan migrate

# 6. Заполнение тестовыми данными
php artisan db:seed

# 7. Настройка файлового хранилища
php artisan storage:link

# 8. Сборка фронтенда
npm run dev

# 9. Запуск сервера
php artisan serve
```

## 🔧 Настройка для разработки

### 1. Настройка Git

```bash
# Проверка статуса
git status

# Просмотр веток
git branch -a

# Переключение на ветку разработки (если есть)
git checkout develop

# Создание собственной ветки
git checkout -b feature/my-feature
```

### 2. Настройка IDE

#### VS Code (рекомендуется):
```bash
# Установка расширений
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension ms-vscode.vscode-json
code --install-extension bradlc.vscode-tailwindcss
code --install-extension formulahendry.auto-rename-tag
```

#### PhpStorm:
1. Откройте проект в PhpStorm
2. Настройте интерпретатор PHP
3. Настройте базу данных
4. Включите Laravel плагин

### 3. Настройка отладки

#### Xdebug (для VS Code):
```json
// .vscode/launch.json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            }
        }
    ]
}
```

## 📊 Проверка установки

### 1. Проверка зависимостей

```bash
# Проверка PHP
php -v

# Проверка Composer
composer --version

# Проверка Node.js
node --version
npm --version

# Проверка Git
git --version
```

### 2. Проверка приложения

```bash
# Проверка маршрутов
php artisan route:list

# Проверка конфигурации
php artisan config:show

# Проверка базы данных
php artisan migrate:status
```

### 3. Тестирование функциональности

1. **Откройте браузер**: http://127.0.0.1:8000
2. **Проверьте главную страницу** - должна загрузиться без ошибок
3. **Проверьте регистрацию** - `/register`
4. **Проверьте вход** - `/login`
5. **Войдите как администратор**: `admin@sevgu.ru` / `password`
6. **Проверьте админ-панель** - `/admin`

## 🐛 Решение проблем

### Проблема: "Composer install failed"

```bash
# Очистка кэша Composer
composer clear-cache

# Обновление Composer
composer self-update

# Установка без dev-зависимостей
composer install --no-dev --optimize-autoloader
```

### Проблема: "npm install failed"

```bash
# Очистка кэша npm
npm cache clean --force

# Удаление node_modules
rm -rf node_modules package-lock.json

# Установка заново
npm install

# Если не помогает, попробуйте yarn
npm install -g yarn
yarn install
```

### Проблема: "Permission denied"

```bash
# Linux/macOS
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache

# Windows (запустите от имени администратора)
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

### Проблема: "Database connection failed"

1. **Проверьте .env файл**:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

2. **Проверьте существование файла базы данных**:
```bash
ls -la database/database.sqlite
```

3. **Проверьте права доступа**:
```bash
chmod 664 database/database.sqlite
```

### Проблема: "Storage link not found"

```bash
# Удаление существующей ссылки
rm public/storage

# Создание новой ссылки
php artisan storage:link

# Проверка
ls -la public/storage
```

## 🔄 Обновление проекта

### 1. Получение обновлений

```bash
# Получение последних изменений
git pull origin main

# Установка новых зависимостей
composer install
npm install

# Запуск новых миграций
php artisan migrate

# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. Сброс к исходному состоянию

```bash
# Сброс базы данных
php artisan migrate:fresh --seed

# Очистка всех кэшей
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Пересборка фронтенда
npm run dev
```

## 🚀 Настройка для продакшена

### 1. Подготовка к продакшену

```bash
# Установка без dev-зависимостей
composer install --optimize-autoloader --no-dev

# Сборка фронтенда для продакшена
npm run build

# Кэширование конфигурации
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Настройка веб-сервера

#### Apache (.htaccess уже настроен):
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

#### Nginx:
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

## 📝 Дополнительные настройки

### 1. Настройка почты

В `.env` добавьте:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### 2. Настройка очередей

```bash
# Установка Redis (опционально)
sudo apt install redis-server

# В .env
QUEUE_CONNECTION=redis

# Запуск воркера очередей
php artisan queue:work
```

### 3. Настройка кэширования

```bash
# Установка Redis для кэширования
sudo apt install redis-server

# В .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## ✅ Финальная проверка

После установки проверьте:

1. ✅ **Главная страница** загружается без ошибок
2. ✅ **Регистрация/вход** работает
3. ✅ **Админ-панель** доступна
4. ✅ **Проекты** отображаются
5. ✅ **Команды** работают
6. ✅ **Загрузка файлов** работает
7. ✅ **Поиск и фильтрация** работают

## 🆘 Получение помощи

Если у вас возникли проблемы:

1. **Проверьте логи**: `storage/logs/laravel.log`
2. **Очистите кэш**: `php artisan cache:clear`
3. **Проверьте права доступа**: `chmod -R 755 storage bootstrap/cache`
4. **Переустановите зависимости**: `composer install && npm install`
5. **Создайте Issue** в GitHub репозитории

## 📚 Полезные ссылки

- **Laravel документация**: https://laravel.com/docs
- **Bootstrap документация**: https://getbootstrap.com/docs
- **Git документация**: https://git-scm.com/doc
- **Composer документация**: https://getcomposer.org/doc

---

**Удачной работы с проектом! 🎉**