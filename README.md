# DocAppoint — Full-Stack Medical Appointment Platform

> **Stack**: Vue 3 + Inertia.js · Laravel 11 · MySQL · Redis · TypeScript · Tailwind CSS

## Architecture

```
docappoint/
├── frontend/          # Vue 3 + TypeScript + Tailwind + Pinia
├── backend/           # Laravel 11 + Sanctum + Eloquent
├── docker-compose.yml
└── README.md
```

## Tech Stack

| Layer | Technology | Why |
|---|---|---|
| Frontend SPA | Vue 3 + Composition API | Reactive, component-first, excellent TS support |
| SPA Bridge | Inertia.js | No separate API — Laravel sends props directly |
| Styling | Tailwind CSS v3 | Utility-first, custom design tokens |
| State | Pinia | Lightweight, TypeScript-native, Vue 3 first-class |
| Backend | Laravel 11 | Eloquent ORM, policies, jobs, queues |
| Auth | Laravel Sanctum | SPA cookie auth + API token support |
| Database | MySQL 8 | Relational, ACID, full-text doctor search |
| Cache/Queue | Redis | SMS/email jobs, session store, rate limiting |
| Containers | Docker + nginx | Reproducible dev environment |

## Quick Start

```bash
# 1. Clone
git clone https://github.com/your-org/docappoint.git && cd docappoint

# 2. Start services
docker-compose up -d

# 3. Backend
cd backend && composer install && cp .env.example .env
php artisan key:generate && php artisan migrate --seed
php artisan queue:work &

# 4. Frontend
cd ../frontend && npm install && npm run dev
# → http://localhost:5173
```

## User Roles

| Role | Capabilities |
|---|---|
| **Patient** | Browse doctors, book/cancel, view history |
| **Doctor** | Manage schedule, accept/decline requests |
| **Admin** | Verify doctors, platform overview |

## Key Features

- Doctor credential verification flow
- Real-time slot locking (no double-bookings)
- Insurance filtering (CNSS, RAMED, private)
- SMS + email reminders via queued jobs
- Patient rating system post-appointment
- Mobile-responsive design system
