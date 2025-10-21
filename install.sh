#!/bin/bash

# 🚀 Автоматическая установка проекта "Витрина студенческих проектов СевГУ"
# Для Linux/macOS

echo "🚀 Начинаем установку проекта..."

# Проверка наличия необходимых инструментов
echo "📋 Проверяем наличие необходимых инструментов..."

if ! command -v php &> /dev/null; then
    echo "❌ PHP не найден. Установите PHP 8.2+"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Composer не найден. Установите Composer"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "❌ npm не найден. Установите Node.js и npm"
    exit 1
fi

echo "✅ Все необходимые инструменты найдены"

# Установка PHP зависимостей
echo "📦 Устанавливаем PHP зависимости..."
composer install

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при установке PHP зависимостей"
    exit 1
fi

echo "✅ PHP зависимости установлены"

# Установка Node.js зависимостей
echo "📦 Устанавливаем Node.js зависимости..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при установке Node.js зависимостей"
    exit 1
fi

echo "✅ Node.js зависимости установлены"

# Настройка окружения
echo "⚙️ Настраиваем окружение..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Файл .env создан"
else
    echo "⚠️ Файл .env уже существует"
fi

# Генерация ключа приложения
echo "🔑 Генерируем ключ приложения..."
php artisan key:generate

# Создание базы данных SQLite
echo "🗄️ Создаем базу данных..."
touch database/database.sqlite

# Запуск миграций
echo "🔄 Запускаем миграции..."
php artisan migrate

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при выполнении миграций"
    exit 1
fi

echo "✅ Миграции выполнены"

# Заполнение тестовыми данными
echo "🌱 Заполняем тестовыми данными..."
php artisan db:seed

if [ $? -ne 0 ]; then
    echo "⚠️ Ошибка при заполнении тестовыми данными, но продолжаем..."
fi

# Настройка файлового хранилища
echo "📁 Настраиваем файловое хранилище..."
php artisan storage:link

# Сборка фронтенда
echo "🎨 Собираем фронтенд..."
npm run dev

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при сборке фронтенда"
    exit 1
fi

# Настройка прав доступа
echo "🔐 Настраиваем права доступа..."
chmod -R 755 storage bootstrap/cache

echo ""
echo "🎉 Установка завершена!"
echo ""
echo "📋 Следующие шаги:"
echo "1. Запустите сервер: php artisan serve"
echo "2. Откройте браузер: http://127.0.0.1:8000"
echo "3. Войдите как администратор: admin@sevgu.ru / password"
echo ""
echo "📚 Дополнительная информация:"
echo "- README.md - общая информация о проекте"
echo "- SETUP.md - подробные инструкции по установке"
echo ""
echo "🚀 Удачной работы!"
