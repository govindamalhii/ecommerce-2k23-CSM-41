# Sprint 2 — Local Setup (paste into your repo's README)

## What's in this drop
- 5 migrations (`role` on users, categories, products, variants, skus)
- Models: `Category`, `Product`, `Variant`, `Sku`, updated `User`
- Admin middleware + 2 custom validation rules
- 4 admin controllers + their form requests
- Routes to merge into `routes/api.php`
- Factories + `CatalogSeeder` (matches the demo data in `docs/SPRINT_2.md`)
- Feature tests for categories, products, variants/SKUs, and authorization

**This code has not been executed** — this sandbox has no network access to
Packagist, so Composer/Laravel/Sanctum can't actually be installed here to
run it. Everything below is written to standard Laravel 10/11 conventions,
but you need to run it locally to get the real migration/test evidence the
rubric asks for.

## 1. Install dependencies
```bash
composer install
composer require laravel/sanctum   # skip if already installed
```

## 2. Environment variables
No new variables beyond the standard DB connection are required this sprint:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booknest
DB_USERNAME=root
DB_PASSWORD=
```
Don't commit `.env` — only `.env.example` with these keys left blank.

## 3. Register the `admin` middleware alias

**Laravel 10** (`app/Http/Kernel.php`):
```php
protected $middlewareAliases = [
    // ...
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
];
```

**Laravel 11** (`bootstrap/app.php`):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

## 4. Merge the routes
Copy the contents of `routes/api_admin.php` into your existing `routes/api.php`
(or `require` it from there).

## 5. Migrate and seed
```bash
php artisan migrate
php artisan db:seed --class=CatalogSeeder
```

## 6. Run the tests
```bash
php artisan test --filter=Admin
```
Record the actual command output in `docs/SPRINT_2.md` §7 once it runs —
that's the "result" evidence the rubric wants, and it can't be faked here.

## 7. Manually verify the demo flow (for §6 evidence)
1. Create an admin user (e.g. via `php artisan tinker`: `User::factory()->create(['role' => 'admin'])`), get a Sanctum token.
2. `POST /api/v1/admin/categories` → create a category.
3. `POST /api/v1/admin/products` → create a draft product.
4. `POST /api/v1/admin/products/{id}/variants` → add a variant.
5. `POST /api/v1/admin/products/{id}/skus` → add a SKU.
6. `GET /api/v1/admin/products` → confirm it's all retrievable.
Capture the real request/response pairs (with the token redacted) into `docs/SPRINT_2.md`.
