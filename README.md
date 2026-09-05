# dealership-management-system

A web-based distribution and delivery management system for a beverage and snacks business. Built on Laravel 5.8 with role-based dashboards for **Admins**, **Area Managers**, and **Shopkeepers** — covering areas, shop registration, inventory (with category/size/flavor/type variants), orders, deliveries, employee management, expenses, transactions, and product returns. Includes a SslCommerz payment gateway integration for online order settlement.

## Stack

- **Framework:** Laravel 5.8 (PHP 7.1+ target; runs on PHP 8.x with the patches noted below)
- **Database:** MySQL / MariaDB
- **Frontend:** Blade templates, Laravel Mix (webpack)
- **Auth:** Laravel's built-in auth + custom role middleware (`AdminMiddleware`, `AreaManagerMiddleware`, `ShopkeeperMiddleware`, and the combined variants)
- **Payments:** SslCommerz (Bangladesh) — `app/Library/SslCommerz/`

## Prerequisites

- PHP 7.1+ (this repo's `composer.json` target) — **PHP 8.x is also supported**, see the note below
- Composer 2.x
- MySQL 5.7+ or MariaDB 10.3+
- A local MySQL user with privileges to create databases (the project ships a `dms.sql` schema dump)

## First-time setup

```bash
# 1. Configure environment
cp .env.example .env
# Edit .env: set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 2. Install dependencies
#    If you're on PHP 8.x, the lock file (targeted at PHP 7.1+) will reject the
#    install unless you bypass the platform check. See the note below.
composer install --ignore-platform-req=php

# 3. Create the database and import the schema
mysql -u root -p -e "CREATE DATABASE dms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p dms < dms.sql

# 4. Run any pending migrations + generate the app key
php artisan migrate
php artisan key:generate

# 5. Clear caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

## Running the dev server

```bash
./start.sh                 # serves on http://127.0.0.1:8000
./start.sh 8080            # custom port
./start.sh 0.0.0.0 8080    # custom host + port
```

The script auto-detects the available PHP binary (preferring `/home/jarir-ahmed/.local/bin/php` if present), checks that `.env` and `vendor/` exist, and runs `php artisan serve` in the foreground.

## Demo users

Three demo accounts (one per role) are seeded with the password `password`:

| Role          | Email                          |
| ------------- | ------------------------------ |
| Admin         | `demo.admin@demo.test`         |
| Area Manager  | `demo.manager@demo.test`       |
| Shopkeeper    | `demo.shop@demo.test`          |

The full `users` table (including original data from `dms.sql`) can be inspected with `SELECT * FROM users;`. Roles are stored polymorphically: `action_table` holds the model class name (e.g. `App\AreaManager`) and `row_id` links to the related `area_managers` / `shopkeepers` / etc. row.

## Note on PHP 8.x

This project pins to PHP 7.1+ in `composer.json`, but the development machine used to set it up runs PHP 8.3 / 8.5 only. To make Laravel 5.8 boot on PHP 8.x, three small patches are applied inside `vendor/` (see [memory note](.gitignore) — this is just for context; the patches will be lost on a fresh `composer install` and need re-applying):

1. `vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/HandleExceptions.php` — `error_reporting(-1)` → `error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED)` so framework deprecation notices don't get converted to fatal `ErrorException`s.
2. `vendor/nesbot/carbon/src/Carbon/Traits/Creator.php` — `parent::getLastErrors()` → `parent::getLastErrors() ?: []` because PHP 8's `DateTime::getLastErrors()` returns `false` instead of an array.
3. `artisan` and `public/index.php` — `error_reporting()` set at startup, so even the autoloader is silent.

The long-term fix is either installing PHP 7.4 natively, or upgrading the project to Laravel 6+ / PHP 8+ (which is a real code change — 5.8 → 6 removed several APIs).

## Database schema

A complete schema dump is included at `dms.sql` (26 tables). Key tables:

- `users` — polymorphic auth (linked to admins / area managers / shopkeepers)
- `areas` — geographic areas
- `area_managers` — managers of an area
- `shopkeepers` — shop owners (one per shop)
- `shop_registrations` — registered shops with `area_id` FK
- `inventories` — products with `category`, `size`, `flavor`, `type` variants for beverages and snacks
- `beverage_categories`, `beverage_sizes`, `beverage_flavors`, `beverage_types` (same for `snacks_*`)
- `orders`, `order_details`, `carts`, `deliveries`, `transactions`
- `stocks`, `expenses`, `employees`, `employee_management`
- `return_products` (added by `2021_12_04_164627_create_return_products_table`)

A fix migration (`2026_09_05_220000_fix_shopkeepers_table`) adds `shopkeepers.deleted_at` (for `SoftDeletes`) and `shopkeepers.area_id` (FK to `areas.id`) — both were missing from the original `dms.sql` dump and broke the dashboard's eager-loading.

## Routes overview

All business routes are under one of five middleware groups (defined in [`routes/web.php`](routes/web.php)):

- `auth + AdminMiddleware` — full admin panel
- `auth + AreaManagerMiddleware` — area-scoped shop registration + returns
- `auth + ShopkeeperMiddleware` — shop ordering, payment, returns, transaction history
- `auth + AdminAreaManagerMiddleware` — pending deliveries, shopkeeper list
- `auth + AdminShopkeeperAreaManagerMiddleware` — shared dashboard, order status

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m "Add your feature"`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a pull request

## License

[MIT](LICENSE) — see the `LICENSE` file for the full text.
