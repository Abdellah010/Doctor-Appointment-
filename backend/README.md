# 🏥 DocAppoint — Premium Healthcare Platform

DocAppoint is a state-of-the-art medical appointment booking platform designed specifically for the Moroccan market. It provides a seamless, high-end experience for patients to find verified specialists and for doctors to manage their practice with absolute precision.

![DocAppoint Hero](https://via.placeholder.com/1200x600/059669/FFFFFF?text=DocAppoint+Healthcare+Platform)

## ✨ Core Features

### 🎨 World-Class Design
- **Premium UI/UX**: Built with a "Medical Emerald" aesthetic featuring glassmorphism and modern typography (DM Sans).
- **Dynamic Theme Support**: Native Dark and Light modes with zero-latency initialization to prevent flickering.
- **Cinematic Landing Page**: High-impact hero sections, trust statistics, and feature highlights designed to wow users.

### 🔐 Secure Specialized Portals
- **Patient Portal**: Dedicated registration and dashboard for healthcare seekers.
- **Doctor Portal (`/doctor-portal-secure`)**: Private, branded entry point for medical professionals.
- **Admin Hub (`/admin-system-secure/login`)**: Obfuscated, high-security portal for platform administrators.

### 🌍 Global Localization (i18n)
- **Full Multi-language Support**: Seamless switching between **English**, **French**, and **Arabic (RTL)**.
- **Localized UI**: Everything from date formats to currency (MAD) and RTL layouts are handled with precision.

### 🔍 Intelligent Search & Optimization
- **Icon-Rich Selection**: Intuitive specialty and location selectors with medical icons and descriptions for better patient clarity.
- **High-Performance Caching**: Query results are cached at the service layer to ensure lightning-fast responses without database bottlenecks.
- **Clean Architecture**: Decoupled service layer for search, bookings, and verifications.

---

## 🛠️ Technology Stack

- **Backend**: [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Frontend**: [Vue.js 3](https://vuejs.org/) + [Inertia.js](https://inertiajs.com/)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/)
- **Localization**: [Vue I18n](https://vue-i18n.intlify.dev/)
- **State Management**: [Pinia](https://pinia.vuejs.org/)
- **Database**: MySQL / MariaDB
- **Reminders**: Twilio SMS Integration

---

## 🚀 Installation & Setup

### 1. Clone & Dependencies
```bash
git clone https://github.com/your-repo/docappoint.git
cd docappoint/backend
composer install
npm install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```
*Update your `.env` with your database credentials and API keys (Google/Twilio).*

### 3. Database Initialization
```bash
php artisan migrate --seed
```

### 4. Running Locally
```bash
# Terminal 1: PHP Server
php artisan serve

# Terminal 2: Vite Dev Server
npm run dev
```

---

## 📦 Deployment

To build assets for production:
```bash
npm run build
```

---

## 🛡️ License

© {{ new Date().getFullYear() }} **DocAppoint**. All rights reserved. Professional Healthcare Infrastructure for Morocco.
