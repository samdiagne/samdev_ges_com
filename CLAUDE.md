# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Al-Adab Tech** is a Laravel 12 e-commerce and business management platform with two main areas:
- **Public Shop** (`/shop`): Customer-facing catalog, cart, checkout with Paydunya payment integration
- **Admin Dashboard** (`/dashboard`): POS, inventory, sales, purchases, expenses, invoices, quotes, and financial reports

## Common Commands

```bash
# Install dependencies
composer install
npm install

# Development servers (run concurrently)
php artisan serve
npm run dev

# Or using the composer setup script
composer run setup

# Database
php artisan migrate
php artisan db:seed

# Code style (Laravel Pint)
./vendor/bin/pint

# Tests
php artisan test
php artisan test --filter=TestClassName

# PDF & assets
npm run build
```

## Architecture

### Authentication & Authorization

Two separate auth flows coexist:
- **Admin/Staff**: Standard Laravel auth via `/login`, roles enforced with Spatie permission
- **Customers**: Separate auth at `/compte/connexion` and `/compte/inscription` via `Shop/AuthClientController`

Three roles via Spatie (`spatie/laravel-permission`): `admin`, `gestionnaire`, `client`
- Custom middleware: `EnsureUserIsActive`, `EnsureUserIsAdmin`, `EnsureUserIsGestionnaire`
- User model has helper methods: `isAdmin()`, `isGestionnaire()`, `isClient()`

### Two Sales Systems (Important Distinction)

The project has **two separate order/sales flows** that must not be confused:
- **`Order` + `OrderItem`**: E-commerce orders placed through `/shop/checkout`. Includes Paydunya payment tokens, delivery address, and statuses like `pending/confirmed/shipped/delivered`.
- **`Sale` + `SaleItem`**: POS counter sales from `/caisse/pos`. Tracks HT/TVA/TTC amounts, used to generate `Invoice` records.

### Livewire Components

The app uses Livewire 4.2 extensively for reactive UIs without JS. Key components:
- `CatalogueShop` — product filtering, search, sort, pagination on the shop catalog
- `CartIcon` — live cart item count in the navbar
- `PointOfSale` — full POS interface in `/caisse`
- Admin management components for products, categories, clients, dashboard stats, invoices, quotes, financial states

Livewire components are in `app/Livewire/` and their views in `resources/views/livewire/`.

### Payment Integration (Paydunya)

Config is in `config/paydunya.php`. The flow:
1. `Shop/CheckoutController` creates an `Order` and calls Paydunya API
2. Paydunya redirects to `GET /shop/payment/callback` on success
3. `POST /shop/payment/webhook` handles async payment status updates
4. Tokens stored in `orders.paydunya_token` and `orders.paydunya_invoice_token`

### PDF Generation

Uses `barryvdh/laravel-dompdf`. PDFs are generated in:
- `FactureController` — invoices
- `DevisController` — quotes
- `EtatController` — financial reports (sales, expenses, treasury, stock balance)

Company logo, signature, and stamp are stored in `company_settings` and embedded in PDFs.

### Frontend Stack

- **TailwindCSS** with `@tailwindcss/forms` plugin
- **Alpine.js** for lightweight interactivity
- **Vite** for asset bundling (HMR in dev)
- No custom CSS framework — pure Tailwind utility classes

### Key Config Files

- `config/paydunya.php` — payment gateway credentials and store info
- `config/permission.php` — Spatie RBAC configuration
- `database/seeders/` — includes role/permission seeding and demo data

### Image Handling

Product images use `intervention/image-laravel`. Uploads stored in `storage/app/public/`. Run `php artisan storage:link` if images don't appear.

## Route Organization

Routes in `routes/web.php` are grouped:
1. Public shop routes — `prefix('shop')`
2. Customer account routes — `prefix('compte')`
3. Admin dashboard routes — protected by `auth` + `active` middleware, further restricted by role middleware per section

The `caisse` (POS) section requires `gestionnaire` role. Settings and user management require `admin` role.
