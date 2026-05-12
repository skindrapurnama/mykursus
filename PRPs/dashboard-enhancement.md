name: "Dashboard Enhancement & Routing — myKursus"
description: |
  Wire the unwired `/dashboard` and `/certificates` routes, build their controllers, and enhance the
  dashboard with a "Lanjutkan belajar" rail and "Aktivitas terbaru" timeline. All work in Laravel 12 +
  Tailwind v4 + Alpine.js, using the project's design tokens.

## Purpose
Bring the already-styled dashboard.blade.php and certificates.blade.php to life by adding their missing
routes/controllers, while extending the dashboard with two activity-oriented sections so the page tells the
user something useful instead of just three KPI numbers.

## Core Principles
1. Mirror existing controller pattern from `UserCourseController` (eager-loaded, thin).
2. Use design-system tokens only in Blade (`bg-(--color-brand-600)`, etc.).
3. Aggregate activity in PHP (no schema changes).
4. Test the happy path + auth redirect + isolation between users.

---

## Goal

After implementation, an authenticated user visiting `/dashboard` sees:
1. The existing KPI cards (Kursus, Sertifikat, Status) — unchanged.
2. A **"Lanjutkan belajar"** section listing up to 3 of their `approved` registrations (most recently registered first), each linking to the course detail page.
3. An **"Aktivitas terbaru"** timeline showing the latest 5 events drawn from their registrations, payments, and certificates — with icon, Indonesian title, and relative time (e.g. "2 jam lalu").
4. The existing "Akses cepat" section — unchanged.

A user visiting `/certificates` sees their certificates list (view already styled).

Guests visiting either route are redirected to `/login`.

## Why
- The navbar (`layouts/app.blade.php`) already links to `/dashboard` and `/certificates`, but both currently 404 because the routes don't exist. Users see a broken navigation experience.
- The dashboard view today shows only counts. A "continue learning" rail and an activity feed measurably increase engagement and re-entry into the learning flow.
- No new schema is needed — the data model already supports this. We're closing a wiring gap, not building net-new features.

## What
- `GET /dashboard` → `DashboardController@index` (auth) → renders existing view with extra props.
- `GET /certificates` → `CertificateController@index` (auth) → renders existing view.
- `\Carbon\Carbon::setLocale('id')` set in `AppServiceProvider::boot()` so date helpers output Indonesian.
- Dashboard view extended with two new sections.

### Success Criteria
- [ ] `php artisan route:list` shows `dashboard` and `certificates.index` named routes, both with `auth` middleware.
- [ ] Authenticated user gets 200 from both routes.
- [ ] Guest gets 302 to `/login` from both routes.
- [ ] User A cannot see User B's data on the dashboard.
- [ ] `/dashboard` shows the 3 most recent approved registrations in "Lanjutkan belajar" (or an empty state).
- [ ] "Aktivitas terbaru" shows at most 5 events, newest first, with Indonesian relative time.
- [ ] No N+1 queries (verified via `DB::listen` count in test or telescope).
- [ ] Works in light AND dark mode.
- [ ] `php artisan test` passes.

## All Needed Context

### Documentation & References
```yaml
- url: https://laravel.com/docs/12.x/routing#middleware
  why: Pattern for grouping auth-protected routes
  critical: Use Route::middleware('auth')->group() like existing routes/web.php

- url: https://laravel.com/docs/12.x/eloquent-relationships#eager-loading
  why: Avoid N+1 when activity items reference course titles

- url: https://laravel.com/docs/12.x/collections#available-methods
  why: merge(), sortByDesc(), take(), map() for aggregating activity from 3 sources

- url: https://carbon.nesbot.com/docs/#api-localization
  section: setLocale, diffForHumans
  critical: Without setLocale('id'), diffForHumans() returns "2 hours ago" instead of "2 jam lalu"

- file: app/Http/Controllers/UserCourseController.php
  why: Canonical controller pattern — eager loads, scopes to auth()->id(), returns view with compact()

- file: resources/views/dashboard.blade.php
  why: Existing view. Expects $coursesCount, $certificatesCount. Add new sections AFTER KPI grid, BEFORE "Akses cepat".

- file: resources/views/user/courses.blade.php
  why: Card-list rendering pattern with semantic tokens and match() for badges

- file: resources/views/certificates.blade.php
  why: Already-styled list view; needs only its controller + route

- file: design-system/MASTER.md
  section: §5 Components — card pattern, badge pattern
  critical: All new markup uses bg-(--color-*) / text-(--color-*) tokens — no raw Tailwind colors

- file: routes/web.php
  why: Where to add the two new route definitions. Pattern: Route::middleware('auth')->group(...)
```

### Current codebase tree (relevant)
```
app/
  Http/Controllers/
    AuthController.php
    CourseController.php
    MidtransController.php
    UserCourseController.php          # mirror this
  Models/
    User.php                          # has registrations() + certificates()
    Course.php                        # has registrations() + certificates() + testimonials()
    Registration.php                  # belongsTo user + course; hasOne payment
    Certificate.php                   # belongsTo user + course; $dates=['issued_at']
    Payment.php                       # belongsTo registration
  Providers/
    AppServiceProvider.php            # MODIFY — add Carbon::setLocale('id')
database/
  factories/
    UserFactory.php                   # exists; we'll create factories for Course/Registration/Cert/Payment
  migrations/
    2026_04_21_082917_create_registrations_table.php   # status enum: pending|approved|rejected
    2026_04_21_082918_create_payments_table.php        # status enum incl: paid, approved
    2026_04_21_082919_create_certificates_table.php    # issued_at timestamp nullable
resources/
  views/
    layouts/app.blade.php             # already links /dashboard, /certificates
    dashboard.blade.php               # MODIFY — append two sections
    certificates.blade.php            # OK as-is
routes/web.php                        # MODIFY — add 2 routes inside auth group
tests/
  Feature/ExampleTest.php             # current placeholder
  TestCase.php                        # bare TestCase — no traits applied
```

### Desired changes
```
app/Http/Controllers/DashboardController.php          (CREATE)
app/Http/Controllers/CertificateController.php        (CREATE)
app/Providers/AppServiceProvider.php                  (MODIFY — add Carbon locale)
routes/web.php                                        (MODIFY — add 2 named routes in auth group)
resources/views/dashboard.blade.php                   (MODIFY — append "Lanjutkan belajar" + "Aktivitas terbaru")
database/factories/CourseFactory.php                  (CREATE)
database/factories/RegistrationFactory.php            (CREATE)
database/factories/CertificateFactory.php             (CREATE)
database/factories/PaymentFactory.php                 (CREATE)
tests/Feature/DashboardTest.php                       (CREATE)
tests/Feature/CertificateIndexTest.php                (CREATE)
```

### Known gotchas
```php
// CRITICAL: routes/web.php currently has NO /dashboard or /certificates route.
// The navbar links to them but they 404. This is the primary fix.

// CRITICAL: Certificate model uses $dates = ['issued_at'] (Laravel 11+ deprecated form).
// In Laravel 12 the preferred form is $casts = ['issued_at' => 'datetime']. Don't change
// existing model unless asked, but be aware Carbon parsing still works either way.

// CRITICAL: Payment status enum includes BOTH 'paid' and 'approved' — confusing.
// Existing code (UserCourseController/blade) treats 'approved' as "paid successfully."
// Mirror that — treat 'approved' as the success status for payments.

// CRITICAL: Registration status enum is 'pending' | 'approved' | 'rejected'.
// "Lanjutkan belajar" rail should filter to status='approved'.

// CRITICAL: Without Carbon::setLocale('id') in AppServiceProvider::boot(), 
// $event->created_at->diffForHumans() returns "2 hours ago" not "2 jam lalu".

// CRITICAL: Tailwind v4 — use bg-(--color-brand-600). Never bg-blue-600.

// CRITICAL: factories for Course/Registration/Certificate/Payment don't exist.
// We must create them for the tests to work.

// CRITICAL: tests/TestCase is bare. Tests using RefreshDatabase must `use` the trait directly.
```

## Implementation Blueprint

### Data shape produced by `DashboardController@index`

```php
return view('dashboard', [
    // existing keys (don't break the view)
    'coursesCount'      => int,    // count of user's registrations (any status)
    'certificatesCount' => int,    // count of user's certificates
    // new keys
    'continueLearning'  => Collection<Registration>,   // approved registrations, with course, latest 3
    'activities'        => Collection<array>,          // [{type, title, timestamp:Carbon, icon, url?}], max 5
]);
```

### Activity aggregation algorithm

```php
// Three sources, normalized into one shape, then merged + sorted + sliced.
$regs = Registration::with('course')
    ->where('user_id', auth()->id())
    ->get()
    ->map(fn ($r) => [
        'type'      => 'registration',
        'title'     => 'Mendaftar kursus '.$r->course->title,
        'timestamp' => $r->created_at,
        'icon'      => 'book',
        'url'       => route('courses.show', $r->course_id),
    ]);

$payments = Payment::with('registration.course')
    ->whereHas('registration', fn ($q) => $q->where('user_id', auth()->id()))
    ->whereIn('status', ['approved', 'paid'])
    ->get()
    ->map(fn ($p) => [
        'type'      => 'payment',
        'title'     => 'Pembayaran berhasil untuk '.$p->registration->course->title,
        'timestamp' => $p->paid_at ?? $p->updated_at,
        'icon'      => 'check-badge',
        'url'       => null,
    ]);

$certs = Certificate::with('course')
    ->where('user_id', auth()->id())
    ->get()
    ->map(fn ($c) => [
        'type'      => 'certificate',
        'title'     => 'Sertifikat diterbitkan: '.$c->course->title,
        'timestamp' => $c->issued_at ?? $c->created_at,
        'icon'      => 'star',
        'url'       => route('certificate.download', $c->id),
    ]);

$activities = $regs->concat($payments)->concat($certs)
    ->sortByDesc('timestamp')
    ->take(5)
    ->values();
```

### List of tasks in execution order

```yaml
Task 1 — Set Carbon locale:
  MODIFY app/Providers/AppServiceProvider.php
  - FIND: "public function boot(): void"
  - APPEND inside the method body, alongside Payment::observe(...):
      \Carbon\Carbon::setLocale('id');
      // optional: also set Laravel app locale for resolved Carbon instances
      \Illuminate\Support\Carbon::setLocale('id');

Task 2 — Create DashboardController:
  CREATE app/Http/Controllers/DashboardController.php
  - PATTERN: mirror UserCourseController structure (constructor-less, single index() method).
  - Use the activity aggregation algorithm above.
  - Always eager-load relations.
  - Return view('dashboard', compact(...)).

Task 3 — Create CertificateController:
  CREATE app/Http/Controllers/CertificateController.php
  - PATTERN: trivial — same pattern as UserCourseController::index.
  - Load $certificates = Certificate::with('course')->where('user_id', auth()->id())->latest('issued_at')->get();
  - return view('certificates', compact('certificates'));

Task 4 — Wire routes:
  MODIFY routes/web.php
  - Inside the existing Route::middleware('auth')->group(function () { ... }) block,
    APPEND:
      Route::get('/dashboard',    [\App\Http\Controllers\DashboardController::class, 'index'])
          ->name('dashboard');
      Route::get('/certificates', [\App\Http\Controllers\CertificateController::class, 'index'])
          ->name('certificates.index');

Task 5 — Extend dashboard view:
  MODIFY resources/views/dashboard.blade.php
  - LOCATE: end of the KPI grid div (after the "Status" article).
  - INSERT between KPI grid and the "Akses cepat" heading:
      * "Lanjutkan belajar" section (h2 + horizontal rail of cards, max 3)
      * "Aktivitas terbaru" section (h2 + ul with icon + title + relative timestamp)
  - Use ONLY semantic tokens. Mirror card classes from existing KPI cards.
  - Empty state for "Lanjutkan belajar" when collection is empty: friendly message + CTA to /#kursus.
  - Empty state for "Aktivitas terbaru": "Belum ada aktivitas."

Task 6 — Factories for tests:
  CREATE database/factories/CourseFactory.php
  CREATE database/factories/RegistrationFactory.php
  CREATE database/factories/CertificateFactory.php
  CREATE database/factories/PaymentFactory.php
  - PATTERN: mirror UserFactory shape.
  - CourseFactory: title=fake()->sentence(3), description, price=rand 100_000 — 1_000_000 (int), defaults for the rest.
  - RegistrationFactory: user_id + course_id from factories, status='approved' default.
  - CertificateFactory: certificate_number=fake()->unique()->bothify('CERT-####'), issued_at=now().
  - PaymentFactory: amount=fake()->numberBetween(...), status='approved' default, paid_at=now().

Task 7 — Tests:
  CREATE tests/Feature/DashboardTest.php
  - Use Illuminate\Foundation\Testing\RefreshDatabase trait.
  - Cases:
      1. test_guest_is_redirected_to_login
      2. test_authenticated_user_sees_dashboard
      3. test_dashboard_shows_correct_counts_for_user
      4. test_dashboard_does_not_leak_other_users_data
      5. test_continue_learning_only_shows_approved_registrations_max_3
      6. test_activities_section_shows_newest_first_max_5

  CREATE tests/Feature/CertificateIndexTest.php
  - Cases:
      1. test_guest_redirected
      2. test_user_sees_only_own_certificates
```

### Per-task pseudocode

```php
// Task 2 — DashboardController::index
namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        // PATTERN: pre-load with(); avoid N+1.
        $registrations = Registration::with('course')
            ->where('user_id', $userId)
            ->get();

        $continueLearning = $registrations
            ->where('status', 'approved')
            ->sortByDesc('created_at')
            ->take(3)
            ->values();

        $payments = Payment::with('registration.course')
            ->whereHas('registration', fn ($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['approved', 'paid'])
            ->get();

        $certificates = Certificate::with('course')
            ->where('user_id', $userId)
            ->get();

        $activities = $registrations->map(fn ($r) => [
            'type'      => 'registration',
            'title'     => 'Mendaftar kursus '.$r->course->title,
            'timestamp' => $r->created_at,
            'icon'      => 'book',
            'url'       => route('courses.show', $r->course_id),
        ])
        ->concat($payments->map(fn ($p) => [
            'type'      => 'payment',
            'title'     => 'Pembayaran berhasil untuk '.$p->registration->course->title,
            'timestamp' => $p->paid_at ?? $p->updated_at,
            'icon'      => 'check-badge',
            'url'       => null,
        ]))
        ->concat($certificates->map(fn ($c) => [
            'type'      => 'certificate',
            'title'     => 'Sertifikat diterbitkan: '.$c->course->title,
            'timestamp' => $c->issued_at ?? $c->created_at,
            'icon'      => 'star',
            'url'       => route('certificate.download', $c->id),
        ]))
        ->sortByDesc('timestamp')
        ->take(5)
        ->values();

        return view('dashboard', [
            'coursesCount'      => $registrations->count(),
            'certificatesCount' => $certificates->count(),
            'continueLearning'  => $continueLearning,
            'activities'        => $activities,
        ]);
    }
}
```

```blade
{{-- Task 5 — view additions (between KPI grid and "Akses cepat") --}}

{{-- ============== LANJUTKAN BELAJAR ============== --}}
<section class="mt-12">
    <div class="flex items-end justify-between mb-4">
        <h2 class="font-display text-2xl tracking-tight text-(--color-text)">Lanjutkan belajar</h2>
        <a href="{{ url('/my-courses') }}"
           class="text-sm font-medium text-(--color-brand-600) hover:text-(--color-brand-700)">
            Lihat semua →
        </a>
    </div>

    @if($continueLearning->isEmpty())
        <div class="rounded-xl border border-dashed border-(--color-border-strong) bg-(--color-surface) p-8 text-center">
            <p class="text-(--color-text-muted)">Belum ada kursus yang aktif.</p>
            <a href="/#kursus"
               class="mt-3 inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) transition">
                Jelajahi kursus
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($continueLearning as $reg)
                <a href="{{ route('courses.show', $reg->course_id) }}"
                   class="block p-5 rounded-xl bg-(--color-surface) border border-(--color-border) hover:border-(--color-brand-500) hover:shadow-md transition">
                    <span class="text-xs uppercase tracking-wider font-semibold text-(--color-brand-600)">Kursus aktif</span>
                    <h3 class="mt-2 font-semibold text-(--color-text) line-clamp-2">{{ $reg->course->title }}</h3>
                    <p class="mt-2 text-xs text-(--color-text-subtle)">
                        Terdaftar {{ $reg->created_at->diffForHumans() }}
                    </p>
                </a>
            @endforeach
        </div>
    @endif
</section>

{{-- ============== AKTIVITAS TERBARU ============== --}}
<section class="mt-12">
    <h2 class="font-display text-2xl tracking-tight text-(--color-text) mb-4">Aktivitas terbaru</h2>

    @if($activities->isEmpty())
        <p class="text-(--color-text-muted)">Belum ada aktivitas.</p>
    @else
        <ul role="list" class="space-y-2">
            @foreach($activities as $a)
                <li class="flex items-start gap-3 p-4 rounded-xl bg-(--color-surface) border border-(--color-border)">
                    {{-- Inline an icon based on $a['icon'] using Heroicons SVG. Use a Blade @switch or a tiny x-data partial. --}}
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-(--color-brand-50) text-(--color-brand-700) shrink-0">
                        {{-- Heroicon SVG goes here, conditional by $a['icon'] --}}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-(--color-text)">{{ $a['title'] }}</p>
                        <p class="text-xs text-(--color-text-subtle) mt-0.5">
                            {{ $a['timestamp']->diffForHumans() }}
                        </p>
                    </div>
                    @if($a['url'])
                        <a href="{{ $a['url'] }}" class="text-sm font-medium text-(--color-brand-600) hover:text-(--color-brand-700)">Buka</a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</section>
```

```php
// Task 7 — Test sketch
namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_dashboard_shows_correct_counts_for_user(): void
    {
        $user = User::factory()->create();
        Registration::factory()->count(3)->for($user)->create();
        Certificate::factory()->count(2)->for($user)->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee('3')   // courses count
            ->assertSee('2'); // certificates count
    }

    public function test_dashboard_does_not_leak_other_users_data(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        Registration::factory()->for($userB)->create([
            'course_id' => Course::factory()->create(['title' => 'OTHER USER COURSE'])->id,
        ]);

        $this->actingAs($userA)
            ->get(route('dashboard'))
            ->assertDontSee('OTHER USER COURSE');
    }
}
```

### Integration points
```yaml
DATABASE:
  - migrations: NONE (no schema change)

ROUTES:
  - add to: routes/web.php
  - inside: existing Route::middleware('auth')->group(...) block
  - names: 'dashboard', 'certificates.index'

NAVBAR:
  - file: resources/views/layouts/app.blade.php
  - status: ALREADY links to /dashboard and /certificates — no change needed.

PROVIDERS:
  - file: app/Providers/AppServiceProvider.php
  - change: add Carbon::setLocale('id') in boot()

DESIGN SYSTEM:
  - reference: design-system/MASTER.md §5 (Components — card pattern, list-item pattern)
  - no new component category required
```

## Validation Loop

### Level 1 — Syntax & Style
```bash
vendor/bin/pint app/Http/Controllers/DashboardController.php
vendor/bin/pint app/Http/Controllers/CertificateController.php
vendor/bin/pint app/Providers/AppServiceProvider.php
php artisan view:clear
```
**Expected:** No errors.

### Level 2 — Tests
```bash
php artisan test --filter=DashboardTest
php artisan test --filter=CertificateIndexTest
```
**Expected:** all green. If failing, read the error, do NOT mock to pass — fix the controller or view.

### Level 3 — Manual smoke
```bash
php artisan serve
# in another shell
npm run dev
```
Then in browser:
1. Visit `/dashboard` while logged out → should redirect to `/login`.
2. Log in, visit `/dashboard` → KPI cards + "Lanjutkan belajar" + "Aktivitas terbaru" + "Akses cepat" all render.
3. Toggle dark mode — both sections still readable, tokens correct.
4. Visit `/certificates` → list renders without 404.
5. Open DevTools → no console errors, Alpine widgets in navbar still work.

## Final validation checklist
- [ ] `vendor/bin/pint --test` clean on all modified files
- [ ] `php artisan test` green
- [ ] `npm run build` succeeds
- [ ] `php artisan route:list | grep dashboard` shows the route, with auth middleware
- [ ] `php artisan route:list | grep certificates.index` shows the route, with auth middleware
- [ ] `\Carbon\Carbon::now()->diffForHumans()` returns "beberapa detik lalu" in tinker (locale set)
- [ ] No raw Tailwind colors (`bg-blue-*`, `text-red-*`) added to dashboard.blade.php
- [ ] Works in light AND dark theme
- [ ] No N+1 (Telescope, Debugbar, or a `DB::listen` assertion in test)
- [ ] All visible new copy is Bahasa Indonesia

---

## Anti-Patterns to Avoid
- ❌ Adding a `dashboards` table or new migration — we have no need
- ❌ Doing the activity aggregation in Blade with multiple `@foreach` over raw model queries
- ❌ Calling Eloquent inside Blade `@foreach`
- ❌ Using `bg-indigo-600` / hardcoded hex
- ❌ Skipping Carbon locale setting "because diffForHumans still works"
- ❌ Skipping the empty-state UI
- ❌ Mocking auth to pass tests instead of using `actingAs()`
- ❌ Forgetting to name the routes

---

## Confidence Score: **8 / 10**

**Why 8 (not 10):**
- Strong: every needed file path exists, all column names verified, the algorithm is fully spelled out, validation is executable.
- Risk 1: Heroicons SVG for the activity timeline is sketched but not fully inlined per icon type — implementer must paste the right `book`/`check-badge`/`star` SVG. Easy but a place to slip.
- Risk 2: Existing `tests/TestCase.php` is bare. RefreshDatabase has to be `use`d per test class. If `phpunit.xml` doesn't have a test DB configured, the first test run may need a `.env.testing` setup. Document but don't pre-empt.
- Risk 3: `Payment` enum has both `paid` and `approved`. PRP says treat `approved` as success but the activity query includes both — verify with the user if any seeded payments use `paid` semantically.

**Mitigation built into the PRP:** all three are called out explicitly above with file references.
