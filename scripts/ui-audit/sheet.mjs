import { chromium } from 'playwright'; import fs from 'fs';
const files = fs.readdirSync('/out/out/shots').filter(f => f.endsWith('.png')).sort();
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1800, height: 1000 } });
for (const [tag, w, cols] of [['m', 180, 8], ['d', 440, 4]]) for (const theme of ['light', 'dark']) {
  const set = files.filter(f => f.endsWith(`.${theme}.${tag}.png`));
  const html = `<body style="margin:0;background:#888;font:12px sans-serif"><div style="display:grid;grid-template-columns:repeat(${cols},${w}px);gap:6px;padding:6px">` +
    set.map(f => `<figure style="margin:0"><img style="width:${w}px;display:block" src="data:image/png;base64,${fs.readFileSync('/out/out/shots/'+f).toString('base64')}"><figcaption>${f}</figcaption></figure>`).join('') + '</div></body>';
  await p.setContent(html); await p.screenshot({ path: `/out/out/sheet-${theme}-${tag}.png`, fullPage: true });
}
await b.close();
