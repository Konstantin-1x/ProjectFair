@echo off
chcp 65001 >nul
echo 🚀 Автоматическая установка проекта "Витрина студенческих проектов СевГУ"
echo Для Windows
echo.

echo 📋 Проверяем наличие необходимых инструментов...

:: Проверка PHP
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP не найден. Установите PHP 8.2+ или XAMPP
    pause
    exit /b 1
)

:: Проверка Composer
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Composer не найден. Установите Composer
    pause
    exit /b 1
)

:: Проверка npm
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ npm не найден. Установите Node.js и npm
    pause
    exit /b 1
)

echo ✅ Все необходимые инструменты найдены
echo.

:: Установка PHP зависимостей
echo 📦 Устанавливаем PHP зависимости...
composer install
if %errorlevel% neq 0 (
    echo ❌ Ошибка при установке PHP зависимостей
    pause
    exit /b 1
)
echo ✅ PHP зависимости установлены
echo.

:: Установка Node.js зависимостей
echo 📦 Устанавливаем Node.js зависимости...
npm install
if %errorlevel% neq 0 (
    echo ❌ Ошибка при установке Node.js зависимостей
    pause
    exit /b 1
)
echo ✅ Node.js зависимости установлены
echo.

:: Настройка окружения
echo ⚙️ Настраиваем окружение...
if not exist .env (
    copy .env.example .env
    echo ✅ Файл .env создан
) else (
    echo ⚠️ Файл .env уже существует
)
echo.

:: Генерация ключа приложения
echo 🔑 Генерируем ключ приложения...
php artisan key:generate
echo.

:: Создание базы данных SQLite
echo 🗄️ Создаем базу данных...
if not exist database\database.sqlite (
    type nul > database\database.sqlite
    echo ✅ База данных SQLite создана
) else (
    echo ⚠️ База данных уже существует
)
echo.

:: Запуск миграций
echo 🔄 Запускаем миграции...
php artisan migrate
if %errorlevel% neq 0 (
    echo ❌ Ошибка при выполнении миграций
    pause
    exit /b 1
)
echo ✅ Миграции выполнены
echo.

:: Заполнение тестовыми данными
echo 🌱 Заполняем тестовыми данными...
php artisan db:seed
if %errorlevel% neq 0 (
    echo ⚠️ Ошибка при заполнении тестовыми данными, но продолжаем...
)
echo.

:: Настройка файлового хранилища
echo 📁 Настраиваем файловое хранилище...
php artisan storage:link
echo.

:: Сборка фронтенда
echo 🎨 Собираем фронтенд...
npm run dev
if %errorlevel% neq 0 (
    echo ❌ Ошибка при сборке фронтенда
    pause
    exit /b 1
)
echo ✅ Фронтенд собран
echo.

:: Настройка прав доступа (для Windows)
echo 🔐 Настраиваем права доступа...
icacls storage /grant Everyone:F /T >nul 2>&1
icacls bootstrap\cache /grant Everyone:F /T >nul 2>&1
echo.

echo 🎉 Установка завершена!
echo.
echo 📋 Следующие шаги:
echo 1. Запустите сервер: php artisan serve
echo 2. Откройте браузер: http://127.0.0.1:8000
echo 3. Войдите как администратор: admin@sevgu.ru / password
echo.
echo 📚 Дополнительная информация:
echo - README.md - общая информация о проекте
echo - SETUP.md - подробные инструкции по установке
echo.
echo 🚀 Удачной работы!
echo.
pause
