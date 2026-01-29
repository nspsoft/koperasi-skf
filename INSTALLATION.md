# 🛠️ INSTALLATION GUIDE

## 🎯 Prerequisites

Before you begin, ensure your development environment has:

| Requirement | Version | Check Command |
|---|---|---|
| **PHP** | 8.2 or higher | `php -v` |
| **Composer** | 2.x | `composer --version` |
| **Node.js** | 18.x or 20.x | `node -v` |
| **Database** | MySQL 8.0 / MariaDB 10.6 | `mysql --version` |
| **Web Server** | Nginx or Apache | |
| **Git** | Latest | `git --version` |

---

## 🚀 Step-by-Step Installation

### 1. Clone the Repository
```bash
git clone https://github.com/your-org/koperasi.git
cd koperasi
```

### 2. Install Backend Dependencies
```bash
composer install
```

### 3. Install Frontend Dependencies
```bash
npm install
```

### 4. Environment Configuration
Duplicate the example file:
```bash
cp .env.example .env
```

Open `.env` and configure your database:
```env
APP_NAME="Koperasi Karyawan SKF"
APP_URL=http://koperasi.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi_db
DB_USERNAME=root
DB_PASSWORD=

# Optional: Mail configuration for testing
MAIL_MAILER=log
```

### 5. Generate App Key
```bash
php artisan key:generate
```

### 6. Database Setup
Create the database (if not exists) and run migrations with seed data:
```bash
# Create DB (if using terminal mysql client)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS koperasi_db;"

# Run migrations & seeders
php artisan migrate:fresh --seed
```

> **Note:** The seeder will create default users:
> - Admin: `admin@kopkarskf.com` / `password`
> - Member: `member@kopkarskf.com` / `password`

### 7. Storage Linking
Link the public storage folder:
```bash
php artisan storage:link
```

### 8. Build Assets
For development (Hotmr):
```bash
npm run dev
```
For production build:
```bash
npm run build
```

### 9. Run Server
Start the Laravel development server:
```bash
php artisan serve
```
Access at: `http://127.0.0.1:8000`

---

## 🧪 Running Tests

To ensure everything is working correctly:

```bash
php artisan test
```

---

## 🔧 Common Installation Issues

### `Composer install` fails with memory error
Increase memory limit:
```bash
php -d memory_limit=-1 /usr/bin/composer install
```

### Database connection refused
- Check if MySQL service is running.
- Verify credentials in `.env`.
- Ensure DB_HOST is `127.0.0.1` (sometimes `localhost` causes socket issues).

### Permission Denied (Storage)
If you are on Linux/Mac:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Node compatibility
If `npm install` fails, try:
```bash
npm install --legacy-peer-deps
```

---

## 🐳 Docker Setup (Optional)

If you prefer using Laravel Sail (Docker):

```bash
# Start containers
./vendor/bin/sail up -d

# Run artisan commands via sail
./vendor/bin/sail artisan migrate --seed
```
