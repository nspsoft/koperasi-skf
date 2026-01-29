# ⚡ QUICK START GUIDE

**For:** New Developers  
**Time:** 5 minutes  
**Goal:** Get the app running locally

---

## 🚀 Super Quick Setup (3 Commands)

```bash
# 1. Clone & navigate
git clone https://github.com/your-org/koperasi.git
cd koperasi

# 2. Install dependencies
composer install && npm install && cp .env.example .env && php artisan key:generate

# 3. Setup database & run
# (Edit .env with your DB credentials first!)
php artisan migrate:fresh --seed && php artisan serve
```

**Done! 🎉** Open: http://localhost:8000

---

## 🔑 Default Login

| Role | Email | Password |
|---|---|---|
| **Admin** | admin@koperasi.local | password |
| **Pengurus** | pengurus@koperasi.local | password |
| **Member** | member@koperasi.local | password |

⚠️ **Change passwords after first login!**

---

## 📁 Key Files & Folders

```
koperasi/
├── app/
│   ├── Http/Controllers/       ← Your controllers
│   ├── Models/                 ← Eloquent models
│   └── Policies/               ← Authorization
├── resources/
│   └── views/                  ← Blade templates
├── routes/
│   └── web.php                 ← Routes definition
├── database/
│   ├── migrations/             ← Database schema
│   └── seeders/                ← Demo data
├── .env                        ← Configuration (IMPORTANT!)
└── README.md                   ← Start here
```

---

## 🛠️ Common Commands

### **Development:**
```bash
# Start dev server
php artisan serve

# Watch frontend assets
npm run dev

# Clear all cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear
```

### **Database:**
```bash
# Fresh migration + seed
php artisan migrate:fresh --seed
```

### **Testing:**
```bash
# Run tests
php artisan test
```

---

## 🔧 Environment Setup (.env)

**Minimum required:**
```env
APP_NAME="Koperasi Karyawan SKF"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

## 📚 Next Steps

| Step | Document | Time |
|---|---|---|
| 1️⃣ | [README.md](README.md) - Understand the project | 15 min |
| 2️⃣ | [ARCHITECTURE.md](ARCHITECTURE.md) - Learn structure | 30 min |
| 3️⃣ | [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Explore database | 30 min |
| 4️⃣ | [FEATURES.md](FEATURES.md) - See all features | 45 min |
| 5️⃣ | Start coding! | ∞ |

---

## 🐛 Common Issues

### **"Class not found" error**
```bash
composer dump-autoload
```

### **"SQLSTATE[HY000] [1049] Unknown database"**
```bash
# Create database first
mysql -u root -p
CREATE DATABASE koperasi;
exit;

# Then migrate
php artisan migrate
```

---

## 🆘 Need Help?

- **Full Setup Guide:** [INSTALLATION.md](INSTALLATION.md)
- **All Documentation:** [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)
- **Support:** dev@kopkarskf.com

---

**Happy Coding! 👨‍💻✨**
