# Platform & infrastructure

## Shared kernel (`app/Shared`, `app/Concerns`, `app/Http`, `app/Providers`)
- `Shared/Exceptions/DomainRuleViolation`: a business-rule failure. Rendered as a toast on Inertia requests, JSON 422 otherwise (`bootstrap/app.php`).
- `Shared/Concerns/LogsModelActivity` (activity log), `FreesUniqueValuesOnDelete` (frees unique values on soft delete, sized to the column).
- `Shared/Queries/Concerns/SortsQueries` (safe sorting incl. `relation.column`), `OptionsQuery` (select options), `Shared/Support/RouteModel` (route-bound key for unique rules), `Shared/Services/PublicUploads`.
- Middleware: `HandleInertiaRequests` (shell choice and shared props: auth, menus, cart, store, wishlist, bell count), `SecurityHeaders`, `HandleAppearance`.
- `AppServiceProvider`: morph map, `Gate::before` for super admins, guest-cart merge on login, https in production.

## Running it
| What | Command |
|---|---|
| Prod-like stack | `docker compose up -d` → http://localhost:8000 (app + Postgres + Redis) |
| Dev with HMR | `scripts/dev.sh` → http://localhost:8001 (Vite on 5174) |
| Any PHP/Node command | `scripts/t.sh <cmd>` (PHP 8.3 + Node 22 container; joins the compose network) |
| PHP gates | `scripts/t.sh composer check` (Pint, Larastan level 7, PHPUnit on Postgres) |
| Frontend gates | `scripts/t.sh npm run check` (ESLint, Prettier, tsc) |
| Module generator | `scripts/modgen/modgen.py <module>` (+ `scripts/t.sh php scripts/modgen/schema.php`) |
| Docs | `scripts/gen-modules-doc.py`, `scripts/gen-project-map.py` (both take `route:list --json`) |

## Deployment
- **Docker:** `Dockerfile` (multi-stage; PHP-FPM + nginx + supervisor), `docker/entrypoint.sh` (waits for the DB through Laravel, migrates, seeds on first run only, caches in production).
- **Vercel:** `vercel.json` runs `api/index.php` on `vercel-php`. It needs a hosted PostgreSQL `DB_URL` (with `sslmode=require`) set in the Vercel project; without it there is no persistent database.

## Improvement audit
- Production on Vercel needs a hosted Postgres `DB_URL` before the next deploy.

Full list: [improvements.md](improvements.md).
