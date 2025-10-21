# 🚀 Инструкция по деплою проекта

## 📋 Подготовка к деплою

### 1. Подготовка сервера

#### Минимальные требования:
- **RAM**: 2 GB
- **CPU**: 2 ядра
- **Диск**: 20 GB SSD
- **ОС**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+

#### Рекомендуемые требования:
- **RAM**: 4 GB
- **CPU**: 4 ядра
- **Диск**: 50 GB SSD
- **ОС**: Ubuntu 22.04 LTS

### 2. Установка необходимых компонентов

#### Ubuntu/Debian:
```bash
# Обновление системы
sudo apt update && sudo apt upgrade -y

# Установка PHP и расширений
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-sqlite3

# Установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Установка Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Установка Nginx
sudo apt install -y nginx

# Установка MySQL (если используется)
sudo apt install -y mysql-server

# Установка Git
sudo apt install -y git
```

#### CentOS/RHEL:
```bash
# Установка EPEL репозитория
sudo yum install -y epel-release

# Установка PHP и расширений
sudo yum install -y php82 php82-cli php82-fpm php82-mysql php82-xml php82-mbstring php82-curl php82-zip php82-gd

# Установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Установка Node.js
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install -y nodejs

# Установка Nginx
sudo yum install -y nginx

# Установка MySQL
sudo yum install -y mysql-server

# Установка Git
sudo yum install -y git
```

## 🔧 Настройка веб-сервера

### 1. Настройка Nginx

#### Создание конфигурации сайта:
```bash
sudo nano /etc/nginx/sites-available/project-fair
```

#### Содержимое конфигурации:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/project-fair/public;
    
    index index.php index.html;
    
    # Безопасность
    server_tokens off;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Основные настройки
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Обработка PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Безопасность
        fastcgi_hide_header X-Powered-By;
    }
    
    # Статические файлы
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Запрет доступа к системным файлам
    location ~ /\. {
        deny all;
    }
    
    location ~ /(storage|bootstrap/cache) {
        deny all;
    }
    
    # Логи
    access_log /var/log/nginx/project-fair_access.log;
    error_log /var/log/nginx/project-fair_error.log;
}
```

#### Активация сайта:
```bash
# Создание символической ссылки
sudo ln -s /etc/nginx/sites-available/project-fair /etc/nginx/sites-enabled/

# Удаление дефолтного сайта
sudo rm /etc/nginx/sites-enabled/default

# Проверка конфигурации
sudo nginx -t

# Перезапуск Nginx
sudo systemctl restart nginx
sudo systemctl enable nginx
```

### 2. Настройка PHP-FPM

#### Редактирование конфигурации:
```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

#### Основные настройки:
```ini
; Пользователь и группа
user = www-data
group = www-data

; Пул процессов
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35

; Лимиты
request_terminate_timeout = 300
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
```

#### Перезапуск PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl enable php8.2-fpm
```

## 🗄️ Настройка базы данных

### 1. MySQL

#### Создание базы данных:
```sql
CREATE DATABASE project_fair CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'project_fair'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON project_fair.* TO 'project_fair'@'localhost';
FLUSH PRIVILEGES;
```

#### Настройка MySQL:
```bash
sudo mysql_secure_installation
```

### 2. PostgreSQL (альтернатива)

#### Создание базы данных:
```sql
CREATE DATABASE project_fair;
CREATE USER project_fair WITH PASSWORD 'strong_password';
GRANT ALL PRIVILEGES ON DATABASE project_fair TO project_fair;
```

## 📦 Деплой приложения

### 1. Клонирование репозитория

```bash
# Создание директории
sudo mkdir -p /var/www/project-fair
cd /var/www/project-fair

# Клонирование репозитория
sudo git clone https://github.com/your-username/project-fair.git .

# Установка прав доступа
sudo chown -R www-data:www-data /var/www/project-fair
sudo chmod -R 755 /var/www/project-fair
```

### 2. Установка зависимостей

```bash
# PHP зависимости
sudo -u www-data composer install --optimize-autoloader --no-dev

# Node.js зависимости
sudo -u www-data npm install

# Сборка фронтенда
sudo -u www-data npm run build
```

### 3. Настройка окружения

```bash
# Копирование файла конфигурации
sudo cp .env.example .env

# Редактирование конфигурации
sudo nano .env
```

#### Основные настройки .env:
```env
APP_NAME="Витрина студенческих проектов СевГУ"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_fair
DB_USERNAME=project_fair
DB_PASSWORD=strong_password

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### 4. Настройка приложения

```bash
# Генерация ключа приложения
sudo -u www-data php artisan key:generate

# Запуск миграций
sudo -u www-data php artisan migrate --force

# Заполнение тестовыми данными (опционально)
sudo -u www-data php artisan db:seed

# Создание символической ссылки для файлов
sudo -u www-data php artisan storage:link

# Кэширование конфигурации
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 5. Настройка прав доступа

```bash
# Основные права
sudo chown -R www-data:www-data /var/www/project-fair
sudo chmod -R 755 /var/www/project-fair

# Права для записи
sudo chmod -R 775 /var/www/project-fair/storage
sudo chmod -R 775 /var/www/project-fair/bootstrap/cache

# Права для файлов
sudo chmod 644 /var/www/project-fair/.env
sudo chmod 644 /var/www/project-fair/composer.json
sudo chmod 644 /var/www/project-fair/package.json
```

## 🔒 Настройка SSL

### 1. Установка Certbot

```bash
# Ubuntu/Debian
sudo apt install -y certbot python3-certbot-nginx

# CentOS/RHEL
sudo yum install -y certbot python3-certbot-nginx
```

### 2. Получение SSL сертификата

```bash
# Автоматическая настройка
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Ручная настройка
sudo certbot certonly --nginx -d your-domain.com -d www.your-domain.com
```

### 3. Автоматическое обновление

```bash
# Добавление в crontab
sudo crontab -e

# Добавление строки
0 12 * * * /usr/bin/certbot renew --quiet
```

## 🔄 Настройка автоматического деплоя

### 1. Создание скрипта деплоя

```bash
sudo nano /var/www/project-fair/deploy.sh
```

#### Содержимое скрипта:
```bash
#!/bin/bash

# Переход в директорию проекта
cd /var/www/project-fair

# Получение обновлений
sudo -u www-data git pull origin main

# Установка зависимостей
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install
sudo -u www-data npm run build

# Запуск миграций
sudo -u www-data php artisan migrate --force

# Кэширование
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Перезапуск сервисов
sudo systemctl reload nginx
sudo systemctl reload php8.2-fpm

echo "Деплой завершен успешно!"
```

#### Права на выполнение:
```bash
sudo chmod +x /var/www/project-fair/deploy.sh
```

### 2. Настройка Webhook (GitHub)

#### Создание webhook скрипта:
```bash
sudo nano /var/www/project-fair/webhook.php
```

#### Содержимое:
```php
<?php
// Проверка подписи GitHub
$secret = 'your-webhook-secret';
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (!hash_equals('sha256=' . hash_hmac('sha256', $payload, $secret), $signature)) {
    http_response_code(401);
    exit('Unauthorized');
}

// Выполнение деплоя
exec('/var/www/project-fair/deploy.sh 2>&1', $output, $return_code);

if ($return_code === 0) {
    http_response_code(200);
    echo 'Deploy successful';
} else {
    http_response_code(500);
    echo 'Deploy failed: ' . implode("\n", $output);
}
?>
```

## 📊 Мониторинг и логи

### 1. Настройка логирования

#### Nginx логи:
```bash
# Просмотр логов
sudo tail -f /var/log/nginx/project-fair_access.log
sudo tail -f /var/log/nginx/project-fair_error.log
```

#### PHP логи:
```bash
# Просмотр логов
sudo tail -f /var/log/php8.2-fpm.log
```

#### Laravel логи:
```bash
# Просмотр логов приложения
sudo tail -f /var/www/project-fair/storage/logs/laravel.log
```

### 2. Настройка мониторинга

#### Установка htop:
```bash
sudo apt install -y htop
```

#### Мониторинг ресурсов:
```bash
# Использование CPU и памяти
htop

# Использование диска
df -h

# Использование сети
netstat -tulpn
```

## 🔧 Обслуживание

### 1. Регулярные задачи

#### Создание cron задач:
```bash
sudo crontab -e
```

#### Добавление задач:
```bash
# Очистка логов (еженедельно)
0 2 * * 0 find /var/log -name "*.log" -mtime +7 -delete

# Обновление SSL сертификатов (ежедневно)
0 12 * * * /usr/bin/certbot renew --quiet

# Очистка кэша Laravel (ежедневно)
0 3 * * * cd /var/www/project-fair && php artisan cache:clear
```

### 2. Резервное копирование

#### Скрипт резервного копирования:
```bash
sudo nano /var/www/project-fair/backup.sh
```

#### Содержимое:
```bash
#!/bin/bash

BACKUP_DIR="/var/backups/project-fair"
DATE=$(date +%Y%m%d_%H%M%S)

# Создание директории
mkdir -p $BACKUP_DIR

# Резервное копирование базы данных
mysqldump -u project_fair -p'strong_password' project_fair > $BACKUP_DIR/database_$DATE.sql

# Резервное копирование файлов
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/project-fair

# Удаление старых резервных копий (старше 30 дней)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Резервное копирование завершено: $DATE"
```

#### Права на выполнение:
```bash
sudo chmod +x /var/www/project-fair/backup.sh
```

## ✅ Проверка деплоя

### 1. Проверка доступности

```bash
# Проверка HTTP
curl -I http://your-domain.com

# Проверка HTTPS
curl -I https://your-domain.com

# Проверка статуса сервисов
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
```

### 2. Проверка производительности

```bash
# Тест нагрузки
ab -n 1000 -c 10 https://your-domain.com/

# Проверка времени отклика
curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com/
```

### 3. Проверка безопасности

```bash
# Проверка SSL
openssl s_client -connect your-domain.com:443 -servername your-domain.com

# Проверка заголовков безопасности
curl -I https://your-domain.com
```

---

**Поздравляем! Ваш проект успешно развернут! 🎉**
