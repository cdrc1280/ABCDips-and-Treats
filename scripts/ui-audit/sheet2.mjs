import { chromium } from 'playwright'; import fs from 'fs';
const all = fs.readdirSync('/out/out/forms').sort();
const groups = {
  dialogs: f => /-(menu|quickview|after-add|cartdrawer|sidebar|rowmenu|confirm)\./.test(f) || f.startsWith('admin_pos'),
  forms1: f => /_create\./.test(f),
  forms2: f => !/_create\./.test(f) && !(/-(menu|quickview|after-add|cartdrawer|sidebar|rowmenu|confirm)\./.test(f) || f.startsWith('admin_pos')),
};
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1900, height: 1000 } });
for (const theme of ['light','dark']) for (const [g, fn] of Object.entries(groups)) {
  const set = all.filter(f => f.endsWith(`.${theme}.png`) && fn(f));
  const html = `<body style="margin:0;background:#888;font:11px sans-serif"><div style="display:grid;grid-template-columns:repeat(10,180px);gap:6px;padding:6px">` +
    set.map(f => `<figure style="margin:0"><div style="height:${g==='dialogs'?390:560}px;overflow:hidden;background:#fff"><img style="width:180px;display:block" src="data:image/png;base64,${fs.readFileSync('/out/out/forms/'+f).toString('base64')}"></div><figcaption>${f.replace(`.${theme}.png`,'')}</figcaption></figure>`).join('') + '</div></body>';
  await p.setContent(html); await p.screenshot({ path: `/out/out/fsheet-${g}-${theme}.png`, fullPage: true });
}
await b.close();
