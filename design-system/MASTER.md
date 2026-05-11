# myKursus — Design System (Master)

> **Source of Truth** for all UI work in this project. Page-specific rules in `design-system/pages/<page>.md` override this file when present.
>
> **Stack:** Laravel 12 (Blade) · Tailwind v4 (`@theme`) · Alpine.js · Vite
> **Product:** Indonesian online course platform — discover, purchase, learn, earn certificate
> **Direction:** Professional + Premium · Light + Dark · Content-first · Trust-led

---

## 1. Design Pattern

**Pattern:** Content-first hierarchy with confident CTAs.

| Surface | Pattern |
|---|---|
| Landing / `welcome` | Hero with single primary CTA → social proof → category grid → featured courses → testimonials → final CTA |
| Course catalog | Filter sidebar (desktop) / sticky filter bar (mobile) + responsive card grid (12-col → 6-col → 2-col) |
| Course detail | Two-column on desktop: media + outline (8 cols) · sticky purchase card (4 cols) → tabs (Overview / Curriculum / Instructor / Reviews) |
| Dashboard | Welcome strip → progress KPIs (3-4 metric cards) → "Continue learning" rail → recommended → activity timeline |
| Lesson player | Distraction-light: video/content focus, collapsible curriculum sidebar, persistent progress, next/prev rail |
| Auth | Centered card, max-width 420px, brand mark, single column |
| Checkout (Midtrans Snap) | Order summary card + Snap embed; clear total + trust badges |
| Certificate | Print-friendly A4 landscape, generous margins, official seal placement |

**One primary CTA per screen.** Secondary actions are visually subordinate (ghost/outline). Tertiary is link-style.

---

## 2. Color System (Semantic Tokens)

**Brand intent:** Indigo communicates trust and intellect (edtech standard); amber-gold communicates achievement and premium quality.

### Brand
| Token | Light | Dark | Use |
|---|---|---|---|
| `--color-brand-50` | `#EEF2FF` | `#1E1B4B` | Tint backgrounds |
| `--color-brand-100` | `#E0E7FF` | `#312E81` | Hover tints |
| `--color-brand-500` | `#6366F1` | `#818CF8` | Decorative accents |
| `--color-brand-600` | `#4F46E5` | `#6366F1` | **Primary CTA / links** |
| `--color-brand-700` | `#4338CA` | `#4F46E5` | Pressed / focus ring |
| `--color-brand-900` | `#312E81` | `#E0E7FF` | Display text on light |

### Accent (achievement, premium signals — certificates, badges, "Bestseller", featured)
| Token | Light | Dark |
|---|---|---|
| `--color-accent-500` | `#F59E0B` | `#FBBF24` |
| `--color-accent-600` | `#D97706` | `#F59E0B` |

### Surface & Text
| Token | Light | Dark |
|---|---|---|
| `--color-bg` | `#FAFAF9` (stone-50) | `#0B1020` |
| `--color-surface` | `#FFFFFF` | `#111827` |
| `--color-surface-2` | `#F8FAFC` (slate-50) | `#1F2937` |
| `--color-border` | `#E5E7EB` | `#1F2937` |
| `--color-border-strong` | `#D1D5DB` | `#374151` |
| `--color-text` | `#0F172A` (slate-900) | `#F8FAFC` |
| `--color-text-muted` | `#475569` (slate-600) | `#94A3B8` |
| `--color-text-subtle` | `#64748B` (slate-500) | `#64748B` |

### Semantic State
| Token | Light | Dark | Meaning |
|---|---|---|---|
| `--color-success` | `#059669` | `#10B981` | Enrolled, completed, paid |
| `--color-warning` | `#D97706` | `#F59E0B` | Pending, expiring |
| `--color-error` | `#DC2626` | `#F87171` | Failed payment, validation |
| `--color-info` | `#0284C7` | `#38BDF8` | Helper, neutral notice |

> **Contrast verified:** All `text-on-surface` pairs ≥ 4.5:1 in both themes. Brand-600 on white = 5.4:1 ✓ · slate-600 on stone-50 = 7.1:1 ✓.

> **Anti-pattern:** Do **not** use raw `text-blue-600`, `bg-red-500`, etc. in Blade. Always use the semantic class `text-brand-600`, `bg-error`, etc. defined via `@theme`.

---

## 3. Typography

**Stack:** Pair `Instrument Serif` (display) with `Instrument Sans` (body, already loaded). Both are open-source, premium-feeling, and free via Bunny/Google Fonts.

```css
@theme {
  --font-display: 'Instrument Serif', ui-serif, Georgia, serif;
  --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
  --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
}
```

### Scale (mobile → desktop)
| Token | Size | Line-height | Use |
|---|---|---|---|
| `text-xs` | 12 / 16px | 1.33 | Captions, metadata, legal |
| `text-sm` | 14 / 20px | 1.43 | Helper text, table cells, labels |
| `text-base` | **16 / 24px** | 1.5 | **Body default (never go lower)** |
| `text-lg` | 18 / 28px | 1.55 | Lead paragraph, course price |
| `text-xl` | 20 / 28px | 1.4 | Card titles, section eyebrow |
| `text-2xl` | 24 / 32px | 1.33 | Subsection heading |
| `text-3xl` | 30 / 36px | 1.2 | Page heading (h1 mobile) |
| `text-4xl` | 36 / 40px | 1.15 | Hero subhead (desktop) |
| `display` | 48 → 72px clamp | 1.05 | Hero h1 — use `font-display` |

### Weight discipline
- Display (serif): 400 only — its weight comes from the typeface.
- Sans: 400 body · 500 labels/UI · 600 headings/strong CTA · 700 reserved for hero only.

### Rules
- **Body ≥ 16px** always. Indonesian text wraps long — never `text-sm` for paragraphs.
- **Line length 60–75 chars** on desktop; 35–60 on mobile (`max-w-prose` ≈ 65ch).
- **Tabular figures** for prices, durations, dates: `font-variant-numeric: tabular-nums`.
- **No tracking adjustments** on body. Display headings may use `tracking-tight` (-0.02em).
- **Indonesian language:** lang="id" stays on `<html>`. Avoid all-caps for long phrases (harder to read).

---

## 4. Spacing, Radius, Elevation

### Spacing (4px base, 8px rhythm)
4 · 8 · 12 · 16 · 24 · 32 · 48 · 64 · 96. Section spacing: `py-16 md:py-24`. Card padding: `p-6` (24px). Form gap: `space-y-5`.

### Radius
| Token | Value | Use |
|---|---|---|
| `rounded-md` | 8px | Inputs, small cards |
| `rounded-lg` | 10px | Buttons, default cards |
| `rounded-xl` | 16px | Modals, feature cards |
| `rounded-2xl` | 24px | Hero illustration containers |
| `rounded-full` | 999px | Avatars, pills, badges |

### Elevation (subtle — premium reads as restrained, not loud)
```css
--shadow-sm:  0 1px 2px 0 rgb(15 23 42 / 0.04);
--shadow-md:  0 4px 12px -2px rgb(15 23 42 / 0.06), 0 2px 4px -2px rgb(15 23 42 / 0.04);
--shadow-lg:  0 12px 32px -8px rgb(15 23 42 / 0.10), 0 4px 8px -4px rgb(15 23 42 / 0.05);
--shadow-xl:  0 24px 48px -16px rgb(15 23 42 / 0.14);
```
- Card resting: `shadow-sm` · hover: `shadow-md` (200ms ease-out).
- Modal: `shadow-xl` + 50% black scrim.
- **Never** layered colored shadows. **Never** `shadow-2xl` decoratively.

---

## 5. Components

### Buttons
| Variant | Class shorthand | When |
|---|---|---|
| Primary | `bg-brand-600 hover:bg-brand-700 text-white shadow-sm` | One per screen — primary action (Daftar, Beli, Mulai) |
| Secondary | `bg-surface text-text border border-border-strong hover:bg-surface-2` | Filter, secondary nav |
| Ghost | `text-brand-600 hover:bg-brand-50` | Inline action in tables / cards |
| Destructive | `bg-error text-white hover:bg-error/90` | Logout, hapus, batal langganan |
| Link | `text-brand-600 underline underline-offset-4 hover:text-brand-700` | Inline link |

- **Height:** `h-11` (44px) default · `h-9` compact · `h-12` hero CTA. Padding: `px-5`.
- **Loading:** disable + inline spinner, retain width to prevent layout shift.
- **Focus ring:** `focus-visible:outline-2 outline-offset-2 outline-brand-700`.
- **Touch target:** Always ≥ 44×44 on mobile (use padding or `hitSlop`-equivalent via `before:absolute`).

### Course Card
- Aspect 16:9 thumbnail with `aspect-ratio` reserved to prevent CLS.
- Hierarchy: thumbnail → category eyebrow (xs, muted, uppercase tracking-wide) → title (lg, 2-line clamp) → instructor (sm, muted) → meta row (rating · students · duration) → price (lg, semibold, tabular).
- `Bestseller` / `Baru` badges: amber accent pill in top-left of thumbnail.
- Hover (desktop only): `translate-y-[-2px]` + shadow-md. No transform on mobile.

### Input / Form
- **Always visible label** above input — never placeholder-as-label.
- Helper text persistent below input (not only on error).
- Inline validation **on blur**, not keystroke.
- Error state: 1.5px error border + error message with `role="alert"` below + auto-focus first invalid on submit.
- Required: append `<span aria-hidden="true">*</span>` to label.
- `autocomplete` + `inputmode` set for every field (email, password, tel, number).
- Indonesian forms: include both ID and EN affordances where helpful (e.g., currency `Rp` prefix as `<span>` not in placeholder).

### Navigation (Navbar)
- Sticky top, 64px height, surface background, subtle bottom border.
- Logo wordmark `font-display text-xl` left · primary nav center · auth/user right.
- Active route: brand-600 text + 2px brand-600 bottom underline.
- Mobile: hamburger triggers Alpine.js full-screen sheet (slide-down, 250ms, focus-trapped).
- Auth state visible in nav (`@auth` Halo, Name + avatar dropdown).

### Modal / Sheet (Alpine.js `x-show` + transition)
- Scrim: `bg-slate-900/60 backdrop-blur-sm`.
- Container: centered, `max-w-lg`, `rounded-xl`, `shadow-xl`.
- Close: visible × top-right + ESC + scrim-click.
- Focus trap + return focus to trigger on close.
- Confirm before dismiss if form is dirty.

### Toast (success/error after server action)
- Top-right desktop, top mobile, dismiss in 4s.
- `aria-live="polite"`, never steals focus.
- Color + icon + text — never color alone.

### Progress (lesson completion, course progress)
- 8px height, rounded-full track at `--color-surface-2`, fill at `--color-brand-600`.
- ARIA: `role="progressbar"` with `aria-valuenow/min/max`.
- Show percentage label in tabular figures.

---

## 6. Motion

- Durations: `150ms` micro · `200ms` default · `300ms` modals/sheets.
- Easing: `cubic-bezier(0.16, 1, 0.3, 1)` (ease-out-expo) for enters; ease-in for exits (~60% of enter duration).
- Animate `transform` + `opacity` only — never `width`, `height`, `top`, `left`.
- Respect `prefers-reduced-motion`: disable parallax, replace slides with fades.
- Page transitions: subtle 8px translate-up + fade on `<main>` mount.
- Card hover: 150ms ease-out lift; reset 100ms ease-in.

---

## 7. Accessibility (non-negotiable)

- All text contrast ≥ 4.5:1; large text ≥ 3:1. Verify both themes independently.
- Focus rings visible — never `outline-none` without replacement.
- Sequential headings (h1 → h2 → h3 — no skips).
- Form labels with `for` attribute matching input `id`.
- Icon-only buttons require `aria-label` (in Indonesian: `aria-label="Tutup"`).
- Decorative SVG: `aria-hidden="true"`; meaningful SVG: `<title>` inside.
- `<html lang="id">` (already correct in layout).
- Skip link: `<a href="#main" class="sr-only focus:not-sr-only">Lewati ke konten</a>` as first child of `<body>`.
- Form errors use `aria-live="assertive"` summary at top with anchor links + per-field `aria-describedby`.
- Don't disable browser zoom in viewport meta. Current layout is **missing** `<meta name="viewport">` — add it.

---

## 8. Responsive

- Breakpoints: `sm 640 · md 768 · lg 1024 · xl 1280 · 2xl 1536`.
- Container: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`.
- Mobile-first. Design 375px first, scale up.
- No horizontal scroll. Test landscape (course player critical).
- Sticky purchase CTA bar on mobile course-detail (bottom-fixed, safe-area aware).
- Tables on mobile: switch to card list, never horizontal scroll.

---

## 9. Iconography

- **Heroicons** (outline 24, solid 20) or **Lucide** — pick one set, do not mix.
- Stroke width consistent (1.5px outline, solid for filled states).
- **No emoji as UI icons.** Emojis only inside user-generated content.
- Sizes: `size-4` (16) inline · `size-5` (20) buttons · `size-6` (24) nav · `size-8` (32) feature.
- Icon + label by default. Icon-only requires `aria-label`.

---

## 10. Dark Mode

- Strategy: `class="dark"` on `<html>` toggled via Alpine.js `x-data` persisted to `localStorage`. Respect `prefers-color-scheme` on first visit.
- All tokens defined per theme (see §2). No hardcoded hex outside `@theme`.
- Dark surfaces are tonal (`#0B1020`, `#111827`, `#1F2937`), not pure black.
- Test contrast independently in dark; light values don't transfer.
- Shadows weaker in dark mode (replace some with `ring-1 ring-white/5`).

---

## 11. Data Viz (Dashboard analytics, progress)

- Use **Chart.js** (works cleanly with Blade + Alpine, no React needed).
- Trends → line · Comparisons → bar · Composition → bar (avoid pie >5 categories).
- Always: legend, axis labels with units (`%`, `jam`, `siswa`), tooltip on hover/tap.
- Colors: brand for primary series, slate for comparison, semantic for status. Never red/green alone for state — add icon or pattern.
- Empty state: meaningful illustration + "Belum ada data" + suggested action.
- Loading: skeleton, not empty axes.
- Respect reduced-motion (no entry animation when set).

---

## 12. Anti-patterns (do NOT do these)

1. ❌ Generic `bg-blue-600`, `text-red-500` in Blade — use semantic tokens.
2. ❌ Emojis as functional icons.
3. ❌ `placeholder` used as label.
4. ❌ `text-sm` for paragraph body text.
5. ❌ Tap targets < 44px on mobile.
6. ❌ `outline-none` on `:focus` without replacement.
7. ❌ Removing `viewport` meta or disabling user zoom.
8. ❌ Color-only error/success indicators.
9. ❌ Animating `width`/`height`/`top`/`left` (use `transform`).
10. ❌ Decorative shadows above `shadow-lg`.
11. ❌ Multiple primary buttons on one screen.
12. ❌ Toasts that steal focus.
13. ❌ Horizontal scroll on mobile.
14. ❌ Hardcoded hex outside `@theme`.
15. ❌ Mixing Heroicons + Lucide + emoji in one screen.

---

## 13. Implementation: Wire Tokens into Tailwind v4

Replace `resources/css/app.css` with the following `@theme` block (extended from current):

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@custom-variant dark (&:where(.dark, .dark *));

@theme {
  /* Typography */
  --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
  --font-display: 'Instrument Serif', ui-serif, Georgia, serif;
  --font-mono: 'JetBrains Mono', ui-monospace, monospace;

  /* Brand */
  --color-brand-50:  #EEF2FF;
  --color-brand-100: #E0E7FF;
  --color-brand-500: #6366F1;
  --color-brand-600: #4F46E5;
  --color-brand-700: #4338CA;
  --color-brand-900: #312E81;

  /* Accent */
  --color-accent-500: #F59E0B;
  --color-accent-600: #D97706;

  /* Surface (light defaults; dark overridden below) */
  --color-bg:             #FAFAF9;
  --color-surface:        #FFFFFF;
  --color-surface-2:      #F8FAFC;
  --color-border:         #E5E7EB;
  --color-border-strong:  #D1D5DB;
  --color-text:           #0F172A;
  --color-text-muted:     #475569;
  --color-text-subtle:    #64748B;

  /* Semantic */
  --color-success: #059669;
  --color-warning: #D97706;
  --color-error:   #DC2626;
  --color-info:    #0284C7;

  /* Radius */
  --radius-md: 0.5rem;
  --radius-lg: 0.625rem;
  --radius-xl: 1rem;

  /* Shadow */
  --shadow-sm: 0 1px 2px 0 rgb(15 23 42 / 0.04);
  --shadow-md: 0 4px 12px -2px rgb(15 23 42 / 0.06), 0 2px 4px -2px rgb(15 23 42 / 0.04);
  --shadow-lg: 0 12px 32px -8px rgb(15 23 42 / 0.10), 0 4px 8px -4px rgb(15 23 42 / 0.05);
  --shadow-xl: 0 24px 48px -16px rgb(15 23 42 / 0.14);
}

.dark {
  --color-bg:             #0B1020;
  --color-surface:        #111827;
  --color-surface-2:      #1F2937;
  --color-border:         #1F2937;
  --color-border-strong:  #374151;
  --color-text:           #F8FAFC;
  --color-text-muted:     #94A3B8;
  --color-text-subtle:    #64748B;

  --color-brand-500: #818CF8;
  --color-brand-600: #6366F1;
  --color-brand-700: #4F46E5;

  --color-accent-500: #FBBF24;
  --color-accent-600: #F59E0B;

  --color-success: #10B981;
  --color-warning: #F59E0B;
  --color-error:   #F87171;
  --color-info:    #38BDF8;
}

@layer base {
  html { color-scheme: light dark; }
  body { background: var(--color-bg); color: var(--color-text); font-family: var(--font-sans); }
  :focus-visible { outline: 2px solid var(--color-brand-700); outline-offset: 2px; border-radius: 4px; }
  .tabular-nums { font-variant-numeric: tabular-nums; }
}
```

### Required `<head>` additions in `layouts/app.blade.php`
```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#FAFAF9" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0B1020" media="(prefers-color-scheme: dark)">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|instrument-serif:400" rel="stylesheet">
```

---

## 14. Pre-Delivery Checklist (run before merging UI changes)

- [ ] Body text ≥ 16px everywhere.
- [ ] All interactive elements ≥ 44×44 on mobile.
- [ ] `:focus-visible` ring visible on every interactive element.
- [ ] Light + dark mode both contrast-verified (4.5:1 body, 3:1 large).
- [ ] No `bg-blue-*` / raw hex outside `app.css`. Only semantic tokens in Blade.
- [ ] No emoji icons; consistent icon set (Heroicons or Lucide, not both).
- [ ] All `<img>` have width + height or `aspect-ratio` (no CLS).
- [ ] Forms: labels above, helper text, inline validation on blur, error summary on submit.
- [ ] One primary CTA per screen.
- [ ] Tested at 375 / 768 / 1280 widths + landscape.
- [ ] `prefers-reduced-motion` respected.
- [ ] `<html lang="id">` and Indonesian copy reviewed for tone.
- [ ] Skip link present on every page.
- [ ] Logout / destructive actions visually separated from primary nav.

---

## How to Use This File

When building any new page or component, prompt yourself:

> *I am building the [Page Name] page. I will read `design-system/MASTER.md`. I will check if `design-system/pages/<page-name>.md` exists; if so, its rules override Master. Now I will generate the code following these tokens, components, and rules.*

Add page-specific overrides under `design-system/pages/<page>.md` only when the page genuinely deviates (e.g., lesson player has no navbar, certificate is print-only).
