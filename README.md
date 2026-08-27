# GAME STORE - E-COMMERCE REST API

A clean RESTful API built with Laravel, featuring stateless JWT authentication,
role-based access control (RBAC), concurrency-safe shopping cart & checkout flows using database row locking, product management with automatic image compression, and multi-step password resets.

## REQUIREMENTS

- **PHP** `^8.3` or higher
- **Composer** (PHP dependency manager)
- **PHP GD extension** — required by `intervention/image` for image processing
- **MySQL** — default DB driver (`DB_CONNECTION=mysql`), a separate `mysql` server needed
  - _Alternative:_ SQLite or PostgreSQL.

## INSTALLATION

Clone the repo

```Bash
git clone "https://github.com/AmerSalar/E-Commerce-Project.git"
cd api
```

Install PHP dependencies

```Bash
composer install
```

Configure the environment

```Bash
cp .env.example .env
```

Don't forget to set your database in the `.env` file.
here is an example for local setup:

```env code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=game_store_db
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

Generate APP key and JWT secret

```Bash
php artisan key:generate
php artisan jwt:secret
```

Run migrations and seed (if you wanted)

```Bash
php artisan migrate --seed
```

Link to public storage

```Bash
php artisan storage:link
```

Run the server

```Bash
php artisan serve
```

## MIGHT WANT

Run tests using

```Bash
php artisan test
```

Check out the API documentation at this route

```bash
/docs/api
```

## KEY FEATURES

1. Stateless JWT Auth:
   Token-based authentication
2. Concurrency-Safe Cart & Checkout:
   Uses `DB::transaction()` combined with `lockForUpdate()` to prevent
   race conditions and avoid overselling.
3. Granular RBAC Policies:
   Dedicated policies preventing self-demotion, self-deletion,
   and enforcing super admin immutability.
4. Optimized Image Processing:
   Automatic conversion to `.webp` format and compression via
   Intervention Image on product creation.
5. Multi-Step Password Reset:
   6-digit numeric OTP generation, verification handoff tokens,
   and password reuse prevention.

## HOW IT WAS MADE

- **JWT authentication** (`php-open-source-saver/jwt-auth`) — login, register, refresh, and forced logout via token blacklisting on password change

- **Role-based access control** via Laravel Gates & Policies — both auto-resolved model policies (`Gate::policy()`) and custom-named gates (`Gate::define()`) for non-CRUD actions

- **Pessimistic locking with `lockForUpdate()`** inside `DB::transaction()` — used for cart/stock mutations to prevent race conditions and overselling under concurrent requests

- **Eloquent relationships**, including `belongsToMany` with custom pivot tables (`withPivot`, `withTimestamps`, `syncWithoutDetaching`) for cart items and order items

- **Form Request validation classes** for request-level authorization and rules, keeping controllers thin

- **API Resources & Resource Collections** for consistent, whitelisted JSON response shaping (no accidental field leakage from Eloquent models)

- **Service layer pattern** — business logic (e.g. checkout, cart operations) extracted out of controllers into dedicated service classes

- **Custom reusable traits** — a relation-whitelisting trait (`HasRelations`) to safely support `?include=` query params without exposing arbitrary Eloquent relations

- **Migrations with deliberate FK strategies** — `cascadeOnDelete()` vs `nullOnDelete()` chosen per-table based on whether historical data (orders) needs preserving vs. active data (carts) doesn't

- **Database factories & seeders** for reproducible local/test data, including role and address factories

- **Automated testing with Pest** — feature tests for auth/orders/products and unit tests for helper classes

- **Centralized exception handling** in `bootstrap/app.php` instead of per-route `->missing()` callbacks, for consistent error responses

- **Custom validation exceptions** (`ValidationException::withMessages()`) for business-rule failures, not just field validation

- **Rate limiting / throttling** on sensitive auth endpoints (login, register, password reset, OTP verification)

- **Image processing on upload** (`intervention/image`) — compressing, resizing, and format-converting product photos server-side

- **Environment-gated service providers** — conditionally registering dev-only tooling (Laravel Telescope) so it never loads in production

- **API documentation generation** (`dedoc/scramble`) via inline docblock annotations on Resources/Requests

- **Route model binding** with route-level constraints (`whereNumber()`) to prevent malformed parameter matching

This project is for portfolio purposes only. All rights reserved.
Feel free to read the code for learning purposes, but please don't reuse or redistribute it without permission.
