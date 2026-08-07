# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel 11 JSON API (PHP 8.2+) for LegumexApps — an agro-industrial ERP covering agricultural field work (agrícola), quality control (calidad), production planning, and warehouse (bodega). API-only: no Blade UI beyond the default welcome page. Deployed to AWS via Laravel Vapor (`vapor.yml`, app `legumexapps-api`, prod domain `legumexappsapi.com`).

## Commands

```bash
composer dev              # server + queue:listen + pail logs + vite, all concurrently
php artisan serve         # API only
php artisan migrate       # 164+ migrations
php artisan db:seed       # DatabaseSeeder -> Role/Permission/User/Crop/Finca/Lote/Recipe seeders

php artisan test                                   # full suite
php artisan test --filter=SomeTest                 # single test
php artisan test tests/Feature/ExampleTest.php     # single file
./vendor/bin/pint                                  # format (Laravel Pint)

vapor deploy production   # build steps live in vapor.yml
```

Tests are effectively unwritten (only the two skeleton `ExampleTest`s). `phpunit.xml` has the sqlite in-memory `DB_CONNECTION`/`DB_DATABASE` lines **commented out**, so tests run against whatever `.env` points at — uncomment them or set the env explicitly before writing DB-touching tests.

## Architecture

### Routing
`routes/api.php` is only a set of `require_once` calls; real routes live in domain files, each wrapping everything in `Route::middleware('jwt.auth')`:

| File | Domain |
|---|---|
| `auth.php` | login/logout (unauthenticated) |
| `users.php` | users, roles, permissions |
| `agricola.php` | fincas, lotes, crops, weekly plans, insumos, plantation control |
| `calidad.php` | quality control, defects, transport inspections, RM receptions |
| `production.php` | lines, SKUs, weekly production plans, task production lifecycle |
| `bodega.php` | packing materials, transactions, receptions |
| `reports.php` | Excel report downloads |

There is no route prefix — endpoints sit at the root of `/api`. `bootstrap/app.php` registers no custom middleware; `jwt.auth` and the `api` guard (`driver => jwt`) come from `tymon/jwt-auth`.

### Auth & permissions
JWT (`tymon/jwt-auth`) plus `spatie/laravel-permission`. `User::getJWTCustomClaims()` embeds `id`, `name`, `role`, `email` in the token. Controllers commonly read identity straight from the token rather than `auth()`:

```php
$payload = JWTAuth::getPayload();
$user = User::find($payload->get('id'));
$permissions = $user->getRoleNames()/getPermissionNames();
```

Authorization is done **inside controller methods** (conditionals on role name / permission names that filter the Eloquent query), not via route middleware or policies — e.g. `SKUController::index` restricts SKU codes by `create pcs tasks` / `create pab tasks` / `create ptf tasks` permissions for non-`admin`/`logistics`/`costosuser` roles. Follow that pattern when adding scoped endpoints.

### Layers
- `app/Http/Controllers/` — ~67 controllers, mostly `apiResource` + many custom verbs registered explicitly in the route files.
- `app/Http/Requests/` — FormRequests for validation; controllers use `$request->validated()`.
- `app/Http/Resources/` — API shaping; return `XResource::collection(...)` or `new XResource(...)`.
- `app/Services/` + `app/Repositories/` — used only for the agrícola/plantation-control/notification slice (Lote, CropDisease, CropPart, PlantationControl, Email). These services instantiate their collaborators with `new` in the constructor rather than DI. Most other domains put logic directly in the controller.
- `app/Exports/` + `app/Imports/` — `maatwebsite/excel`; Excel upload endpoints (`/skus/upload`, `/packing-materials/upload`, …) and report downloads.
- `app/Events/` + `routes/channels.php` — Laravel Reverb broadcasting on public channels `planification.change` / item status; `Broadcast::routes` is registered with `jwt.auth` in `AppServiceProvider`.
- `app/Console/Commands/` — batch jobs (weekly payment calculation, production-change emails, employee assignment, data fixups).
- Microsoft Graph (`microsoft/microsoft-graph`) powers outgoing mail via `App\Abstracts\EmailProvider` → `App\Providers\EmailProvider` → `EmailService`; recipients come from `NOTIFY_EMAILS*` env vars.
- External biometric attendance system integrated through `BIOMETRICO_URL*` env vars (`Biometric*` models).

### Conventions to match
- Controller methods wrap work in `try/catch (\Throwable $th)` and return `response()->json(['msg' => $th->getMessage()], 500)`.
- User-facing response messages are in **Spanish** (`'SKU Creado Correctamente'`, `'Credenciales Incorrectas'`); code identifiers are English/Spanish mixed (`Finca`, `Lote`, `Insumo`, `Tarea` stay Spanish).
- Custom controller actions use `PascalCase` method names (`UploadStockKeepingUnits`, `GetTasksByLineId`), unlike the resource methods.
- Commit messages: `feat:` / `patch:` prefix, English, lowercase.
- Timezone is `America/Guatemala`.
