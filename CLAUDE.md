# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Al-Adab Tech** is a Laravel 12 e-commerce and business management platform for a food/restaurant business. Two main areas:
- **Public Shop** (`/shop`): Customer-facing catalog, cart, checkout with Paydunya payment integration
- **Admin Dashboard** (`/dashboard`): POS, inventory, sales, purchases, expenses, invoices, and financial reports (PDF)

## Common Commands

```bash
# Full dev environment (server + queue + logs + vite, all concurrently)
composer run dev

# First-time setup (install deps, generate key, migrate, build assets)
composer run setup

# Individual commands
php artisan serve          # Laravel dev server
npm run dev                # Vite HMR
php artisan migrate        # Run migrations
php artisan db:seed        # Seed database

# Code style
./vendor/bin/pint

# Tests
php artisan test
php artisan test --filter=TestClassName

# Build for production
npm run build

# Storage link (required for images to display)
php artisan storage:link
```

## Architecture

### French Naming Convention

All models, database columns, routes, views, and business logic use French naming. Key vocabulary:
- `Plat` = product/dish, `Vente` = sale, `Achat` = purchase, `Depense` = expense
- `Commande` = e-commerce order, `Facture` = invoice, `Fournisseur` = supplier
- `Categorie` = category, `Avis` = review, `Parametre` = company settings
- `lignes` = line items (e.g., `VenteLigne`, `CommandeLigne`, `AchatLigne`)
- `est_actif` / `est_active` = is_active, `vendu_le` = sold_at, `emise_le` = issued_at

### Authentication — Two Models, Two Flows

The default auth model is **`Utilisateur`** (not `User`), configured in `config/auth.php`. This is critical — `auth()->user()` returns an `Utilisateur` instance.

- **Admin/Staff auth**: Laravel Breeze at `/login`. `Utilisateur` has a `role` column (`admin` or `gestionnaire`). Role checks via `isAdmin()` and `isGestionnaire()` methods directly on the model (no Spatie roles on this model).
- **Customer auth**: Separate flow at `/compte/connexion` and `/compte/inscription` via `Shop\AuthClientController`. Customers are stored in both `utilisateurs` and `clients` tables (linked via `client.utilisateur_id`).

A `User` model also exists with Spatie `HasRoles` trait, but the auth guard uses `Utilisateur`.

Middleware aliases registered in `bootstrap/app.php`:
- `admin` → `EnsureUserIsAdmin` (checks `role === 'admin'`)
- `gestionnaire` → `EnsureUserIsGestionnaire` (checks `role in ['admin', 'gestionnaire']`)
- `active` → `EnsureUserIsActive`

### Three Sales Flows (Important Distinction)

- **`Vente` + `VenteLigne`**: POS counter sales from `/caisse/pos`. Created via `VenteService::creerVente()`. Each sale auto-generates a `Facture`. References prefixed `VNT-`. `source` field is `boutique` (direct POS) or `devis` (converted from a quote).
- **`Commande` + `CommandeLigne`**: E-commerce orders from `/shop/commande`. Created via `CheckoutController::store()`. Includes Paydunya payment tokens, delivery info, statuses (`en_attente`/`confirmee`/`expediee`/`livree`/`annulee`). References prefixed `CMD-`.
- **`Quote` + `QuoteItem`**: Quotes/estimates from `/devis`. Created via `DevisForm` Livewire component. References prefixed `DEV-`. Statuses: `draft`, `sent`, `accepted`, `refused`. Can be converted to a `Vente` via `QuoteService::convertToSale()`, which also decrements stock and generates a `Facture`.

A `Commande` can link to a `Vente` via `commande.vente_id`, but they are created independently.

### Service Layer

Business logic is in `app/Services/`:
- **`VenteService`** — Creates POS sales: builds `Vente` + `VenteLigne` records, decrements stock, auto-generates `Facture`, all in a DB transaction.
- **`StockService`** — All stock mutations (`incrementerStock`, `decrementerStock`, `ajusterStock`) go through this service, which creates `MouvementStock` audit records.
- **`FactureService`** — Generates invoice PDFs via DomPDF. Uses absolute file paths for logo/stamp/signature (DomPDF can't access URLs).
- **`QuoteService`** — Generates quote PDFs (`generatePdf`), streams downloads (`download`), and converts a quote to a `Vente` + `Facture` (`convertToSale`).

### Stock Tracking

Every stock change creates a `MouvementStock` record with `type` (entree/sortie), `source` (vente/achat/ajustement), before/after quantities, and who did it. Always use `StockService` rather than directly modifying `plat.stock`.

### Livewire Components

Most admin UI is Livewire 4.2 components in `app/Livewire/`. Routes often render simple Blade views that mount a Livewire component (e.g., `Route::get('/plats', fn() => view('plats.index'))` where `plats.index` mounts `<livewire:plats.plat-manager />`).

Key components:
- `Caisse\PointOfSale` — full POS interface with cart, client search/quick-create, payment modal, receipt generation
- `Devis\DevisForm` — quote builder with product search, free-form lines, tiered pricing, and quick client creation
- `Shop\CartDrawer` / `Shop\CartIcon` — Alpine.js-driven cart sidebar on the public shop

### Payment Integration (Paydunya)

Config in `config/paydunya.php` (env vars: `PAYDUNYA_MASTER_KEY`, `PAYDUNYA_PUBLIC_KEY`, `PAYDUNYA_PRIVATE_KEY`, `PAYDUNYA_TOKEN`, `PAYDUNYA_MODE`).

Flow: `CheckoutController::store()` → creates `Commande` → calls `initiatePaydunya()` → redirects to Paydunya → callback at `GET /shop/payment/callback` → webhook at `POST /shop/payment/webhook` (CSRF-exempt in `bootstrap/app.php`).

### PDF Generation

Uses `barryvdh/laravel-dompdf` with `DejaVu Sans` font. PDF templates in `resources/views/pdf/`.
- `FactureService` — invoices (stored in `storage/app/public/factures/`)
- `EtatController` — financial reports: sales, expenses, treasury, stock balance (date-range filtered, downloaded directly)

Company branding (logo, stamp, signature) loaded from `Parametre` model and embedded as base64 in PDFs.

### Data Import

`maatwebsite/excel` handles bulk imports via `app/Imports/ProduitsImport.php`. Column mapping is flexible — supports French and English header aliases (e.g., "nom", "name", "produit", "plat" all map to the product name field).

### Company Settings

The `Parametre` model (singleton row) holds all company config: name, NINEA, RC, contact info, logo/signature/stamp paths, invoice prefix/counter, social links, and currency. Accessed via `Parametre::first()` throughout the app.

`ViewServiceProvider` registers `SettingsComposer` on every view, so `$parametres` is automatically available in all Blade templates without needing to pass it from each controller.

### Purchases (Achats)

`Achat` + `AchatLigne` records supplier purchases managed via the `Caisse\AchatManager` Livewire component at `/caisse/achats`. References prefixed `ACH-`. `fournisseur_id` is nullable (purchases can be recorded without a supplier). Statuses: `en_attente`, `recu`, `annule`.

### Email Notifications

Queued mailable classes in `app/Mail/`:
- `OrderConfirmationMail` / `NewOrderAdminMail` — sent on successful Paydunya payment
- `OrderDeliveredMail` — sent when a `Commande` is marked `livree`
- `NewReviewAdminMail` — sent when a customer submits an `Avis`

Emails are queued (the `queue:listen` process in `composer run dev` must be running). Admin email address is configured via `MAIL_ADMIN_ADDRESS` env var, read as `config('mail.admin_address')`.

### Plat Features

- `sur_commande` (boolean) — made-to-order items bypass stock checks in POS and shop
- Tiered pricing: `quantite_palier` + `prix_palier` — use `$plat->prixPourQuantite($qty)` to get the correct price
- `est_mis_en_avant` — featured/highlighted on the public shop hero section
- `PlatImage` — ordered gallery images (separate from `image_principale`)
- `Avis` (reviews) require admin approval (`est_approuve`) before showing publicly

### Artisan Import Commands

```bash
# Import purchases and expenses from an MAFFOOD-format Excel file
php artisan import:achats-depenses {fichier}

# Import historical sales from Excel
php artisan import:ventes {fichier}
```

### Route Organization

Routes in `routes/web.php`:
1. **Public shop** — `prefix('shop')`: catalog, cart, checkout, payment callbacks, static pages
2. **Customer account** — `prefix('compte')`: auth, order history, profile
3. **Admin/Staff** — `middleware(['auth', 'active'])`: dashboard, products, categories, clients, invoices, reports, financial statements
4. **POS** — `prefix('caisse')->middleware('gestionnaire')`: point of sale, sales, purchases, expenses
5. **Admin-only** — `middleware('admin')`: settings (`/parametres`), user management (`/utilisateurs`)

### Frontend Stack

TailwindCSS + Alpine.js + Vite. No custom CSS framework. Livewire handles reactivity for admin components.
