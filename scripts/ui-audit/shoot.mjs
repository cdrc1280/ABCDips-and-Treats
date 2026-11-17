import { chromium } from 'playwright';
const BASE = process.env.BASE || 'http://localhost:8000';
const pub = ['/', '/shop', '/products/classic-banana-bread-loaf', '/cart', '/about', '/faq', '/login', '/register'];
const adm = ['/admin', '/admin/products', '/admin/orders', '/admin/ingredients', '/admin/employees'];
const browser = await chromium.launch();
const issues = [];
for (const theme of ['light', 'dark']) {
  for (const [w, h, tag] of [[375, 812, 'm'], [1440, 900, 'd']]) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h }, colorScheme: theme });
    await ctx.addCookies([{ name: 'appearance', value: theme, url: BASE }]);
    const page = await ctx.newPage();
    page.on('console', m => { if (m.type() === 'error') issues.push(`[${theme}/${tag}] console: ${m.text().slice(0, 200)}`); });
    page.on('pageerror', e => issues.push(`[${theme}/${tag}] pageerror: ${e.message.slice(0, 200)}`));
    const shoot = async (p) => {
      const r = await page.goto(BASE + p, { waitUntil: 'networkidle' });
      await page.waitForTimeout(400);
      const sw = await page.evaluate(() => document.documentElement.scrollWidth);
      if (r && r.status() >= 400) issues.push(`[${theme}/${tag}] ${p} HTTP ${r.status()}`);
      if (sw > w + 1) issues.push(`[${theme}/${tag}] ${p} horizontal overflow: scrollWidth ${sw} > ${w}`);
      const name = (p === '/' ? 'home' : p.replace(/\//g, '_').replace(/^_/, '')) + `.${theme}.${tag}.png`;
      await page.screenshot({ path: `/out/out/shots/${name}`, fullPage: tag === 'm' ? false : false });
    };
    for (const p of pub) await shoot(p);
    await page.goto(BASE + '/login');
    await page.fill('input[name=email]', 'admin@abcdips.test');
    await page.fill('input[name=password]', 'Password123!');
    await Promise.all([page.waitForURL(u => !u.pathname.startsWith('/login'), { timeout: 15000 }).catch(() => issues.push(`[${theme}/${tag}] login did not redirect`)), page.click('button[type=submit]')]);
    for (const p of adm) await shoot(p);
    await ctx.close();
  }
}
await browser.close();
console.log(issues.length ? issues.join('\n') : 'no issues');
