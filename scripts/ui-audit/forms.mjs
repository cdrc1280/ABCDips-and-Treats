import { chromium } from 'playwright';
const BASE = 'http://localhost:8000', W = 375;
const ids = JSON.parse(process.env.IDS);
const creates = ['attendances','blog-posts','categories','coupons','custom-orders','delivery-pools','employees','ingredients','packaging-materials','payrolls','product-costings','production-batches','products','purchase-orders','quotations','recipes','reviews','roles','suppliers','users'].map(m => `/admin/${m}/create`);
const edits = [['products','products'],['categories','product_categories'],['employees','employees'],['suppliers','suppliers'],['orders','orders'],['roles','roles'],['users','users']].map(([m,t]) => `/admin/${m}/${ids[t]}/edit`);
const extra = ['/admin/settings', '/admin/payrolls', '/admin/blog-posts', '/checkout', '/contact', '/custom-orders', '/track', '/suggestions', '/forgot-password'];
const issues = [];
const theme = process.env.THEME || 'light';
const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width: W, height: 812 }, colorScheme: theme, deviceScaleFactor: 1 });
await ctx.addCookies([{ name: 'appearance', value: theme, url: BASE }]);
const page = await ctx.newPage();
page.on('pageerror', e => issues.push(`pageerror @ ${page.url()}: ${e.message.slice(0,160)}`));
const overflow = () => page.evaluate((W) => {
  const out = [];
  const clipped = (el) => { for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) { const o = getComputedStyle(p).overflowX; if (o !== 'visible') return true; } return false; };
  for (const el of document.querySelectorAll('body *')) {
    const r = el.getBoundingClientRect();
    if (r.width === 0 || r.right <= W + 1 || clipped(el) || el.closest('[data-sonner-toaster]')) continue;
    const s = getComputedStyle(el); if (s.position === 'fixed' && s.visibility === 'hidden') continue;
    out.push(`${el.tagName.toLowerCase()}.${String(el.className).split(' ').slice(0,3).join('.')} right=${Math.round(r.right)} "${(el.textContent||'').trim().slice(0,30)}"`);
  }
  for (const el of document.querySelectorAll('button, a, input, select, [role=button]')) {
    const r = el.getBoundingClientRect(); if (r.width === 0 || r.height === 0) continue;
    for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) {
      const o = getComputedStyle(p).overflowX; if (o === 'visible') continue;
      if (o === 'auto' || o === 'scroll') break; // user can scroll to it
      const pr = p.getBoundingClientRect(); if (r.right > pr.right + 1) out.push(`CLIPPED ${el.tagName.toLowerCase()} "${(el.getAttribute('aria-label')||el.textContent||'').trim().slice(0,25)}" right=${Math.round(r.right)} > ${Math.round(pr.right)}`); break;
    }
  }
  for (const d of document.querySelectorAll('[role=dialog],[role=alertdialog]')) { const dr = d.getBoundingClientRect();
    for (const el of d.querySelectorAll('*')) { const r = el.getBoundingClientRect(); if (r.width && r.right > dr.right + 1) { out.push(`DIALOG-OVERFLOW ${el.tagName.toLowerCase()} "${(el.textContent||'').trim().slice(0,25)}" right=${Math.round(r.right)} > ${Math.round(dr.right)}`); break; } } }
  return [document.documentElement.scrollWidth, out.slice(0, 6)];
}, W);
const name = (p, s='') => p.replace(/[0-9a-f-]{36}/, 'ID').replace(/\//g,'_').replace(/^_/,'') + s + `.${theme}.png`;
const check = async (label) => { const [sw, els] = await overflow(); if (sw > W + 1 || els.length) issues.push(`${label}: scrollWidth ${sw}; ${els.join(' | ')}`); };
const shot = async (p, suffix = '', full = true) => { await page.waitForTimeout(800); await check(p + suffix); await page.screenshot({ path: `/out/out/forms/${name(p, suffix)}`, fullPage: full }); };
const step = async (label, fn) => { try { await fn(); } catch (e) { issues.push(`STEP FAILED ${label}: ${e.message.split('\n')[0]}`); } };
const go = async (p) => { const r = await page.goto(BASE + p, { waitUntil: 'networkidle' }); if (r.status() >= 400) issues.push(`${p} HTTP ${r.status()}`); };

// storefront dialogs (guest)
await step('storefront menu', async () => { await go('/'); await page.getByRole('button', { name: 'Open menu' }).click(); await shot('/', '-menu', false); await page.keyboard.press('Escape'); });
await step('quick view', async () => { await go('/shop'); await page.locator('main button, main [role=button]').filter({ hasText: '₱' }).first().click(); await page.getByRole('dialog').waitFor(); await shot('/shop', '-quickview', false);
  await page.getByRole('dialog').getByRole('button', { name: /add/i }).first().click(); await page.waitForTimeout(800); await shot('/shop', '-after-add', false); });
await step('cart drawer', async () => { await page.keyboard.press('Escape'); await page.getByRole('button', { name: /Open basket/ }).click(); await page.getByRole('dialog').waitFor(); await shot('/shop', '-cartdrawer', false); await page.keyboard.press('Escape'); });
for (const p of extra.filter(p => !p.startsWith('/admin'))) await step(p, async () => { await go(p); await shot(p); });

// admin
await go('/login'); await page.fill('input[name=email]', 'admin@abcdips.test'); await page.fill('input[name=password]', 'Password123!');
await Promise.all([page.waitForURL(u => !u.pathname.startsWith('/login')), page.click('button[type=submit]')]);
await step('sidebar sheet', async () => { await go('/admin'); await page.getByRole('button', { name: 'Toggle Sidebar' }).first().click(); await shot('/admin', '-sidebar', false); await page.keyboard.press('Escape'); });
await step('delete confirm', async () => { await go('/admin/suppliers'); await page.getByRole('button', { name: 'Row actions' }).first().click(); await shot('/admin/suppliers', '-rowmenu', false);
  await page.getByRole('menuitem', { name: /delete|archive/i }).first().click(); await page.getByRole('alertdialog').or(page.getByRole('dialog')).first().waitFor(); await shot('/admin/suppliers', '-confirm', false); await page.keyboard.press('Escape'); });
for (const p of [...creates, ...edits, ...extra.filter(p => p.startsWith('/admin'))]) await step(p, async () => { await go(p); await shot(p); });

// POS
await step('pos', async () => { await go('/admin/pos'); await shot('/admin/pos');
  const cards = page.locator('main button:not([disabled])').filter({ hasText: 'left' });
  await cards.nth(0).click(); await cards.nth(1).click(); await cards.nth(1).click();
  await shot('/admin/pos', '-cart'); await shot('/admin/pos', '-bar', false); await page.getByRole('button', { name: /Review sale/ }).click(); await page.waitForTimeout(700); await shot('/admin/pos', '-sale', false);
  const exact = page.getByRole('button', { name: /exact|₱/i }).filter({ hasNotText: 'left' });
  const cash = page.locator('input#cash, input[name=cash]'); if (await cash.count()) await cash.fill('5000'); else await exact.first().click();
  await shot('/admin/pos', '-tendered');
  await page.getByRole('button', { name: /charge/i }).click(); await page.getByRole('dialog').waitFor({ timeout: 10000 }); await shot('/admin/pos', '-receipt', false); });

await browser.close();
console.log(issues.length ? issues.join('\n') : 'no issues');
