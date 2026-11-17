# Handoff — Inertia/React rewrite (branch `feature/inertia-react`)

_Last updated: 2026-11-17. Rewrite is LIVE: merged to `main` (PR #6, dd87a9c), deployed on Vercel (Root Directory `src`, region sin1) with Neon Postgres in Singapore. Production setup/migrations: `scripts/prod-db.sh` (`migrate` after deploys that add migrations). Open: owner adds real products._

> **Layout (2026-11-17):** the Laravel + React app moved into `src/`. Code paths in this file are relative to `src/`. Docker build: `docker/app.Dockerfile` (context = repo root). Vercel needs Root Directory = `src` once this ships to `main`.

## Goal
Rebuild ABCDips & Treats as **Laravel 13 + Inertia v3 + React 19 + TypeScript**, with domain-driven folders under `app/Domains/*`, spatie/laravel-data, Fortify, Wayfinder and shadcn. **No Filament, no Vue.**

## Status
| Phase | State |
|---|---|
| 1. Foundation (Shared, Access, Menu, Preference, Fortify auth pages, admin shell) | ✅ done |
| 2. Back office: 31 modules, plus dashboard, POS, AI advisor, settings, activity log, PO PDF import | ✅ done |
| 3. Storefront in React | ✅ done. Lint, Prettier and build are clean; an HTTP smoke test of every page plus register → cart → checkout (pickup + GCash sandbox) → account passed. A visual check in a real browser is still worth doing |
| 4. Removal | ✅ done (2026-11-16): Filament, Livewire, Vue, the UI REST API and the SPA catch-all are gone |
| 5. Quality gates | ✅ done (2026-11-16): Pint, Larastan level 7 (0 errors, no baseline), tsc, ESLint, Prettier, `composer audit` 0, `npm audit` 0 |
| 6. Docs + code graph | ✅ done (2026-11-16) |
| 7. PostgreSQL + UUID keys | ✅ done (2026-11-16, user-approved) |
| 8a. Project map (`docs/project/`) + business-logic audit fixes (A1–A15) | ✅ done (2026-11-16), 359 tests |
| 8b. Full UI redesign ("Bakery counter", `docs/project/design-direction.md`) | ✅ done (2026-11-17), 375 tests. Photo hero, label cards, page headers + copy, opt-in 3D, Lenis/GSAP removed, back-office phone layouts |

**Tests: 383 passing** (`scripts/t.sh php vendor/bin/phpunit`). The old API/Filament/token-auth tests were deleted; the price-tampering and pooling-payment tests were ported to the new endpoints.

**One-command gates:** `scripts/t.sh composer check` (Pint, Larastan, PHPUnit) and `scripts/t.sh npm run check` (ESLint, Prettier, tsc). Use PHPUnit rather than `php artisan test` in the tools container: the latter tries to read a `.env` file that isn't there and reports every test as a warning. (`scripts/t.sh php vendor/bin/phpunit`). `tsc` is clean.

## How to run
- **Tools container** (PHP 8.3 + Node 22): `scripts/t.sh <cmd>`, e.g.:
  - `scripts/t.sh php vendor/bin/phpunit`
  - `scripts/t.sh npx tsc --noEmit`
  - `scripts/t.sh npx eslint resources/js`
  - `scripts/t.sh npx prettier --write …`
  - `scripts/t.sh php artisan wayfinder:generate --with-form`

  Host `npx` points at the Windows npm and doesn't work; always use `t.sh`.
- **Prod-like:** `docker compose up -d` → http://localhost:8000 (admin: `admin@abcdips.test` / `Password123!`).
- **Dev with HMR:** `scripts/dev.sh` → http://localhost:8001, with Vite on 5174. Port 5173 belongs to the user's other project, "cloutia"; don't touch it.
- **Module generator:** `scripts/modgen/modgen.py` + `specs.py`. Refresh the schema with `scripts/t.sh php scripts/modgen/schema.php`.

## Storefront map (phase 3)
- **Backend:** `app/Domains/Storefront/{Controllers,Data,Queries,Resources,Support,Listeners}` and `routes/storefront/*.php`. `routes/web.php` globs these files before the legacy SPA catch-all.
  - **Cart:** `Support/CurrentCart`. The token lives in the session, and reading never creates a row. The shared Inertia prop `cart` is set in `HandleInertiaRequests`, along with `store` and `wishlist_ids`. The `MergeGuestCart` listener (registered in `AppServiceProvider`) merges the guest cart on Login.
  - **Prices:** decided only in `Support/CartPricing` and `Support/CustomCakePricing`.
  - **Checkout:** `Controllers/CheckoutController` gets the fee from `Support/DeliveryQuoter` (Lalamove, or a distance estimate, cached for 10 minutes per point). The PayMongo return is tied to the session, and capture goes through `PayMongoService::captureSource` with an amount check.
- **Frontend:**
  - Pages: `resources/js/pages/storefront/*.tsx` and `resources/js/pages/account/*.tsx`.
  - Shell: `layouts/storefront-layout.tsx`.
  - Components: `components/storefront/*` (nav, footer, cart drawer and parts, product card and quick view and purchase panel, reviews, chat widget, PSGC address fields, Leaflet map picker, 3D showcase via `lib/banana-bread-scene.js`).
  - Helpers: `lib/http.ts` (JSON + XSRF), `hooks/use-cart.ts`.
- **Tests:** `tests/Feature/Storefront/**`.

## Phase 4 notes (2026-11-16)
- **Invoices:** `app/Domains/Sales/Controllers/InvoiceController.php` (`/order-invoice/{order}[/download]`). Staff access is now the `orders-view` permission instead of hard-coded role names; the token check uses `hash_equals`; the download link inside the invoice used to point at the removed `/api/...` URL.
- **New-order bell:** `Sales/Notifications/NewOrderPlaced` (database channel) is sent from `OrderService::createOrderFromCart` to everyone with `orders-view` plus super admins. `User/Controllers/NotificationController` + `routes/admin/notifications.php`. The shared prop `notifications.unread` and `components/custom/notification-bell.tsx` in the admin header poll every 60 s. Old Filament rows still display; their `/filament` links are dropped.
- **Deleted:** `app/Filament`, the Filament provider/views/tests and published assets in `public/`, `routes/api.php`, `app/Http/Controllers/Api`, `app/Http/Requests/Api`, unused `app/Http/Resources/*` (`ReviewResource` stays), the legacy-only `Costing/Customer/Product/PsgcService`, every `.vue` file plus `router/ stores/ composables/ directives/ plugins/ legacy-spa.js spa.blade.php welcome.blade.php`. Packages removed: `filament/filament` (and Livewire), plus npm `vue vue-router pinia @vitejs/plugin-vue @vueuse/core lucide-vue-next axios`.
- **Kept on purpose:** `laravel/sanctum` + `HasApiTokens` (unused now; safe to remove later), and `vercel.json` (its `api/index.php` is Vercel's PHP entry, not the Laravel API).
- `.cursorrules` now matches the React stack (updated 2026-11-16 at the user's request).

## Security and logic bugs fixed during the rewrite (tell the user / keep fixed)
- **Cart and pricing:**
  - Cart price tampering: client-sent `unit_price` and modifiers were trusted.
  - An expired sale price was still charged (`Product::effective_price`).
- **Checkout and payments:**
  - Priority delivery fee was always ₱0, because the fee was taken from the client and stripped on save.
  - The payment success URL could be forged by passing another order's id/source. Any non-production environment marked orders paid with no check at all.
  - PayMongo "chargeable" sources were never captured, so customers were never actually charged.
  - Customer cancel was neither transactional nor locked, so stock could be restocked twice.
- **Data exposure:**
  - Order tracking IDOR: `/api/orders/track/{id|number}` exposed customer PII.
  - Chat: any guest thread was readable by email, and clients could inject "admin" messages.
  - The public AI chat leaked revenue, order counts and low-stock ingredients.
- **Account:**
  - `POST /api/customer/verify-email` self-verified an email without a token.
  - The fake phone OTP (no SMS is ever sent) also marked the email as verified.
- **Content:** blog HTML is now sanitized (`Support/SafeHtml`, symfony/html-sanitizer). Scheduled posts were showing early.
- **Config:** Inertia SSR was on by default with no SSR bundle; it's now off in `config/inertia.php`.

Earlier (phases 1–2):
- AI keys read via `env()` broke under `config:cache`.
- The PO PDF parser silently fell back to the wrong supplier or ingredient.
- Quotation → PO silently fell back too.
- POS had no transaction, allowed overselling and accepted underpayment.
- Settings secrets were sent to the browser.

## Phase 5 notes (2026-11-16)
- **Larastan:** `phpstan.neon` at level 7. The only global ignore is `missingType.iterableValue` (array shapes in Data rules/messages). Relations carry `@return Rel<Target, $this>` docblocks. `Shared/Support/RouteModel::key()` replaces `request()->route('x')?->id` in Data rules. List-returning Collection chains are typed `array<int, …>`, because Larastan never infers `list` from `Collection::values()->all()`.
- **Generator** (`scripts/modgen/modgen.py`) emits the same patterns now and prunes unused private static helpers, so regenerated modules stay Larastan-clean. This was verified on `supplier` against a snapshot, and the tree was restored afterwards.
- **Dependencies:** `maatwebsite/excel` was pinned `^3.1.70` (with `"*"` it had jumped to 4.x); phpspreadsheet is 1.30.8. Every `"*"` constraint is now pinned to its installed line. `laravel/sanctum`, `HasApiTokens`, `statefulApi()` and the token `AuthService` were removed; the `personal_access_tokens` migration stays so existing databases aren't touched. npm has an `overrides` entry pinning typescript-estree's `minimatch` to `^11.0.8`. `axios` remains in `node_modules` only as a dependency of `@inertiajs/core`. `vue` was removed from the lockfile (it was only an optional peer of `lenis`).
- **Bundle:** `components/custom/menu-icon.tsx` is a static map of the 37 menu icons; add a line when a menu migration uses a new icon. The admin `app-*.js` went from 491 kB to 206 kB. three.js (529 kB) is the only chunk over Vite's warning, and it's lazy-loaded.
- **Docs:** `docs/MODULES_AND_PAGES.md` is generated: `scripts/t.sh php artisan route:list --json --except-vendor > /tmp/r.json && python3 scripts/gen-modules-doc.py /tmp/r.json`.

## Phase 7 notes: PostgreSQL + UUIDs (2026-11-16)
- **Why it was safe:** the Vercel project had no env vars, so production ran on SQLite in `/tmp`, which is wiped on every cold start. Local MySQL held only the seeded admin. No real data existed to migrate.
- **Infra:** `docker-compose.yml` `db` is `postgres:17-alpine` on the new `pg_data` volume. The old `db_data` MySQL volume is kept, untouched and unused. The Dockerfile and tools image use `pdo_pgsql`. `config/database.php` defaults to `pgsql`. The entrypoint DB checks go through Laravel, so `DB_URL` works too. `vercel.json` uses `pgsql` + `DB_SSLMODE=require` and **needs a hosted Postgres `DB_URL` in Vercel before the next deploy**.
- **Schema:** the migrations were converted in place. `uuid('id')->primary()`, `foreignUuid`, `uuidMorphs` and `nullableUuidMorphs` are used everywhere. Integer IDs remain only on jobs/failed_jobs, cache, media (the media library names its folders by ID) and activity_log. The `personal_access_tokens` migration was deleted. `menus.parent_id` gets its FK in a second statement, because Laravel emits a fluent `->primary()` after inline FKs. `costing_items.unit` is now `string(20)`, validated against `UnitConverterService::isKnownUnit()`; it was an enum that rejected `pcs`.
- **Models:** all 47 use `HasUuids`, giving ordered UUIDv7 keys. There's an app `Permission` model (config/permission.php), and the code must never import `Spatie\Permission\Models\*` directly. `Relation::enforceMorphMap()` uses short aliases (`user`, `order`…) for every model.
- **Postgres specifics handled:** `whereLike()` everywhere (Postgres `LIKE` is case-sensitive). `'bail', 'uuid'` comes before every `exists` on an ID, because a non-UUID string reaching Postgres throws instead of failing validation. JSON columns are searched via `CAST(... AS TEXT)`. `FreesUniqueValuesOnDelete` sizes its `~deleted-` suffix to the column's real length.
- **Tests run on Postgres** (`abcdips_test` database in the compose container; `scripts/t.sh` joins the compose network). A test was added that an owner can open their own order: an int-vs-UUID comparison briefly 404'd owners during the conversion.
- **Generator:** `relation` fields now emit UUID strings, rules, filters and TS types, and nullability is read from `schema.json` (refresh with `scripts/t.sh php scripts/modgen/schema.php`). Verified by regenerating `ingredient` against a snapshot, then restoring.
- The unused `OrderService::getOrderByTrackingToken()` (by ID/number, the old IDOR) was deleted.

## Next steps (in order)
1. **Owner:** confirm the payroll pay-policy defaults in Settings → "Payroll rates and pay policy" (313 working days, OT +25%, holiday +100%, deduct absences and late minutes from 08:00, 0 min grace).
2. Review and commit. Nothing has been committed on `feature/inertia-react`.
3. Add a hosted Postgres `DB_URL` in Vercel.
4. Optional: re-run `/graphify` (`graphify-out/` still lists the 20 deleted summary files).

## 2026-11-17 follow-ups
- **A16 payroll ↔ attendance:** `PayPolicy` (settings) + `PayrollService::attendancePay()`; `PayLine::totals()` is the one gross/net formula for generation and manual edits. Migration `2026_11_17_000001` adds `holiday_pay`, `attendance_deduction`, `attendance_note` to `payroll_items`. Tests: `PayrollAttendanceTest`.
- **Unknown-URL crash:** an unmatched URL's 404 skipped the `web` middleware, so the storefront nav read `auth.user` of undefined. `Route::fallback(fn () => abort(404))` in `routes/web.php`; test `UnknownUrlTest`.
- **Cleanup:** the 10 unused `*SummaryQuery` classes and `schemas/<module>/summary.ts` are deleted. `modgen` stat cards are opt-in (non-empty `summary` in the spec; `specs.py` empties it for those modules) and the index template now uses `PageHeader`.

## Phase 8b notes (2026-11-17)
- Storefront: `<ProductPhoto>` (storefront `image_url` is null without a real photo; admin/POS/cart keep the logo), `parchment`/`kraft` tokens, left-aligned `PageHeader` with an `aside` slot, copy defaults live in BOTH `StorefrontContent.php` and `StoreSettingsSchema.php`.
- `Setting::get` no longer caches the caller's default (it hid code-default changes for an hour); cache key is `setting:v2:*`.
- 3D: `turn-the-loaf.tsx` dialog on products in `Product::SLUGS_WITH_3D_MODEL`, ≥768px, three.js fetched on click only (verified).
- Back office phones (<640px): `TableFilter` → Filters bottom sheet; `DataTable` → card rows (first column + up to 3 priority-1 columns, no S.No, no sorting on phones); compact `StatCards`; `tabGridClass` puts the form before the preview below lg.
- `scripts/ui-audit/run.sh pages|forms` is the screenshot audit against :8000 (rebuild the app container first).

## Gotchas
- **Moved models:** never `git checkout` a moved model file; it restores the old namespace. The morph map keeps the old `App\Models\*` names.
- **Wayfinder:** the route directory is the kebab-case route-name segment, and top-level names (`home`, `shop`, `category`, `login`) live in `@/routes`.
- **Docker:** the local Docker store's `php:8.3-cli` layers are corrupted, so use the bookworm images. Never run `docker compose down -v`.
- **Database:** only non-destructive DB actions. The local MySQL `abcdips` has no catalog data. For smoke tests, run the tools image with `DB_CONNECTION=sqlite DB_DATABASE=/tmp/smoke.sqlite` and `php artisan migrate --seed`, then `php artisan serve` inside the container.
- **Untracked files:** most new files are untracked, so `rm` can't be undone with git. Grep for a file's name before deleting it.
