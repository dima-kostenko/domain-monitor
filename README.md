# Domain Monitor

Admin panel for monitoring domain availability. Tracks HTTP response codes, response times and uptime statistics for any number of domains — with automatic checks on a configurable schedule.

---

## Table of contents

- [Features](#features)
- [Tech stack](#tech-stack)
- [Screenshots](#screenshots)
- [Local setup with Docker](#local-setup-with-docker)
- [Local setup without Docker](#local-setup-without-docker)
- [Running migrations & seeds](#running-migrations--seeds)
- [Artisan commands](#artisan-commands)
- [Running tests](#running-tests)
- [Deploy to Railway / Render / Fly.io](#deploy)
- [Project structure](#project-structure)

---

## Features

- **Multi-user** — each user manages their own domains independently
- **Automatic checks** — configurable per-domain interval (1 – 1440 min); scheduler runs inside Docker via Supervisor
- **Flexible HTTP checks** — GET or HEAD method, configurable timeout, follows redirects (2xx/3xx = online, 4xx/5xx = offline)
- **Statistics** — uptime %, average response time, total check count
- **History with filters** — filter check history by status and date range, paginate 25 / 50 / 100 per page
- **Dashboard** — at-a-glance overview of all domains + recent activity feed
- **Auth** — registration, login, remember me, profile settings, password change, account deletion

---

## Tech stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3 |
| Framework | Laravel 13 |
| Database | MySQL 8.2 |
| Cache / Sessions | File (Redis optional) |
| Frontend | Blade + Tailwind CSS (CDN) |
| Web server | Nginx 1.25 |
| Process manager | Supervisor 4 |
| Containerisation | Docker + Docker Compose |
| Hosting | Railway |
| Testing | PHPUnit (via `php artisan test`) |

---

## Screenshots

### Login
Clean login page with link to registration.

```
┌─────────────────────────────────────┐
│         🌐 Domain Monitor           │
│         Sign in to your account     │
│                                     │
│  Email ________________________     │
│  Password ______________________    │
│  ☑ Remember me                     │
│                                     │
│       [ Sign in ]                   │
│  No account? Create one             │
└─────────────────────────────────────┘
```

### Dashboard
Overview with 4 stat cards (total / online / offline / pending) + domain status list + recent activity feed.

```
Hello, Admin                          [ + Add domain ]

┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐
│  Total  │ │  Online │ │ Offline │ │ Pending │
│    12   │ │    10   │ │    1    │ │    1    │
└─────────┘ └─────────┘ └─────────┘ └─────────┘

Domains                        Recent activity
───────────────────────        ─────────────────────
● example.com   2 min ago      ● example.com  200 45ms
● github.com    1 min ago      ● github.com   200 120ms
● down.example  just now  ↗    ✕ down.example timeout
```

### Domains list
Paginated table with status indicator, response time, HTTP method badge, edit/delete actions.

```
Domains                                    [ + Add domain ]

┌──────────────┬────────┬─────────┬───────────┬───────┬────────┐
│ Domain       │ Status │ Response│ Interval  │ Method│ Actions│
├──────────────┼────────┼─────────┼───────────┼───────┼────────┤
│ ● example.com│ Online │ 234 ms  │ every 5m  │ HEAD  │ ✎  🗑  │
│ ● github.com │ Online │ 89 ms   │ every 1m  │ HEAD  │ ✎  🗑  │
│ ✕ down.io   │ Offline│ –       │ every 10m │ GET   │ ✎  🗑  │
└──────────────┴────────┴─────────┴───────────┴───────┴────────┘
```

### Domain detail
Stat cards (uptime %, avg response, total checks, last checked) + recent checks preview with link to full history.

```
● Online  example.com                          [ Edit ]
Check every 5 min · Timeout 10s · HEAD

┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐
│  1 248   │ │  99.8%   │ │  187 ms  │ │  2 min ago   │
│  checks  │ │  uptime  │ │  avg     │ │  last check  │
└──────────┘ └──────────┘ └──────────┘ └──────────────┘

Recent checks                          View full history →
────────────────────────────────────────────────────────
 26 Mar 14:32:01  Online   200   187 ms
 26 Mar 14:27:00  Online   200   201 ms
 26 Mar 14:22:01  Online   200   176 ms
```

### Check history
Dedicated history page with filters, colour-coded HTTP codes and response times.

```
Domains › example.com › Check history

● Online  example.com · last checked 2 min ago

┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│  1 248   │ │   1 245  │ │    3     │ │  99.8%   │ │  187 ms  │
│  total   │ │  online  │ │ offline  │ │  uptime  │ │  avg     │
└──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘

Status [All ▼]  From [    ]  To [    ]  Per page [25 ▼]  [ Apply ]

Date & time          Status    HTTP   Response   Error
────────────────────────────────────────────────────────
26 Mar 2024 14:32:01  ● Online  200   187 ms      –
26 Mar 2024 14:22:01  ✕ Offline  –     –         timeout

  ◀ 1  2  3 … 50 ▶
```

---

## Local setup with Docker

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) ≥ 24
- `make` (optional, for shortcut commands)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/dima-kostenko/domain-monitor.git
cd domain-monitor

# 2. Create environment file
cp .env.docker .env

# 3. Build images and start all services
docker compose up -d --build
# or: make up

# 4. Open the application
open http://localhost:8080
```

> On first start the `entrypoint.sh` script automatically:
> - Generates `APP_KEY`
> - Waits for MySQL to be ready
> - Runs `php artisan migrate`
> - Warms config / route / view caches

### Services and ports

| Service | Container | Host port |
|---|---|---|
| Laravel (PHP-FPM) | `crm_app` | – (internal) |
| Nginx | `crm_nginx` | **8080** |
| MySQL | `crm_db` | 3306 |
| Redis | `crm_redis` | 6379 |

Override any port in `.env`:

```dotenv
NGINX_PORT=9090
DB_EXTERNAL_PORT=3307
```

### Useful Docker / Makefile commands

```bash
make up             # build + start all services
make down           # stop all services
make restart        # restart app container
make shell          # open bash inside app container
make logs           # tail all container logs
make logs-app       # tail app logs only
make ps             # show container status

make artisan CMD="route:list"
make migrate
make seed
make tinker
make test
```

---

## Local setup without Docker

### Prerequisites

- PHP 8.3+ with extensions: `pdo_mysql bcmath exif gd intl mbstring opcache pcntl zip`
- Composer 2
- MySQL 8+ or MariaDB 10.6+
- Redis 7+ (optional — can fall back to `file` driver)

### Steps

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env
# DB_HOST=127.0.0.1  DB_DATABASE=crm  DB_USERNAME=...  DB_PASSWORD=...

# 4. Migrate and seed
php artisan migrate
php artisan db:seed

# 5. Start the dev server
php artisan serve

# 6. (Optional) Run the scheduler in a separate terminal
php artisan schedule:work
```

---

## Running migrations & seeds

```bash
# Run all pending migrations
php artisan migrate

# Rollback and re-run everything (drops all data!)
php artisan migrate:fresh

# Seed the database
php artisan db:seed

# Fresh + seed in one step (ideal for local dev)
php artisan migrate:fresh --seed
```

### Default seed accounts

| Email | Password | Role |
|---|---|---|
| `admin@example.com` | `password` | Admin |

The seeder also creates 3 regular users, each with 1–4 domains and 10–24 check history entries.

---

## Artisan commands

```bash
# Check all domains that are due (respects check_interval per domain)
php artisan check:domains

# Force-check all active domains right now (ignore intervals)
php artisan check:domains --force

# Check a single domain by its database ID
php artisan check:domains --domain=42
```

---

## Running tests

```bash
# Run the full test suite
php artisan test

# With Docker
make test

# Run a specific test file
php artisan test tests/Feature/Auth/LoginTest.php

# Run tests in parallel (faster on multi-core)
php artisan test --parallel
```

### Test coverage areas

| Suite | File | Tests |
|---|---|---|
| Auth – Registration | `tests/Feature/Auth/RegistrationTest.php` | 5 |
| Auth – Login / Logout | `tests/Feature/Auth/LoginTest.php` | 6 |
| Auth – Profile | `tests/Feature/Auth/ProfileTest.php` | 5 |
| Check history | `tests/Feature/Http/DomainCheckControllerTest.php` | 12 |
| Artisan command | `tests/Feature/Commands/CheckDomainsCommandTest.php` | 4 |
| Check service | `tests/Unit/Services/DomainCheckServiceTest.php` | 5 |

---

## Deploy

### Railway (recommended — free tier available)

1. Push the repository to GitHub
2. Go to [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub repo**
3. Add a **MySQL** plugin from the Railway dashboard
4. Set the following environment variables in the Railway service:

| Variable | Value |
|---|---|
| `APP_KEY` | `base64:...` (generate with `php artisan key:generate --show`) |
| `APP_ENV` | `production` |
| `APP_URL` | `https://your-app.up.railway.app` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `${{MySQL.MYSQL_HOST}}` |
| `DB_PORT` | `${{MySQL.MYSQL_PORT}}` |
| `DB_DATABASE` | `${{MySQL.MYSQL_DATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQL_USER}}` |
| `DB_PASSWORD` | `${{MySQL.MYSQL_PASSWORD}}` |
| `CACHE_STORE` | `file` |
| `SESSION_DRIVER` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `LOG_CHANNEL` | `stderr` |

5. Railway builds using the `Dockerfile` automatically — no extra start command needed
6. Go to **Settings → Networking → Generate Domain** to get a public URL

### Render

1. Create a **Web Service** → connect GitHub repo
2. **Environment**: Docker
3. Add a **PostgreSQL** database (change `DB_CONNECTION=pgsql` and install `pdo_pgsql` extension)
4. Add environment variables via the Render dashboard
5. **Health check path**: `/`

### Fly.io

```bash
# Install flyctl
brew install flyctl

# Login and launch
fly auth login
fly launch          # detects Dockerfile automatically
fly postgres create --name crm-db
fly postgres attach crm-db

# Set secrets
fly secrets set APP_KEY=$(php artisan key:generate --show)

# Deploy
fly deploy
```

> **Live demo:** [https://domain-monitor-production.up.railway.app](https://domain-monitor-production.up.railway.app)

---

## Project structure

```
.
├── app/
│   ├── Console/Commands/
│   │   └── CheckDomains.php          # Artisan: check:domains
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── DashboardController.php
│   │   ├── DomainCheckController.php  # GET /domains/{domain}/checks
│   │   ├── DomainController.php       # CRUD /domains
│   │   └── ProfileController.php
│   ├── Http/Requests/
│   │   ├── StoreDomainRequest.php
│   │   └── UpdateDomainRequest.php
│   ├── Models/
│   │   ├── Domain.php
│   │   ├── DomainCheck.php
│   │   └── User.php
│   └── Services/
│       └── DomainCheckService.php     # HTTP check logic
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── docker/
│   ├── entrypoint.sh                  # init: key, wait-db, migrate, cache
│   ├── supervisord.conf               # php-fpm + scheduler
│   ├── nginx/default.conf
│   ├── php/php.ini
│   ├── php/opcache.ini
│   └── mysql/my.cnf
│
├── resources/views/
│   ├── auth/          login.blade.php, register.blade.php
│   ├── components/    breadcrumb, stat-card, status-badge
│   ├── dashboard/     index.blade.php
│   ├── domain_checks/ index.blade.php   ← history + filters
│   ├── domains/       index, show, create, edit
│   ├── layouts/       app.blade.php, _form.blade.php
│   └── profile/       edit.blade.php
│
├── routes/
│   ├── web.php
│   └── console.php                    # schedule::command(...)
│
├── tests/
│   ├── Feature/Auth/
│   ├── Feature/Commands/
│   ├── Feature/Http/
│   └── Unit/Services/
│
├── Dockerfile
├── docker-compose.yml
├── Makefile
└── .env.docker
```

---

## License

MIT
