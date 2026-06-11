# NexRun

**Engineered for Athletes.**

NexRun is a premium, modern monolithic web application designed for high-performance sportswear and footwear e-commerce. Built with a Laravel backend and a TanStack Start React frontend.

## Architecture

- **Frontend:** React 19, TanStack Start, Tailwind CSS v4, Framer Motion.
- **Backend:** Laravel 12, MySQL, Sanctum (API Authentication), Redis.
- **Design System:** Native CSS variables (`oklch()`) combined with Tailwind `@theme` logic.

## Prerequisites

- PHP >= 8.2
- Node.js >= 20
- MySQL >= 8.0
- Composer

## Installation

### 1. Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Configure your `.env` with your database credentials, then run:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

### 2. Frontend Setup

```bash
# In the project root
npm install
npm run dev
```

## Features

- **Core Commerce:** Granular product catalog, dimensional variants, real-time inventory tracking.
- **Checkout & Payments:** Multi-step checkout, state machine order tracking, integrations with Stripe, PayPal, SSLCommerz, bKash, and Nagad.
- **Ancillary Modules:** Loyalty points, referral mechanics, coupon validation, and an extensive admin analytics dashboard.

## License

Proprietary & Confidential. Copyright © 2026 NexRun.
