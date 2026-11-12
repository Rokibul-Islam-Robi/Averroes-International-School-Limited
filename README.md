# Averroes International School - Dynamic PHP & MySQL CMS

A full-stack, enterprise-grade dynamic PHP & MySQL web application and Content Management System (CMS) for Averroes International School. Built following modern PHP standards, strict typing, PDO database connection pooling, robust security guards, and modular architecture.

---

## 📁 Project Directory Structure (Ideal Architecture)

```text
averroes/
├── .env                        # Local environment configuration (DB credentials, debug)
├── .env.example                # Environment template for deployment
├── .gitignore                  # Git ignore rules (.env, vendor, OS caches)
├── .htaccess                   # Root server hardening (disable dir indexes, block sensitive files)
├── composer.json               # Modern dependency manager & PSR-4 autoloading definitions
├── vercel.json                 # Vercel Serverless PHP Deployment Configuration
├── index.php                   # Public dynamic homepage entry point
├── newsletter.php              # AJAX / POST endpoint for newsletter subscriptions
├── README.md                   # Full documentation & setup guide
│
├── admin/                      # CMS Dashboard & Administration Suite
│   ├── css/
│   │   └── admin.css           # Modern CMS styling (responsive dashboard layout)
│   ├── includes/
│   │   ├── auth.php            # Session security & admin authentication guard
│   │   ├── footer.php          # Admin footer & script initialization
│   │   ├── header.php          # Admin header, topbar, user profile pill
│   │   └── sidebar.php         # Admin navigation sidebar
│   ├── js/
│   │   └── admin.js            # Live image preview, modal triggers, confirmation
│   ├── campuses.php            # Campuses & contact locations CRUD
│   ├── index.php               # Admin overview metrics & recent subscribers
│   ├── login.php               # Secure login screen with CSRF verification
│   ├── logout.php              # Session destruction & safe exit
│   ├── navigation.php          # Navbar menu, CTA buttons, footer links CRUD
│   ├── partners.php            # Accreditation & partners strip CRUD
│   ├── profile.php             # Admin credentials & password update
│   ├── programs.php            # "First Time in Bangladesh" slider cards CRUD
│   ├── sections.php            # "Why Averroes" & section headlines management
│   ├── settings.php            # Global site settings, logos, hero video, meta
│   ├── stats.php               # Animated counter indicators CRUD
│   ├── subscribers.php         # Newsletter subscribers management & CSV export
│   └── topbar.php              # Top contact bar (emails & hotlines) CRUD
│
├── assets/                     # Frontend Assets & Media
│   ├── css/
│   │   └── style.css           # Original 100% pixel-perfect stylesheet
│   ├── img/                    # Original SVGs, images, and logos
│   ├── js/
│   │   └── main.js             # Mobile nav, slider, stat counters, newsletter AJAX
│   └── uploads/                # User-uploaded CMS media
│       └── .htaccess           # Security: strictly prevents PHP script execution in uploads
│
├── config/                     # Configuration & Environment Bootstrap
│   ├── .htaccess               # Security: prevents direct HTTP access to configs
│   ├── config.php              # Dynamic URL resolution, .env parser, session security
│   └── db.php                  # Secure PDO singleton database connection
│
├── database/                   # Database Schemas & Migrations
│   ├── .htaccess               # Security: prevents direct HTTP downloading of SQL files
│   └── averroes-ltd.sql        # Complete standalone MySQL database dump (DDL + Live Data)
│
├── scripts/                    # CLI Maintenance & Automation Utilities
│   ├── .htaccess               # Security: prevents direct HTTP access to scripts
│   ├── export_db.php           # CLI database exporter (`php scripts/export_db.php`)
│   └── import_db.php           # CLI database importer (`php scripts/import_db.php`)
│
└── includes/                   # Shared Core Business Logic & Layout Partials
    ├── .htaccess               # Security: prevents direct HTTP access to internal logic
    ├── footer.php              # Dynamic footer, campuses, newsletter form, scripts
    ├── functions.php           # Master functions gateway & autoloader
    ├── header.php              # Dynamic HTML head, topbar, navbar, action buttons
    ├── helpers/                # Modular Helper Utilities
    │   ├── format.php          # Video embed URL parser & string formatters
    │   ├── security.php        # HTML escaping (e), CSRF tokens, flash alerts
    │   └── upload.php          # Image validation (MIME, size, ext) & safe storage
    └── repositories/           # Modular Data Repositories (Database Query Layer)
        ├── ContentRepository.php    # Sections, stats, programs, campuses, partners
        ├── NavigationRepository.php # Topbar contacts & navigation menus
        ├── NewsletterRepository.php # Newsletter subscriptions handler
        └── SettingRepository.php    # Global site settings & metadata
```

---

## 🚀 Quick Setup Instructions (XAMPP / WAMP / LAMP)

### 1. Place the Project in Web Root
The project should sit cleanly in your web server root directory:
- **XAMPP on Windows:** `C:\xampp\htdocs\averroes`
- **WampServer:** `C:\wamp64\www\averroes`
- **Linux Apache:** `/var/www/html/averroes`

### 2. Start Apache and MySQL
Open your **XAMPP Control Panel** and start:
- **Apache**
- **MySQL**

### 3. Import Database (`database/averroes-ltd.sql`)
1. Open your browser and go to **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click the **Import** tab at the top.
3. Click **Choose File** and select `database/averroes-ltd.sql`.
4. Click **Import** (or **Go**).
5. The database `averroes_db` with all 10 tables and live production data will be created automatically.

> **Tip via MySQL CLI:**
> ```bash
> mysql -u root -p < database/averroes-ltd.sql
> ```

### 4. Environment Configuration (`.env`)
The project comes with automatic `.env` support. Standard XAMPP defaults work out of the box:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=averroes_db
DB_USER=root
DB_PASS=
```

---

## 🔐 Admin CMS Dashboard Access

- **Public Website URL:** `http://localhost/averroes/` (or `http://localhost/averroes (2)/`)
- **Admin Login URL:** `http://localhost/averroes/admin/`

### Default Administrator Credentials
- **Username:** `admin` *(or `admin@averroes.edu.bd`)*
- **Password:** `admin123`

*(Change username, email, and password anytime from the **Admin Profile** page inside the CMS).*

---

## ☁️ Cloud & Production Deployment Guide

### A. Deploying to Vercel (Serverless PHP)
The project includes a ready-to-use [`vercel.json`](vercel.json):
1. Push your repository to **GitHub** or **GitLab**.
2. Connect your repo in the [Vercel Dashboard](https://vercel.com).
3. Under **Project Settings > Environment Variables**, add your remote MySQL credentials:
   - `DB_HOST`: Host of your remote MySQL database (e.g. Railway, PlanetScale, Aiven, Supabase)
   - `DB_PORT`: `3306`
   - `DB_NAME`: Database name
   - `DB_USER`: Database username
   - `DB_PASS`: Database password
4. Import `database/schema.sql` into your remote MySQL instance.
5. Hit **Deploy** — Vercel will bundle the serverless PHP functions and serve all assets instantly.

### B. Deploying to cPanel / Shared Hosting
1. Compress all files in `averroes/` (or upload via FTP / Git Version Control in cPanel).
2. Extract into `public_html` (or your subdomain directory).
3. In **cPanel > MySQL Databases**, create a database and user, assigning all privileges.
4. Open **phpMyAdmin**, select your new database, and import `database/schema.sql`.
5. Update your `.env` file in the root directory with your cPanel database credentials.
6. Your dynamic school portal is live!

---

## 🛡️ Enterprise Security & Architectural Highlights

1. **SQL Injection Prevention:** 100% of queries use PDO prepared statements with bound parameters.
2. **XSS Protection:** All dynamic outputs are escaped with `htmlspecialchars()` via `e()`.
3. **CSRF Protection:** State-changing POST forms and the Admin Login screen are guarded with cryptographic CSRF tokens.
4. **Restricted Script Execution:** `assets/uploads/.htaccess` blocks execution of any uploaded `.php`, `.phtml`, or executable files.
5. **Direct Access Protection:** `.htaccess` rules deny direct HTTP access to `config/`, `database/`, and `includes/`.
6. **Root Server Hardening:** Directory indexing (`Options -Indexes`) is disabled, sensitive files (`.env`, `.sql`, `.log`) are blocked, and modern security headers are enforced.
7. **Modular Separation of Concerns:** Database queries and helper functions are isolated into clean Repositories and Helpers, keeping code clean, maintainable, and testable.
