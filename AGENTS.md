# AGENTS.md

## Stack

- **Laravel 12** (PHP 8.2+) with **SQLite**, **Sanctum** (token auth), **l5-swagger** (OpenAPI 3.0)
- Single app (no monorepo)

## Commands

```bash
# serve
php artisan serve

# test (runs `config:clear` first via composer script)
composer test

# code style (Laravel Pint)
./vendor/bin/pint

# regenerate swagger docs (annotations in controllers + app/Swagger/OpenApi.php)
php artisan l5-swagger:generate

# migrate + seed
php artisan migrate --seed

# full dev environment (server + queue + logs + vite concurrently)
composer dev
```

## API

| Method | Path | Auth | Notes |
|--------|------|------|-------|
| POST | `/api/auth/login` | No | body: `{"email":"admin@example.com", "password":"password"}` |
| GET | `/api/banks` | No | returns supported banks/ewallets |
| POST | `/api/auth/logout` | Bearer Token | 5000/day per user/IP |
| POST | `/api/cek-rekening` | Bearer Token | 5000/day; body: `{"bank_code":"bca", "account_number":"0888123456"}` |

- Swagger docs at `/api/documentation`
- Test accounts in `app/Services/BankValidators/DummyValidator.php` (BCA 0888123456 → Budi Santoso, etc.)
- `POST /cek-rekening` returns **200** (valid) or **404** (invalid)
- Response fields: `type` (bank/ewallet), `reference_id` (UUID), `validator` (dummy/xendit/flip)

## Architecture

- **Validator chain** (order in `AppServiceProvider::register()`): Xendit → Flip → Dummy
  - Each implements `BankValidatorInterface`; `isEnabled()` gates it
  - `DummyValidator` always enabled; Xendit/Flip opt-in via `.env`: `XENDIT_ENABLED=true` + `XENDIT_API_KEY`, similarly `FLIP_ENABLED` + `FLIP_API_KEY`
  - `BankValidationManager` iterates validators; returns first valid/invalid; skips unsupported/error
- **Middleware**: `RequestLogMiddleware` prepended to API stack in `bootstrap/app.php`, logs to `storage/logs/`
- **Rate limiter** (`AppServiceProvider::boot()`): `Limit::perDay(5000)` keyed by user ID or IP, applied to all auth:sanctum routes
- **Swagger**: PHP 8 attributes on controller methods + `app/Swagger/OpenApi.php`
- **Demo UI** at `/demo` — password gate via `DEMO_PASSWORD` env var; auto-logins to API on success (session-based token, no manual email/password)

## Testing

- SQLite `:memory:` (see `phpunit.xml`), no database file needed
- Run: `composer test` or `php artisan test`
