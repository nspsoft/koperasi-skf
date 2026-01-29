# 🚀 DEPLOYMENT GUIDE

## 🎯 Production Requirements

| Component | Recommendation |
|---|---|
| **OS** | Ubuntu 22.04 LTS |
| **CPU** | 2 vCPU minimum |
| **RAM** | 4GB minimum |
| **Disk** | 40GB SSD |
| **Web Server** | Nginx |
| **PHP** | 8.2 (FPM) |
| **Database** | MySQL 8.0 Managed / Self-hosted |
| **Domain** | SSL Enabled (Let's Encrypt) |

---

## 📋 Pre-Deployment Checklist

- [ ] `.env` configuration ready for production (`APP_ENV=production`, `APP_DEBUG=false`)
- [ ] Database backup strategy defined
- [ ] Mail server (SMTP) credentials verified
- [ ] Midtrans Production Server Key ready
- [ ] Domain DNS pointed to server IP

---

## 🛠️ Step-by-Step Deployment

### 1. Server Provisioning (Ubuntu)

```bash
# Update System
sudo apt update && sudo apt upgrade -y

# Install Core dependencies
sudo apt install -y nginx git unzip curl

# Install PHP 8.2 & Extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
zip php8.2-xml php8.2-curl php8.2-gd php8.2-bcmath php8.2-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL (if local)
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 2. Application Setup

```bash
# Navigate to web root
cd /var/www

# Clone Repo
sudo git clone https://github.com/your-org/koperasi.git
sudo chown -R www-data:www-data koperasi
sudo chmod -R 775 koperasi/storage

cd koperasi

# Install Production Dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci
sudo -u www-data npm run build

# Setup Environment
sudo cp .env.example .env
sudo nano .env
# -> Edit DB credentials, APP_URL, etc.

# Generate Key & Cache
sudo -u www-data php artisan key:generate
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 3. Database Migration

```bash
sudo -u www-data php artisan migrate --force
```

### 4. Nginx Configuration

Create config: `/etc/nginx/sites-available/koperasi`

```nginx
server {
    listen 80;
    server_name kopkarskf.com;
    root /var/www/koperasi/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/koperasi /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5. SSL Certificate (HTTPS)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d kopkarskf.com
```

### 6. Supervisor Configuration (Queue Workers)

Config: `/etc/supervisor/conf.d/koperasi-worker.conf`

```ini
[program:koperasi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/koperasi/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/koperasi/storage/logs/worker.log
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start koperasi-worker:*
```

---

## 🔄 Updating / Deploying Changes

Use this script for zero-downtime updates:

```bash
#!/bin/bash
cd /var/www/koperasi

# Maintenance mode on
php artisan down

# Pull Changes
git pull origin main

# Update Deps
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Clear & Rebuild Cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrate DB
php artisan migrate --force

# Restart Queue
php artisan queue:restart

# Maintenance mode off
php artisan up
```

---

## 🩺 Post-Deployment Verification

1. Check homepage loads (HTTPS).
2. Attempt login.
3. Check `storage/logs/laravel.log` for any errors.
4. Verify queues are running: `php artisan queue:monitor database`.
