# ABCDips & Treats — project map

_One file per area. Generated sections are refreshed by `scripts/gen-project-map.py`; the flows, rules and audit are hand-written._

| Area | Purpose |
|---|---|
| [Access](access.md) | Roles, the permission registry and who may grant what. |
| [Analytics](analytics.md) | Back-office dashboard, report exports and the AI advisor. |
| [Audit](audit.md) | Activity log of every model change (spatie/laravel-activitylog). |
| [Catalog](catalog.md) | Categories, products (flavors, variations, allergens, nutrition, tags, gallery). |
| [Content](content.md) | Blog, FAQ, contact messages, suggestions and AI-chat escalations. |
| [Costing](costing.md) | Recipes (ingredients + packaging) and product costing sheets. |
| [Employee](employee.md) | Employees, attendance and payroll. |
| [Inventory](inventory.md) | Suppliers, ingredients, packaging materials and stock movements. |
| [Menu](menu.md) | The back-office sidebar, stored in the database and filtered by permission. |
| [Point of Sale](pos.md) | In-store register: transactional sale, stock deduction, receipt. |
| [Preference](preference.md) | Per-user table preferences (columns, sorting, filters). |
| [Production](production.md) | Production batches that turn recipes into sellable stock. |
| [Purchasing](purchasing.md) | Supplier quotations, purchase orders and PO PDF import. |
| [Review](review.md) | Product reviews, moderation and helpful votes. |
| [Sales](sales.md) | Orders, order pipeline, coupons, custom orders, delivery pools, invoices, payments. |
| [Setting](setting.md) | Schema-driven store settings (secrets are write-only). |
| [Storefront](storefront.md) | The customer-facing shop: catalog, cart, checkout, tracking, account, content, chat. |
| [User](user.md) | Users, customers, Fortify authentication and the profile. |
| [Frontend](frontend.md) | React + Inertia shells, components, design tokens, Wayfinder routes. |
| [Database](database.md) | PostgreSQL schema, UUID keys, migrations, seeders. |
| [Platform & infrastructure](platform.md) | Shared kernel, middleware, Docker, Vercel, tooling and quality gates. |
| [End-to-end business flows](flows.md) | How back office and storefront work together, flow by flow, with the tests that prove it. |
| [Improvement audit](improvements.md) | Everything found wrong or weak, prioritised, with status. |
| [Design direction](design-direction.md) | "Bakery counter" UI direction: photos first, where 3D and motion are (and are not) used, back-office phone layout, order of work. |
