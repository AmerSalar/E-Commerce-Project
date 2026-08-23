# GAME STORE - E-COMMERCE REST API

A clean RESTful API built with Laravel, featuring stateless JWT authentication,
role-based access control (RBAC), concurrency-safe shopping cart & checkout flows using database row locking, product management with automatic image compression, and multi-step password resets.

## REQUIREMENTS

- **PHP** `^8.3` or higher
- **Composer** (PHP dependency manager)
- **PHP GD extension** — required by `intervention/image` for image processing
- **MySQL** — default DB driver (`DB_CONNECTION=mysql`), no separate DB server needed
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
