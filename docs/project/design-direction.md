# Design direction: "Bakery counter" (decided 2026-11-17)

The palette, tokens and fonts from Phase 8 stay (cocoa / caramel / cream, Figtree + Young Serif).
What changes is *what fills the screen*: the generic feel comes from repeated templates, not colours.

## Customer side: the idea
A real Cavite home bakery's counter: kraft boxes, parchment, handwritten-style price tags, photos of the actual loaves.
Food sells on photos, so **real photography leads every screen**; type and texture support it.

1. **Photos first.** `public/images/textures/*` has real loaf, slice, box, crumb and crust shots. Use them for the hero,
   the banana-bread products and category tiles. Products without a photo get a designed fallback:
   parchment token + dashed label edge (CSS only; the `textures/kraft|parchment` JPGs are not usable as tiles), a Young Serif caption
   (category name on cards since the title sits below, product name on the product page), a lucide category icon. **Never the logo repeated in a grid.**
   Built as `<ProductPhoto>` in `components/storefront/product-card.tsx`; storefront `image_url` is null when there is no photo.
2. **No eyebrow template.** Drop the "TINY UPPERCASE EYEBROW / centered serif H1 / grey subtitle" header that 8 pages share.
   Use left-aligned headers. Each page gets one distinct element (a menu board for shop, a receipt for cart, a ticket for track).
3. **Plain bakery voice.** "The menu", not "Explore Our Bakery Collection". "Your basket", not "Your Pastry Basket".
   Short, specific, Taglish-friendly. Run every page against the banned list in `.cursorrules`.
4. **Product cards as bakery labels.** Photo, name, price on a tag, one "Add" button. No three badges plus a heart plus a timer at once.
5. **Phones are the main target** (Facebook/Messenger traffic). Thumb-reach CTAs, sticky "Add to basket" on product pages,
   and the chat bubble must not cover CTAs or bottom bars.

## 3D, motion and scrolling
- **Home hero: remove the three.js loaf.** It costs ~520 KB of JS above the fold. On phones it's slow and drains battery,
  and a procedural loaf looks fake next to the real photos we have. The lighting-mode toggles are a gimmick customers don't need.
  Replace it with a real photo hero.
- **Keep 3D in one place only:** the signature banana-bread product page, as an opt-in "Turn the loaf" button.
  Show a static photo poster, load three.js on click only, desktop/tablet only (≥768px), and never auto-load.
  If analytics later show nobody uses it, delete it.
- **Never 3D in:** shop/listing grids, cart, checkout, account, auth, or anywhere in the back office.
- **Lenis smooth scroll: remove.** It hijacks native scrolling, which hurts on phones and with trackpads. Use native scroll.
- **GSAP: removed** (it was installed but unused). Motion is the CSS `Reveal` component: one-time fade/slide, 300 ms, off under `prefers-reduced-motion`.
- Done 2026-11-17: Lenis and GSAP uninstalled, `.cursorrules` §1 updated, 3D lives in `components/storefront/turn-the-loaf.tsx` (dialog) + `banana-bread-showcase.tsx` (stage), enabled per product by `Product::SLUGS_WITH_3D_MODEL`.

## Back office: the idea
A working tool, not a showcase. Dense, calm, fast on a phone at the counter.
1. Index pages on phones: a "Filters" button opening a sheet (not a full screen of selects), and **card rows instead of tables**
   (name, key number, status, one action menu). Drop the S.No column.
2. Stat cards only where the number drives a decision (low stock, unpaid orders, today's sales), not on every module.
3. Create/edit on phones: form first, preview after it (or collapsed).
4. POS stays as fixed on 2026-11-17 (jump-to-sale bar, wrapping lines).

## Order of work
1. Customer: photo hero replacing 3D, product-card label design + designed fallback, attach real photos to banana-bread products (seeder).
2. Customer: page headers + copy pass on all storefront pages; chat-bubble offset; sticky add-to-basket on the product page.
3. 3D move: opt-in "Turn the loaf" on the banana-bread product page; remove Lenis; trim GSAP; update `.cursorrules`.
4. Back office: mobile filters sheet, card rows, stat-card trim, form-first on phones.
5. After each step: `scripts/ui-audit/run.sh pages` / `forms`, then `composer check` + `npm run check`.
