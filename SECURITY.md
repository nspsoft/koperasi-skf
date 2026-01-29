# 🔐 SECURITY DOCUMENTATION

## 🎯 Security Overview

**Application:** Koperasi Karyawan SKF  
**Security Level:** High (Financial Data)

---

## 🔒 1. AUTHENTICATION (RBAC)

**System:** Laravel Sanctum + Spatie Permission

### Role Matrix

| Role | Access Level | Description |
|---|---|---|
| **System Admin** | Full Access | IT Staff / Developer |
| **Pengurus** | High | Ketua, Bendahara, Sekretaris |
| **Manager** | Medium | Pengelola Toko / Operational |
| **Kasir** | Low | POS Only |
| **Anggota** | Self Only | View own data, transactions |

---

## 🛡️ 2. PROTECTION MECHANISMS

- **SSL/TLS:** Enforced HTTPS on all connections.
- **CSRF Token:** Required for all POST/PUT/DELETE requests.
- **XSS Protection:** Blade engine auto-escaping.
- **SQL Injection:** Eloquent ORM Parameter binding.
- **Rate Limiting:** Throttle login attempts (5 per min).

---

## 🔐 3. DATA PRIVACY (GDPR/PDP)

### Sensitive Fields
- **NIK:** Encrypted in database.
- **Password:** Bcrypt Hashed (Cost 10).
- **Phone/Email:** Unique Index.

### Document Storage
- Storage Path: `storage/app/private/documents/`
- Access Control: Middleware `auth` + `can:view_document`.
- Direct access blocked by Nginx.

---

## 🌐 4. NETWORK SECURITY

- **Firewall (UFW):**
  - Allow 80 (HTTP) -> Redirect HTTPS
  - Allow 443 (HTTPS)
  - Allow 22 (SSH) -> Whitelisted IPs only
  - Deny All others

---

## 🔍 5. INCIDENT RESPONSE PLAN

### Severity Levels

1.  **Low:** Failed login spike.
2.  **Medium:** Suspicious anomaly in transactions.
3.  **High:** Data leak / Unauthorized admin access.

### Response Steps

1.  **Identify:** Check Audit Logs (`spatie/laravel-activitylog`).
2.  **Contain:** Enable "Maintenance Mode".
    ```bash
    php artisan down --secret="admin-access-key"
    ```
3.  **Eradicate:** Patch vulnerability, Rotate keys (`APP_KEY`, DB creds).
4.  **Recover:** Restore clean backup if data corrupted.
    ```bash
    php artisan up
    ```

---

## 📝 6. AUDIT LOGGING

**Recorded Events:**
- Login / Logout
- Create / Update / Delete User
- Financial Transactions (Approve/Reject)
- System Configuration Changes

**Viewing Logs:**
Admin Panel -> System -> Audit Logs

---

**Responsible:** Security Team
**Contact:** security@kopkarskf.com
