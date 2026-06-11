<div align="center">
  <img src="assets/banner.png" alt="NexRun Banner" width="100%">
  
  <h1>🏃‍♂️ NexRun</h1>
  <p><strong>Engineered for Athletes. Designed for Performance.</strong></p>
  
  <p>
    <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
    <img src="https://img.shields.io/badge/React-19-61DAFB?style=for-the-badge&logo=react" alt="React">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php" alt="PHP">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
    <img src="https://img.shields.io/badge/Pest-Testing-FF2D20?style=for-the-badge" alt="Pest">
  </p>
</div>

<br>

<div align="center">
  <img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExc2p2Y3I2dXBseGhoMXd0eXhhcXNtb2RrbHpmZ29hYzBnN3EzeXhseiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/1iTHHRWE5uqJ2/giphy.gif" alt="Running App Demo" width="600" style="border-radius: 10px;">
</div>

---

## 🚀 Overview

NexRun is a premium, modern monolithic web application designed exclusively for high-performance sportswear and footwear e-commerce. It brings together cutting-edge web technologies to deliver a lightning-fast, ultra-responsive shopping experience.

Whether you're browsing the latest athletic gear or managing orders in the admin dashboard, NexRun ensures absolute reliability through robust architecture and comprehensive automated testing.

## ✨ Key Features

- 🛍️ **Granular Product Catalog**: Deep dimensional variants (Size, Color, SKU) with high-res imagery.
- 📦 **Real-time Inventory Tracking**: State-machine-based inventory reservation to prevent overselling.
- 💳 **Seamless Checkout & Payments**: Multi-step checkout with integrations for Stripe, PayPal, SSLCommerz, bKash, and Nagad.
- 🎯 **Loyalty & Referrals**: Earn points, validate coupons, and refer friends natively.
- 🔐 **Secure Authentication**: Sanctum API authentication with role-based access control (RBAC).
- 📊 **Admin Analytics Dashboard**: Comprehensive metrics for sales, products, and customers.

---

## 🛠️ Tech Stack & Architecture

### Backend (API)
- **Framework:** Laravel 11 / PHP 8.2+
- **Database:** MySQL 8.0
- **Caching & Queues:** Redis
- **Authentication:** Laravel Sanctum
- **Testing:** Pest PHP (100% Core Feature Coverage)

### Frontend (SPA)
- **Core:** React 19
- **Routing:** TanStack Start
- **Styling:** Tailwind CSS v4 with Native CSS variables (`oklch()`)
- **Animations:** Framer Motion

---

## 💻 Installation & Setup

### Prerequisites
- PHP >= 8.2
- Node.js >= 20
- MySQL >= 8.0
- Composer

### 1. Backend Setup

```bash
# Navigate to the backend directory
cd backend

# Install PHP dependencies
composer install

# Setup environment variables
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seed the database with dummy data
php artisan migrate:fresh --seed

# Start the local development server
php artisan serve
```

### 2. Frontend Setup

```bash
# Navigate to the root directory
cd ..

# Install Node.js dependencies
npm install

# Start the frontend Vite development server
npm run dev
```

---

## 🧪 Testing

NexRun uses **Pest PHP** for elegant and robust testing. The test suite covers all core modules including cart flows, inventory reservations, order state machines, and payment integrations.

To run the full test suite:
```bash
cd backend
php artisan test
```

To run a specific test suite (e.g., Payments):
```bash
php artisan test --filter Payment
```

---

## 📁 Repository Structure

```text
NexRun/
├── backend/                  # Laravel API
│   ├── app/Models/           # Eloquent Models & Logic
│   ├── app/Services/         # Core Business Logic (Cart, Orders, Payments)
│   ├── database/migrations/  # Database Schemas
│   └── tests/Feature/        # Pest Integration Tests
├── assets/                   # README assets and images
└── README.md                 # Project Documentation
```

---

## 📄 License

Proprietary & Confidential. Copyright © 2026 NexRun.
