# 🏗️ SYSTEM ARCHITECTURE

## 1. High-Level Architecture

### Architecture Diagram
```mermaid
graph TD
    User[Clients (Web/Mobile)] -->|HTTPS| WebServer[Nginx Web Server]
    WebServer -->|Reverse Proxy| AppServer[Laravel Application]
    
    subgraph "Application Layer"
        AppServer -->|Auth| Sanctum[Sanctum Guard]
        AppServer -->|Cache| Cache[Redis/File Cache]
        AppServer -->|Queue| Workers[Supervisor Workers]
    end
    
    subgraph "Data Layer"
        AppServer -->|ORM| DB[(MySQL Database)]
        AppServer -->|Storage| Files[Local/S3 Storage]
    end
    
    subgraph "External Services"
        AppServer -->|API| Midtrans[Midtrans Payment]
        AppServer -->|API| WhatsApp[Fonnte/Twilio]
        AppServer -->|SMTP| MailServer[SMTP Server]
    end
```

### Design Pattern
- **MVC (Model-View-Controller):** Standard Laravel architecture.
- **Monolithic Modular:** Single repo, but logically separated modules (Finance, Mart, Member).
- **Service Repository Pattern:** Used for complex business logic (e.g., Loan calculations, SHU distribution) to keep Controllers thin.

---

## 2. Technology Stack

### Backend
- **Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0 (Innodb engine)
- **Web Server:** Nginx (latest stable)
- **Queue Driver:** Database / Redis (Production)

### Frontend
- **Template Engine:** Blade
- **CSS Framework:** Tailwind CSS 3.x
- **Interaction:** Alpine.js (Lightweight JS framework), Livewire (optional for dynamic forms)
- **Assets Build:** Vite

### Infrastructure (Production)
- **OS:** Ubuntu 22.04 LTS
- **Process Manager:** Supervisor (for queues)
- **Scheduling:** Cron
- **SSL:** Let's Encrypt (Certbot)

---

## 3. Module Details

### 3.1 Authentication & Security (`App\Models\User`)
- **Authentication:** Laravel Sanctum (Session based for web, Token for API).
- **Authorization:** Laravel Policies & Gates.
- **Roles:** Spatie Laravel Permission (or simplified custom middleware).
- **Password:** Bcrypt hashing.
- **Data Protection:** Encrypted fields for NIK/KTP.

### 3.2 Financial Module (`App\Models\Finance\*`)
- **Ledger System:** Double-entry bookkeeping principle.
- **Transactions:** Polymorphic relations (`transactionable`) to link Journal Entries to source documents (Loan, Savings, Sale).
- **Calculations:** Using `bcmath` or high-precision decimal types for currency.

### 3.3 POS Module (`App\Models\Mart\*`)
- **Inventory:** FIFO/Average costing method support.
- **Transactions:** Atomic application of stock reduction and sales recording to prevent race conditions.
- **Offline Capable:** (Future roadmap) PWA with local storage sync.

---

## 4. Key Design Decisions

### A. Why Monolithic?
**Decision:** Start with Monolithic instead of Microservices.
**Reasoning:** 
1.  Lower complexity for development & deployment.
2.  Strong consistency (ACID) required for financial transactions (Simpan Pinjam).
3.  Simpler hosting requirements (VPS) vs K8s cluster.

### B. Database Schema
**Decision:** Normalized Schema (3NF).
**Reasoning:** Data integrity is paramount for financial records. Avoid redundancy.
**Exception:** Reporting tables / Materialized views for heavy aggregation queries (e.g., SHU Dashboard).

### C. PDF Generation
**Decision:** DomPDF / Snappy.
**Reasoning:** Server-side generation for consistent layout of official documents (Kuitansi, Surat Perjanjian).

---

## 5. Security Architecture

1.  **Network Level:**
    -   Cloudflare / Firewall (UFW) restricting ports 80/443/22.
    -   Database access restricted to localhost.

2.  **App Level:**
    -   XSS Protection (Blade escaping).
    -   CSRF Protection (Global middleware).
    -   SQL Injection (Eloquent ORM bindings).
    -   Rate Limiting (Throttle middleware).

3.  **Data Level:**
    -   Daily automated backups.
    -   Soft Deletes for critical records.

---

## 6. Scalability Roadmap

- **Phase 1 (Current):** Single VPS, Vertical Scaling (Upgrade RAM/CPU).
- **Phase 2:** Separate Database Server (Managed DB).
- **Phase 3:** Load Balancer + Multiple App Servers + Redis Session Store.
- **Phase 4:** Separate "Mart/Store" module into microservice if traffic spikes independently.
