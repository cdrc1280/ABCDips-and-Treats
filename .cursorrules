# ABCDips & Treats — Permanent Global Architecture & Brand Directives

## 1. Brand Aesthetics & Visual Identity Reference
- **Brand Visual Source of Truth**:
  - Official Visual Reference: [Facebook Brand Post #122306319362071299](https://www.facebook.com/photo?fbid=122306319362071299)
  - Color Hierarchy:
    - Primary Chocolates: Rich Cocoa (`#20150E`, `#1C1410`, `#2D1B10`)
    - Caramel / Butter Gold Accents: (`#C08E5D`, `#D9A876`, `#E2C08A`)
    - Fresh Pastry Cream & Canvas: (`#FBF3E7`, `#FDFBF7`)
- **Anti-Generic Layout Engineering**:
  - Ban cookie-cutter bootstrap/tailwind template layouts (rigid flat blocks, un-styled cards).
  - Use asymmetrical layered visual hierarchy, backdrop blurs (`backdrop-blur-md`), subtle gold hairline borders (`border-[#C08E5D]/30`), depth shadows, and interactive 3D product showcases.
  - Implement Lenis smooth inertia physics for all scrolling and GSAP ScrollTrigger timeline orchestration.

## 2. Zero AI-Slop Copywriting & Vocabulary Ban
- **STRICTLY FORBIDDEN BUZZWORDS**:
  - Never use: `Artisanal`, `Bespoke`, `Showstopper`, `Elevate`, `Delve`, `Tapestry`, `Masterpiece`, `Unleash`, `Crafted with passion`, `Game-changer`, `Next-level`, `Revolutionize`.
- **AUTHENTIC HUMAN BAKERY VOICE ONLY**:
  - Use genuine bakery language: `Freshly Baked`, `Handcrafted`, `Baked Daily`, `Small Batch`, `Real Butter`, `Oven Fresh`, `Signature Recipe`, `Cavite Bakery`.

## 3. Zero AI-Slop Iconography Standards
- Zero raw unicode emojis as primary buttons, navigation icons, badges, or watermarks.
- Production vector icons only via `lucide-vue-next` with standard stroke widths.

## 4. High Performance & Lightweight Architecture
- **Chunk Splitting**: Keep vendor chunks separated (`vendor-vue`, `vendor-gsap`, `vendor-axios`, `vendor-icons`).
- **Server Caching**: Wrap heavy catalog queries in `Cache::remember(..., 300, ...)`.
- **Client SWR**: Avoid layout shifts with in-memory route caching.
- **RAF Scheduling**: Always throttle continuous mouse/scroll animations with `requestAnimationFrame`.

## 5. Git & Workflow Protocol
- **No Frequent Commits/Pushes**:
  - Do NOT commit or push for every single small change or intermediate step. Keep code in the working directory during active iteration.
  - Only stage, commit, or push when explicitly requested by the user or upon complete milestone sign-off.
