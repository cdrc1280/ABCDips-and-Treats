#!/usr/bin/env python3
"""Regenerate docs/MODULES_AND_PAGES.md from the live route table and the code tree.

    scripts/t.sh php artisan route:list --json --except-vendor > /tmp/routes.json
    python3 scripts/gen-modules-doc.py /tmp/routes.json

Module descriptions live in MODULES below; everything else (routes, pages, permissions,
code folders, models, tests) is read from the repository, so the doc can't drift.
"""
import glob
import json
import os
import re
import sys

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
APP = os.path.join(ROOT, 'src')  # the Laravel + React app

# title, blurb, storefront route-name prefixes, admin route-name keys, domain folders
MODULES = [
    ('Accounts & Access', 'The customer account area (orders, cancellation, wishlist, profile, avatar), staff accounts and roles. Sign-in pages are listed under Fortify at the end.',
     ['account'], ['users', 'roles'], ['User', 'Access']),
    ('Product Catalog', 'Categories, products (flavors, variations, allergens, nutrition, tags, gallery) and the curated collections.',
     ['home', 'shop', 'category', 'products', 'collection'], ['categories', 'products'], ['Catalog']),
    ('Cart & Coupons', 'Session-token cart (no rows written on read), undo-able removal, batch edits, coupons and the guest-cart merge on login. Prices are decided only on the server.',
     ['cart', 'wishlist'], ['coupons'], ['Sales/Models/Cart*', 'Sales/Models/Coupon*']),
    ('Checkout, Payments & Orders', 'Checkout with a server-side delivery quote, PayMongo e-wallet payments (session-bound return, amount-checked capture), order confirmation, tracking by token, invoices and the back-office order pipeline.',
     ['checkout', 'orders', 'order'], ['orders', 'notifications'], ['Sales', 'Storefront']),
    ('Delivery Pooling', 'Group delivery pools: pooled orders wait for the admin to settle a shared fee before payment.',
     [], ['delivery-pools'], ['Sales/Models/DeliveryPool*']),
    ('Custom Orders', 'Custom cake inquiries from the storefront and their production workflow.',
     ['custom-orders'], ['custom-orders'], ['Sales/Models/CustomOrder*']),
    ('Reviews', 'Product reviews with moderation, helpful votes and verified-buyer flags.',
     ['reviews'], ['reviews'], ['Review']),
    ('Customers', 'Customer records for staff.', [], ['customers'], ['User']),
    ('Point of Sale', 'Transactional in-store register; refuses overselling and underpayment.', [], ['pos'], ['Pos']),
    ('Inventory & Supplies', 'Suppliers, ingredients, packaging materials and their stock movements.', [], ['suppliers', 'ingredients', 'packaging-materials'], ['Inventory']),
    ('Recipes & Product Costing', 'Recipes (ingredients + packaging) and computed product costing.', [], ['recipes', 'product-costings'], ['Costing']),
    ('Production & Purchasing', 'Production batches, supplier quotations, purchase orders and PO PDF import.', [], ['production-batches', 'quotations', 'purchase-orders'], ['Production', 'Purchasing']),
    ('HR & Payroll', 'Employees, attendance and payroll with computed items.', [], ['employees', 'attendances', 'payrolls'], ['Employee']),
    ('Content & Engagement', 'Blog, FAQ, about and legal pages, contact messages, suggestions, and AI chat with escalation to staff.',
     ['about', 'faq', 'contact', 'suggestions', 'blog', 'legal', 'chat'], ['blog-posts', 'suggestions', 'contact-messages', 'chat-escalations'], ['Content']),
    ('Analytics, Reports & AI', 'Dashboard (deferred charts and lists), report exports (PDF, Excel, CSV, Word) and the internal AI advisor.',
     [], ['dashboard', 'reports', 'ai-advisor'], ['Analytics']),
    ('Store Configuration & Audit', 'Schema-driven store settings (secrets are write-only), the activity log and per-user table preferences.',
     [], ['settings', 'activity-log', 'preferences'], ['Setting', 'Audit', 'Preference', 'Menu']),
]

FORTIFY = [
    ('GET', 'login', 'auth/login'), ('GET', 'register', 'auth/register'), ('GET', 'forgot-password', 'auth/forgot-password'),
    ('GET', 'reset-password/{token}', 'auth/reset-password'), ('GET', 'email/verify', 'auth/verify-email'),
    ('GET', 'user/confirm-password', 'auth/confirm-password'), ('GET', 'two-factor-challenge', 'auth/two-factor-challenge'),
]


def render_map():
    """'Controller@method' -> Inertia page component, read from the controllers."""
    out = {}
    for f in glob.glob(os.path.join(APP, 'app/**/Controllers/*.php'), recursive=True):
        src = open(f).read()
        cls = os.path.basename(f)[:-4]
        for m in re.finditer(r'public function (\w+)\(', src):
            nxt = src.find('public function ', m.end())
            body = src[m.end(): nxt if nxt > 0 else len(src)]
            c = re.search(r"Inertia::render\('([^']+)'", body)
            if c:
                out[f'{cls}@{m[1]}'] = c[1]
    return out


def route_rows(routes, pages, pred):
    rows = []
    for r in routes:
        name = r.get('name') or ''
        if not pred(name):
            continue
        action = (r.get('action') or '').split('\\')[-1]
        method = r['method'].replace('|HEAD', '')
        mw = [m.split('\\')[-1] for m in r.get('middleware', [])]
        perm = next((m.split(':', 1)[1] for m in mw if m.startswith('can:') and m != 'can:dashboard-view'), '')
        auth = 'auth' if any(m in ('auth', 'Authenticate') for m in mw) else ''
        rows.append((method, '/' + r['uri'].lstrip('/'), name, pages.get(action, ''), perm or auth))
    return rows


def table(rows, admin):
    if not rows:
        return ''
    head = '| Method | URL | Route | Page | Access |\n|---|---|---|---|---|\n'
    return head + '\n'.join(
        f"| {m} | `{u}` | `{n}` | {('`' + p + '`') if p else '—'} | {a or ('staff' if admin else 'public')} |" for m, u, n, p, a in rows) + '\n'


def code_list(folders):
    items = []
    for d in folders:
        for path in sorted(glob.glob(os.path.join(APP, 'app/Domains', d))):
            rel = os.path.relpath(path, ROOT)
            if os.path.isdir(path):
                models = sorted(os.path.basename(p)[:-4] for p in glob.glob(os.path.join(path, 'Models/*.php')))
                items.append(f"[`{rel}/`]({'../' + rel}/)" + (f" — models: {', '.join('`' + m + '`' for m in models)}" if models else ''))
            else:
                items.append(f"[`{rel}`]({'../' + rel})")
    return items


def tests_for(folders):
    names = set()
    for d in folders:
        top = d.split('/')[0]
        for f in glob.glob(os.path.join(APP, 'tests/Feature', top, '**/*Test.php'), recursive=True):
            names.add(os.path.relpath(f, ROOT))
    return sorted(names)


def main(path):
    routes = json.load(open(path))
    pages = render_map()
    out = ['# ABCDips & Treats — Modules & Pages Reference', '',
           '_Generated by `scripts/gen-modules-doc.py` from the route table and the code tree. Edit the module descriptions in that script, not this file._', '',
           '- **Stack:** PHP 8.3, Laravel 13, Inertia v3, React 19 + TypeScript, Tailwind + shadcn/ui, spatie/laravel-data, Fortify, Wayfinder.',
           '- **Shells:** the storefront (`resources/views/app.blade.php`, `resources/js/storefront.tsx`) and the back office under `/admin` (`resources/views/admin.blade.php`, `resources/js/app.tsx`).',
           '- **Access:** Spatie permissions. Every `/admin` route needs `dashboard-view` plus the per-module permission shown below; `super_admin` passes every check.',
           '- **Pages:** `resources/js/pages/<page>.tsx`. Routes for the frontend are typed via Wayfinder (`resources/js/routes`, `resources/js/actions`).', '']
    out += ['## Contents', '']
    for i, (title, *_) in enumerate(MODULES, 1):
        out.append(f'{i}. [{title}](#{re.sub(r"[^a-z0-9 -]", "", title.lower()).replace(" ", "-")})')
    out += [f'{len(MODULES) + 1}. [Authentication pages (Fortify)](#authentication-pages-fortify)', '']

    for title, blurb, store, admin, folders in MODULES:
        out += [f'## {title}', '', blurb, '']
        srows = route_rows(routes, pages, lambda n, s=store: any(n == p or n.startswith(p + '.') for p in s))
        arows = route_rows(routes, pages, lambda n, a=admin: any(n == f'admin.{p}' or n.startswith(f'admin.{p}.') for p in a))
        if srows:
            out += ['**Storefront**', '', table(srows, False)]
        if arows:
            out += ['**Back office**', '', table(arows, True)]
        code = code_list(folders)
        if code:
            out += ['**Code:** ' + '; '.join(code), '']
        tests = tests_for(folders)
        if tests:
            out += ['**Tests:** ' + ', '.join(f'[`{os.path.basename(t)[:-4]}`](../{t})' for t in tests), '']

    out += ['## Authentication pages (Fortify)', '', 'Served by Laravel Fortify with React pages; registration creates a `customer`.', '',
            '| Method | URL | Page |', '|---|---|---|']
    out += [f'| {m} | `/{u}` | `{p}` |' for m, u, p in FORTIFY if os.path.exists(os.path.join(APP, f'resources/js/pages/{p}.tsx'))]
    out.append('')
    open(os.path.join(ROOT, 'docs/MODULES_AND_PAGES.md'), 'w').write('\n'.join(out))
    print('wrote docs/MODULES_AND_PAGES.md')


if __name__ == '__main__':
    main(sys.argv[1])
