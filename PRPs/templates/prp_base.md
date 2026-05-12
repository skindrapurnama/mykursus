name: "Base PRP Template — myKursus (Laravel 12 + Tailwind v4 + Alpine)"
description: |

## Purpose
Template optimized for AI agents to implement features in myKursus with sufficient context and self-validation capabilities to achieve working code through iterative refinement.

## Core Principles
1. **Context is King**: Include ALL necessary docs, examples, and caveats (Laravel quirks, Tailwind v4 syntax, Indonesian copy, route wiring).
2. **Validation Loops**: Provide executable Pint / test / build commands the AI can run and fix.
3. **Information Dense**: Use keywords and patterns from the actual codebase.
4. **Progressive Success**: Start simple (migration → model → controller → view → route), validate, then enhance.
5. **Global rules**: Follow all rules in `CLAUDE.md`.

---

## Goal
[What needs to be built — be specific about the end state and user experience.]

## Why
- [Business value and user impact]
- [Integration with existing features (dashboard? course flow? certificates?)]
- [Problems this solves and for whom]

## What
[User-visible behavior and technical requirements.]

### Success Criteria
- [ ] [Specific measurable outcomes — e.g. "Route `/dashboard` returns 200 for authenticated users and renders 4 KPI cards"]

## All Needed Context

### Documentation & References
```yaml
# MUST READ
- url: https://laravel.com/docs/12.x/<section>
  why: [What's needed from this section]

- file: app/Http/Controllers/UserCourseController.php
  why: [Pattern to mirror — controller structure, with() eager loading]

- file: resources/views/dashboard.blade.php
  why: [Existing view if applicable — what shape it expects]

- file: design-system/MASTER.md
  section: [Component pattern relevant to the feature]
  critical: [Token names to use]

- doc: https://tailwindcss.com/docs/v4-beta
  section: @theme + arbitrary value with CSS vars (`bg-(--color-X)`)
```

### Current codebase tree (relevant parts)
```
app/
  Http/Controllers/
    AuthController.php
    CourseController.php
    MidtransController.php
    UserCourseController.php
  Models/
    Certificate.php  Course.php  Payment.php
    Registration.php  Testimonial.php  User.php
resources/
  views/
    layouts/app.blade.php
    auth/{login,register}.blade.php
    courses/show.blade.php
    user/courses.blade.php
    dashboard.blade.php          # exists but UNROUTED
    certificates.blade.php       # exists but UNROUTED
    payment/snap.blade.php
    home.blade.php
  css/app.css
  js/app.js
routes/web.php                   # check route wiring here
design-system/MASTER.md          # design tokens
```

### Desired codebase tree (files to add/modify)
```
app/Http/Controllers/<NewController>.php       # responsibility
resources/views/<feature>/<page>.blade.php     # extends layouts.app
routes/web.php                                  # MODIFY — add named routes
database/migrations/YYYY_MM_DD_*.php            # if schema changes
tests/Feature/<NewFeatureTest>.php              # at least happy + 1 edge
```

### Known gotchas of our codebase & Laravel/Tailwind quirks
```php
// CRITICAL: Tailwind v4 uses CSS-variable arbitrary syntax: bg-(--color-brand-600)
// Raw bg-blue-600 is forbidden by design system rules.

// CRITICAL: Dashboard/certificates views were refactored but routes/web.php
// has NO /dashboard or /certificates routes yet. Wire them.

// CRITICAL: Carbon Indonesian locale must be set in AppServiceProvider::boot():
//   \Carbon\Carbon::setLocale('id');
// Otherwise translatedFormat() outputs English.

// CRITICAL: Prices stored as integer rupiah, displayed via number_format($v, 0, ',', '.')

// CRITICAL: Layout already loads Alpine via @vite. Do NOT add CDN script tag.

// CRITICAL: Never call Model::where() inside Blade @foreach — preload in controller.
```

## Implementation Blueprint

### Data models & migrations (if any)
```php
// If new tables: 
// CREATE database/migrations/YYYY_MM_DD_HHMMSS_<name>.php
//   Schema::create('table', function (Blueprint $t) { ... });
// 
// If new columns on existing tables:
//   Schema::table('users', fn($t) => $t->...);
//
// Update model $fillable and casts accordingly.
```

### List of tasks in execution order

```yaml
Task 1 — Migration (if schema change):
  CREATE database/migrations/YYYY_MM_DD_HHMMSS_<desc>.php
  - PATTERN: Mirror existing migration in same folder
  - PRESERVE: existing schema; add only new columns/tables

Task 2 — Model (if new model or relationship):
  CREATE/MODIFY app/Models/<Model>.php
  - PATTERN: Mirror app/Models/Registration.php (relationships, $fillable)

Task 3 — Controller:
  CREATE app/Http/Controllers/<Controller>.php
  - PATTERN: Mirror app/Http/Controllers/UserCourseController.php
  - Eager-load relations with ->with([...]) to avoid N+1
  - Pass compact() data to view

Task 4 — View:
  CREATE/MODIFY resources/views/<path>.blade.php
  - PATTERN: @extends('layouts.app') and @section('title', ...)
  - Use design tokens only: bg-(--color-brand-600), text-(--color-text)
  - Heroicons SVG inline; no emojis as icons
  - All buttons h-11 or h-12 (>=44pt touch target)

Task 5 — Routes:
  MODIFY routes/web.php
  - Add Route::middleware('auth')->group for protected routes
  - ALWAYS name routes ->name('feature.action')

Task 6 — Navbar / dashboard wiring:
  MODIFY resources/views/layouts/app.blade.php (if navigation changes)
  - Add link in @auth section; use semantic tokens

Task 7 — Tests:
  CREATE tests/Feature/<NewFeatureTest>.php
  - Happy path
  - One auth/authorization edge
  - One failure path
```

### Per-task pseudocode (when helpful)
```php
// Task 3 — Controller
public function index(): \Illuminate\View\View
{
    // PATTERN: eager-load to avoid N+1
    $items = Registration::with(['course', 'payment'])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    // PATTERN: compute aggregates in PHP, not Blade
    $stats = [
        'foo' => $items->count(),
        'bar' => $items->where('status', 'approved')->count(),
    ];

    return view('feature.index', compact('items', 'stats'));
}
```

### Integration points
```yaml
DATABASE:
  - migration: "[describe table or column change, if any]"

ROUTES:
  - add to: routes/web.php
  - pattern: |
      Route::middleware('auth')->group(function () {
          Route::get('/<path>', [<Controller>::class, 'index'])->name('<feature>.index');
      });

NAVBAR:
  - add to: resources/views/layouts/app.blade.php (@auth section)
  - pattern: "<a href='{{ route('<feature>.index') }}' class='...semantic tokens...'>Label ID</a>"

LOCALE:
  - confirm: AppServiceProvider::boot() sets \Carbon\Carbon::setLocale('id')

DESIGN SYSTEM:
  - reference: design-system/MASTER.md sections for any new component
  - if new component pattern emerges: add to design-system/pages/<page>.md
```

## Validation Loop

### Level 1 — Syntax & Style
```bash
# Auto-fix PHP style
vendor/bin/pint app/Http/Controllers/<Controller>.php

# Lint Blade by rendering view-cache compile (no separate linter — caught by routes test)
php artisan view:clear

# Expected: no errors
```

### Level 2 — Unit / Feature Tests
```php
// CREATE tests/Feature/<NewFeatureTest>.php
public function test_authenticated_user_can_view_page(): void
{
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('<feature>.index'))
        ->assertOk()
        ->assertSee('<expected copy>');
}

public function test_guest_is_redirected(): void
{
    $this->get(route('<feature>.index'))->assertRedirect(route('login'));
}
```

```bash
php artisan test --filter=<NewFeatureTest>
```

### Level 3 — Manual / Integration
```bash
# Start dev stack
php artisan serve &
npm run dev &

# Visit
# http://127.0.0.1:8000/<route>

# Expected: page renders, no console errors, Alpine widgets work
```

## Final validation checklist
- [ ] `vendor/bin/pint --test` clean
- [ ] `php artisan test` green (new + existing)
- [ ] `npm run build` succeeds
- [ ] Route exists and is named
- [ ] Navbar updated if user-facing
- [ ] Design tokens only (no `bg-blue-600`)
- [ ] Indonesian copy reviewed
- [ ] Touch targets ≥ 44pt
- [ ] Works in light AND dark theme
- [ ] No N+1 (use Laravel Debugbar or telescope if available)

---

## Anti-Patterns to Avoid
- ❌ Raw Tailwind colors in Blade
- ❌ Emoji as UI icon
- ❌ Query inside Blade @foreach
- ❌ Adding a controller without naming the route
- ❌ Editing already-shipped migrations
- ❌ Skipping validation gates because "it should work"
- ❌ Hardcoding hex outside `app.css @theme`
- ❌ Sync N+1 lookups in views
