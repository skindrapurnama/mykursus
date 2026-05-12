# myKursus — AI Assistant Rules (CLAUDE.md)

> Global rules for AI coding assistants working on this project. Read this at the start of every conversation.

## Project at a glance

- **Product:** myKursus — Indonesian online course platform (discover → purchase → learn → certificate).
- **Stack:** Laravel 12 (PHP 8.2+) · Tailwind CSS v4 · Alpine.js 3 · Vite · MySQL · Midtrans (Snap)
- **Language of UI copy:** Bahasa Indonesia. `<html lang="id">`.
- **Design system:** `design-system/MASTER.md` is the source of truth. Page-specific overrides under `design-system/pages/<page>.md`.

## Project awareness

- **Always** read `design-system/MASTER.md` before any UI work.
- **Always** check `routes/web.php` to confirm a feature is actually wired — views without routes are dead code.
- Read `PRPs/` for active blueprints. If a relevant PRP exists, follow it.
- Before adding new functionality, check whether an existing controller/model/relationship already covers it.

## Code structure & modularity

- **Controllers** stay thin. Push business logic to models, services (in `app/Services/`), or actions (in `app/Actions/`).
- **No file over ~400 lines.** If a controller or view grows beyond that, split it.
- **Blade views** organize by feature folder: `resources/views/<feature>/<page>.blade.php`.
- **Routes** group by auth requirement and feature. Name every route (`->name('...')`).
- **Migrations** are immutable once shipped. New schema = new migration, never edit old ones.

## Database & models

- Use Eloquent relationships, not raw joins, unless performance demands it.
- **Avoid N+1**: always `->with([...])` when looping over a collection that accesses relations.
- **Never** call `\App\Models\X::query()` inside a Blade `@foreach` — pre-load in the controller.
- Fillable arrays must be explicit. Never use `$guarded = []` in production code.
- Cast date/datetime columns. Use `Carbon` for date arithmetic and `->translatedFormat()` for Indonesian output.

## Frontend (Blade + Tailwind v4 + Alpine)

- **Use semantic tokens only** in Blade: `bg-(--color-brand-600)`, `text-(--color-error)`. Never `bg-blue-600` or raw hex.
- Token definitions live in `resources/css/app.css` under `@theme { ... }` and `.dark { ... }`.
- Alpine.js is imported in `resources/js/app.js` and bundled by Vite. Layout already includes it via `@vite(['resources/css/app.css', 'resources/js/app.js'])` — don't add the CDN script.
- Always include `[x-cloak]{display:none}` behavior — already in base layer.
- Icons: Heroicons SVG inline. **No emoji as UI icons.**
- All interactive elements ≥ 44×44 pt, with visible `:focus-visible` ring.
- Body text minimum 16px (`text-base`). Indonesian text wraps longer than English.
- New pages must extend `layouts.app` and define `@section('title', '...')`.

## Conventions

- **PHP:** PSR-12. Type-hint everything. Return-type all methods.
- **Naming:** snake_case columns, camelCase methods, PascalCase classes, kebab-case routes.
- **Tests:** PHPUnit (`tests/Feature/`, `tests/Unit/`). Pest is OK if already in use.
- **Commits:** present-tense imperative ("add dashboard route", not "added").
- **Migration filenames:** `YYYY_MM_DD_HHMMSS_describe_change.php`.

## Forms & validation

- Use `FormRequest` classes for non-trivial validation (in `app/Http/Requests/`).
- Error display: visible label above input, helper text below, error message via `@error` + `role="alert"`.
- Use semantic input types (`type="email"`, `inputmode="numeric"`, `autocomplete="..."`).
- CSRF token on every form (`@csrf`).

## Testing & reliability

- New feature = at least one Feature test covering the happy path + one edge/failure case.
- Tests must hit a real test DB (`RefreshDatabase` or `DatabaseTransactions`), not mocks for ORM behavior.
- Validation gates: `php artisan test`, `vendor/bin/pint` (or Laravel Pint config), `npm run build`.

## Money & locale

- Prices stored as **integer rupiah** (no fractional). Display with `number_format($value, 0, ',', '.')`.
- Dates: store UTC, render `Carbon::parse()->translatedFormat('d M Y')` with locale `id` set in `AppServiceProvider`.
- Currency prefix: `Rp ` (with space).

## Security

- Never log secrets, payment proofs, or session tokens.
- Validate file uploads: MIME type + size + extension. Store outside webroot when possible.
- Midtrans callback (`/midtrans/callback`) must verify server-key signature.
- Authorization: use Policies or `Gate` for resource-level checks. Never trust `request()->user_id`.

## AI behavior rules

- **Never assume missing context.** Ask if uncertain.
- **Never fabricate** routes, models, columns, or helper functions. Verify they exist first (`grep` / `Read`).
- **Never edit a migration** that has already been run in shared environments. Create a new one.
- **Never use `--no-verify`** to skip git hooks.
- **Never use destructive git commands** (`reset --hard`, `push --force`, branch deletion) without explicit user instruction.
- When refactoring a view, run `php artisan view:clear` mentally and confirm route exists.
- When changing CSS tokens, remind the user to run `npm run dev` / `npm run build`.

## PRP workflow (when used)

1. Write `INITIAL.md` describing the feature with FEATURE / EXAMPLES / DOCUMENTATION / OTHER CONSIDERATIONS sections.
2. Run `/generate-prp INITIAL.md` → produces `PRPs/<feature-name>.md`.
3. Run `/execute-prp PRPs/<feature-name>.md` → implements with validation loops.
4. PRPs are living documents — update during execution.

## Anti-patterns

- ❌ Raw Tailwind colors (`bg-blue-600`) in Blade.
- ❌ Emoji as UI icon (only OK inside user-generated content).
- ❌ Placeholder used as label in forms.
- ❌ Querying models inside Blade `@foreach`.
- ❌ Adding routes/controllers without naming the route.
- ❌ Mutating old migrations.
- ❌ Storing prices as float.
- ❌ Hardcoding hex colors outside `app.css @theme`.

## Reference files

- `design-system/MASTER.md` — design tokens & component rules
- `routes/web.php` — all routes (currently no `/dashboard` or `/certificates` routes wired)
- `resources/views/layouts/app.blade.php` — master layout
- `resources/css/app.css` — Tailwind v4 `@theme` + dark mode tokens
- `resources/js/app.js` — Alpine.js bootstrap
