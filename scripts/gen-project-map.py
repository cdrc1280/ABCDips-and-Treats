#!/usr/bin/env python3
"""Generate the project map in docs/project/ — one Markdown file per area of the codebase.

    scripts/t.sh php artisan route:list --json --except-vendor > /tmp/routes.json
    python3 scripts/gen-project-map.py /tmp/routes.json

Each file has generated facts (files, models + columns + relations, routes + permissions, pages,
tests) between <!-- auto:start --> and <!-- auto:end -->, refreshed on every run. Everything
outside those markers (business flows, rules, the improvement audit) is hand-written and kept.
"""
import collections
import glob
import json
import os
import re
import sys

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
APP = os.path.join(ROOT, 'src')  # the Laravel + React app
OUT = os.path.join(ROOT, 'docs/project')
SCHEMA = json.load(open(os.path.join(APP, 'scripts/modgen/schema.json')))
START, END = '<!-- auto:start -->', '<!-- auto:end -->'

# slug, title, one-line purpose, extra php paths (besides app/Domains/<Domain>), extra test paths
DOMAINS = [
    ('access', 'Access', 'Roles, the permission registry and who may grant what.', [], []),
    ('analytics', 'Analytics', 'Back-office dashboard, report exports and the AI advisor.', ['app/Services/AnalyticsService.php', 'app/Services/AiAdvisorService.php', 'app/Services/ReportExportService.php', 'app/Http/Controllers/AdminReportController.php', 'app/Exports'], []),
    ('audit', 'Audit', 'Activity log of every model change (spatie/laravel-activitylog).', ['app/Shared/Concerns/LogsModelActivity.php', 'app/Services/AuditLogger.php'], []),
    ('catalog', 'Catalog', 'Categories, products (flavors, variations, allergens, nutrition, tags, gallery).', [], []),
    ('content', 'Content', 'Blog, FAQ, contact messages, suggestions and AI-chat escalations.', [], []),
    ('costing', 'Costing', 'Recipes (ingredients + packaging) and product costing sheets.', ['app/Services/UnitConverterService.php'], []),
    ('employee', 'Employee', 'Employees, attendance and payroll.', ['app/Services/PayrollService.php'], []),
    ('inventory', 'Inventory', 'Suppliers, ingredients, packaging materials and stock movements.', ['app/Services/InventoryService.php', 'app/Services/InventoryDeductionService.php'], []),
    ('menu', 'Menu', 'The back-office sidebar, stored in the database and filtered by permission.', [], []),
    ('pos', 'Point of Sale', 'In-store register: transactional sale, stock deduction, receipt.', [], []),
    ('preference', 'Preference', 'Per-user table preferences (columns, sorting, filters).', [], []),
    ('production', 'Production', 'Production batches that turn recipes into sellable stock.', ['app/Services/ProductionService.php'], []),
    ('purchasing', 'Purchasing', 'Supplier quotations, purchase orders and PO PDF import.', ['app/Services/PurchasingService.php', 'app/Services/PoPdfParserService.php'], []),
    ('review', 'Review', 'Product reviews, moderation and helpful votes.', ['app/Services/ReviewService.php', 'app/Http/Resources/ReviewResource.php'], []),
    ('sales', 'Sales', 'Orders, order pipeline, coupons, custom orders, delivery pools, invoices, payments.', ['app/Services/OrderService.php', 'app/Services/CustomOrderService.php', 'app/Services/CartService.php', 'app/Services/Payments', 'app/Services/PayMongoService.php', 'app/Services/LalamoveService.php', 'app/Services/GeocoderService.php', 'app/Contracts/PaymentGatewayInterface.php'], ['tests/Feature/DeliveryPoolingTest.php']),
    ('setting', 'Setting', 'Schema-driven store settings (secrets are write-only).', [], []),
    ('storefront', 'Storefront', 'The customer-facing shop: catalog, cart, checkout, tracking, account, content, chat.', [], []),
    ('user', 'User', 'Users, customers, Fortify authentication and the profile.', ['app/Rules/PhilippinePhone.php'], []),
]


def rel(path):
    return os.path.relpath(path, ROOT)


def link(path, label=None):
    r = rel(path)
    return f'[`{label or r}`](../../{r})'


def summary(php_path):
    """First sentence of the docblock right above the class/interface/trait/enum."""
    try:
        src = open(php_path).read()
    except (IsADirectoryError, FileNotFoundError):
        return ''
    m = re.search(r'/\*\*(.*?)\*/\s*(?:#\[[^\]]*\]\s*)*(?:final |abstract |readonly )*(?:class|interface|trait|enum) ', src, re.S)
    if not m:
        return ''
    text = ' '.join(l.strip(' *') for l in m.group(1).splitlines() if l.strip(' *') and not l.strip(' *').startswith('@'))
    return text.split('. ')[0].rstrip('.')[:160]


def php_files(paths):
    out = []
    for p in paths:
        full = os.path.join(APP, p)
        if os.path.isdir(full):
            out += sorted(glob.glob(os.path.join(full, '**/*.php'), recursive=True))
        elif os.path.exists(full):
            out.append(full)
    return out


def model_info(path):
    src = open(path).read()
    cls = os.path.basename(path)[:-4]
    t = re.search(r"protected \$table = '([^']+)'", src)
    table = t.group(1) if t else None
    if not table:  # Laravel default: snake plural (good enough for this codebase)
        snake = re.sub(r'(?<!^)(?=[A-Z])', '_', cls).lower()
        table = snake + ('es' if snake.endswith(('s', 'x', 'ch', 'sh')) else ('ies' if False else 's'))
        if snake.endswith('y') and not snake.endswith(('ay', 'ey', 'oy', 'uy')):
            table = snake[:-1] + 'ies'
        if table not in SCHEMA and snake in SCHEMA:
            table = snake
    rels = re.findall(r'public function (\w+)\(\)\s*:\s*(BelongsTo|HasMany|HasOne|BelongsToMany|MorphTo|MorphMany|MorphOne|HasManyThrough)\s*\{\s*return \$this->\w+\((\w+)?', src)
    traits = re.findall(r'^\s+use ([A-Z][\w, ]+);', src, re.M)
    return cls, table, rels, ', '.join(traits)


def domain_routes(routes, domain, extra_actions=()):
    rows = []
    for r in routes:
        action = r.get('action') or ''
        if f'App\\Domains\\{domain}\\' in action or any(a in action for a in extra_actions):
            mw = [m.split('\\')[-1] for m in r.get('middleware', [])]
            perm = next((m.split(':', 1)[1] for m in mw if m.startswith('can:') and m != 'can:dashboard-view'), '')
            access = perm or ('auth' if 'Authenticate' in mw or 'auth' in mw else ('staff' if r['uri'].startswith('admin') else 'public'))
            rows.append((r['method'].replace('|HEAD', ''), '/' + r['uri'].lstrip('/'), r.get('name') or '', action.split('\\')[-1], access))
    return rows


def render_map():
    out = {}
    for f in glob.glob(os.path.join(APP, 'app/**/Controllers/*.php'), recursive=True):
        src = open(f).read()
        cls = os.path.basename(f)[:-4]
        for m in re.finditer(r'public function (\w+)\(', src):
            nxt = src.find('public function ', m.end())
            c = re.search(r"Inertia::render\('([^']+)'", src[m.end(): nxt if nxt > 0 else len(src)])
            if c:
                out[f'{cls}@{m[1]}'] = c[1]
    return out


def permissions(domain):
    rows = []
    for f in sorted(glob.glob(os.path.join(APP, f'app/Domains/{domain}/Enums/*Permission.php'))):
        rows += re.findall(r"case \w+ = '([a-z0-9-]+)';", open(f).read())
    return rows


def auto_block(slug, title, purpose, domain, extra, extra_tests, routes, pages):
    lines = [START, '', f'_{purpose}_', '']
    files = php_files([f'app/Domains/{domain}'] + extra)
    # Files grouped by folder
    groups = collections.OrderedDict()
    for f in files:
        groups.setdefault(os.path.dirname(rel(f)), []).append(f)
    lines += ['### Files', '']
    for folder, fs in groups.items():
        lines.append(f'**{folder}/**')
        for f in fs:
            s = summary(f)
            lines.append(f'- {link(f, os.path.basename(f))}' + (f' — {s}' if s else ''))
        lines.append('')
    # Models
    models = [f for f in files if '/Models/' in f]
    if models:
        lines += ['### Data model', '']
        for f in models:
            cls, table, rels_, traits = model_info(f)
            cols = SCHEMA.get(table, [])
            lines.append(f'**`{cls}`** → table `{table}`' + (f' · traits: {traits}' if traits else ''))
            if cols:
                lines.append('- Columns: ' + ', '.join(f'`{c.split(":")[0]}`' + ('?' if c.endswith('?') else '') for c in cols))
            if rels_:
                lines.append('- Relations: ' + ', '.join(f'`{n}()` {k} `{t or "?"}`' for n, k, t in rels_))
            lines.append('')
    # Routes
    rows = domain_routes(routes, domain, extra_actions=tuple(os.path.basename(p)[:-4] + '@' for p in extra if p.endswith('Controller.php')))
    if rows:
        lines += ['### Routes', '', '| Method | URL | Route name | Action | Page | Access |', '|---|---|---|---|---|---|']
        for m, u, n, a, acc in rows:
            p = pages.get(a, '')
            lines.append(f"| {m} | `{u}` | `{n}` | `{a}` | {('`' + p + '`') if p else '—'} | {acc} |")
        lines.append('')
    perms = permissions(domain)
    if perms:
        lines += ['### Permissions', '', ', '.join(f'`{p}`' for p in perms), '']
    page_set = sorted({pages.get(a) for *_, a, _ in [(None, None, None, r[3], None) for r in rows] if pages.get(a)})
    page_files = [os.path.join(APP, f'resources/js/pages/{p}.tsx') for p in page_set]
    if page_files:
        lines += ['### Pages (React)', '', ', '.join(link(p, rel(p).replace('resources/js/pages/', '')) for p in page_files if os.path.exists(p)), '']
    tests = sorted(glob.glob(os.path.join(APP, f'tests/Feature/{domain}/**/*Test.php'), recursive=True)) + [os.path.join(APP, t) for t in extra_tests]
    if tests:
        lines += ['### Tests', '', ', '.join(link(t, os.path.basename(t)[:-4]) for t in tests), '']
    lines += [END]
    return '\n'.join(lines)


def write(slug, title, block, default_notes):
    path = os.path.join(OUT, f'{slug}.md')
    if os.path.exists(path):
        src = open(path).read()
        head, _, rest = src.partition(START)
        _, _, tail = rest.partition(END)
        src = head + block + tail
    else:
        src = f'# {title}\n\n{block}\n\n{default_notes}\n'
    open(path, 'w').write(src)


def main(route_json):
    os.makedirs(OUT, exist_ok=True)
    routes = json.load(open(route_json))
    pages = render_map()
    stub = '## Business flows\n\n_To be written._\n\n## Rules & invariants\n\n_To be written._\n\n## Improvement audit\n\n_To be written._'
    index = ['# ABCDips & Treats — project map', '',
             '_One file per area. Generated sections are refreshed by `scripts/gen-project-map.py`; the flows, rules and audit are hand-written._', '',
             '| Area | Purpose |', '|---|---|']
    for slug, title, purpose, extra, extra_tests in DOMAINS:
        write(slug, title, auto_block(slug, title, purpose, title.replace(' ', '') if title != 'Point of Sale' else 'Pos', extra, extra_tests, routes, pages), stub)
        index.append(f'| [{title}]({slug}.md) | {purpose} |')
    for slug, title, purpose in [('frontend', 'Frontend', 'React + Inertia shells, components, design tokens, Wayfinder routes.'),
                                 ('database', 'Database', 'PostgreSQL schema, UUID keys, migrations, seeders.'),
                                 ('platform', 'Platform & infrastructure', 'Shared kernel, middleware, Docker, Vercel, tooling and quality gates.'),
                                 ('flows', 'End-to-end business flows', 'How back office and storefront work together, flow by flow, with the tests that prove it.'),
                                 ('improvements', 'Improvement audit', 'Everything found wrong or weak, prioritised, with status.')]:
        index.append(f'| [{title}]({slug}.md) | {purpose} |')
    open(os.path.join(OUT, 'README.md'), 'w').write('\n'.join(index) + '\n')
    print(f'wrote {len(DOMAINS)} domain files + README to docs/project/')


if __name__ == '__main__':
    main(sys.argv[1])
