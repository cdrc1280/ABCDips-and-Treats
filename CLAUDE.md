# CLAUDE.md

ABCDips & Treats: a pastry business management system (back office + customer storefront).
Laravel 13 · Inertia v3 · React 19 · TypeScript · Tailwind v4 · shadcn · PostgreSQL 17.
No Filament, no Vue, no Livewire. Those were removed on purpose, so don't reintroduce them.

## Read first
- `docs/HANDOFF.md`: the current state of the rewrite, phase status and ordered next steps.
- `docs/project/README.md`: the project map, one file per domain, plus `flows.md` (end-to-end business flows) and `improvements.md` (the audit backlog).
- `.cursorrules`: brand palette, banned copywriting words and performance rules. These apply here too.
- `graphify-out/` is a code knowledge graph. Use it for architecture questions.

## Layout
The Laravel + React app lives in **`src/`** (its own `composer.json`, `package.json`, `vendor/`, `node_modules/`, `.gitignore`, `vercel.json`, `api/`). The repo root holds only project tooling: `docker/` (incl. `app.Dockerfile`), `docker-compose.yml`, `scripts/` (dev helpers, doc generators, ui-audit), `docs/`, `graphify-out/`. Every app path below (`app/…`, `resources/…`, `tests/…`) is relative to `src/`. Vercel's Root Directory must be `src` once this layout reaches `main`.

## Commands
The host `npx`/`php` don't work in this WSL setup. Run everything from the repo root through the tools container (it mounts `src/` at `/app`):

```sh
scripts/t.sh php vendor/bin/phpunit              # tests (use this, NOT `php artisan test`: there is no .env in the container)
scripts/t.sh php vendor/bin/phpunit --filter=CategoryTest
scripts/t.sh composer check                      # Pint + Larastan (level 7) + PHPUnit
scripts/t.sh npm run check                       # ESLint + Prettier + tsc
scripts/t.sh php artisan wayfinder:generate --with-form   # after adding or changing routes
```

- Tests run against Postgres DB `abcdips_test` and need the compose `db` container up (`docker compose up -d db`).
- Prod-like: `docker compose up -d` → http://localhost:8000 (admin@abcdips.test / Password123!)
- Dev with HMR: `scripts/dev.sh` → http://localhost:8001, Vite on **5174**. Port 5173 belongs to another project, so never use or kill it.

## Architecture
- **Domains**: `app/Domains/<Domain>/{Models,Controllers,Actions,Data,Queries,Resources,Enums,...}`. `Catalog/*Category*` is the reference module, so copy its shape:
  - Controllers are thin; they implement `HasMiddleware` with `can:` permission enums and delegate to `Actions` (writes) and `Queries` (reads).
  - Input is validated as spatie/laravel-data `*Data`; output goes through `*Resource`. `#[FormRules(...)]` exposes the rules to the frontend.
  - Permissions are enums per domain (`Enums/*Permission.php`), registered in the Access domain.
- **Shared kernel**: `app/Shared`. **Routes**: `routes/admin/*.php` (back office) and `routes/storefront/*.php`, globbed by `routes/web.php`.
- **Frontend**: `resources/js/pages/<module>/{index,create,edit}.tsx`, `components/ui` (shadcn), `components/storefront`. Use Wayfinder-generated route helpers (`resources/js/actions`, `routes`), never hard-coded URLs. Two entry points: `app.tsx` (admin) and `storefront.tsx`.
- **Keys**: UUIDv7 on all business tables. Never assume integer IDs.
- **Money and prices** are decided server-side only (`Storefront/Support/CartPricing`, `CustomCakePricing`, `DeliveryQuoter`). Never trust client totals.
- **New CRUD modules**: generate them with `python3 src/scripts/modgen/modgen.py` + `specs.py`, then hand-edit. Refresh the schema with `scripts/t.sh php scripts/modgen/schema.php`.

## Testing
- Tests are PHPUnit classes under `tests/Feature/<Domain>/...`, using `RefreshDatabase` and the `userWith(...permissions)` helper. Assert Inertia responses with `AssertableInertia`.
- Every behavior change needs a feature test, including permission-denied and IDOR cases for anything customer-facing.
- Before saying work is done: `composer check` and `npm run check` must both pass.

## UI rules
- One shared palette across storefront, auth and back office (cocoa / caramel / cream, see `.cursorrules`), defined once as tokens in light and dark.
- Don't use stock shadcn looks: no slate/sky defaults, template heroes, purple gradients or emoji icons. Use `lucide-react` icons only.
- Keep a pastry-shop character, a bento-grid dashboard, and check every screen at phone width.

## Gotchas
- Many files on this branch are **untracked**, so git cannot restore them. Grep for usages before deleting anything.
- Never run destructive commands against existing Docker DB volumes (`pg_data`, legacy `db_data`).
- Production (Vercel) needs a hosted Postgres `DB_URL`. Without it, the app falls back to an ephemeral SQLite file in /tmp.
- Use `config()` and never `env()` outside `config/*.php` (it breaks under `config:cache`).
- Inertia SSR is intentionally disabled (`config/inertia.php`).

## Git
- Don't commit or push unless the user asks. Work stays in the working tree until milestone sign-off.
- Branch: `feature/inertia-react` → PRs target `main` (via `staging`).

## Session Handoff

When the user says "handoff" or when you detect the session is getting long
(> ~50 messages or you sense the context window is filling up), proactively
write or update `WORKING.md` in the project root with:

- **Task**: one-line summary of the goal
- **Done**: what's completed (files created/modified, decisions made)
- **Pending**: what's left, in order
- **Next step**: the exact next action to take
- **Key context**: any non-obvious constraints, gotchas, or file paths

Keep it under 40 lines. Overwrite the file each time (no appending).
If `WORKING.md` already exists, update it rather than creating a new one.

`WORKING.md` is the short, per-session scratch note. `docs/HANDOFF.md` remains the long-lived project state; update it when a phase changes.
