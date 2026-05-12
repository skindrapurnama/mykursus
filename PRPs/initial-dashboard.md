## FEATURE:

**Wire up and enhance the authenticated user Dashboard at `/dashboard`.**

The Blade view `resources/views/dashboard.blade.php` already exists (refactored to use the design-system tokens), but there is **no route** for `/dashboard` in `routes/web.php` and **no `DashboardController`**. As a result the navbar link `{{ url('/dashboard') }}` 404s today.

We want to:

1. **Wire** the route: `GET /dashboard` → `DashboardController@index`, named `dashboard`, behind the `auth` middleware.
2. **Build** the controller to compute the data the view already expects (`$coursesCount`, `$certificatesCount`) plus a richer set of metrics described below.
3. **Enhance** the dashboard with two new sections (without breaking the current refactored layout):
   - **"Lanjutkan belajar"** rail — list of the user's most-recently-registered courses with `approved` status, max 3, link to course detail.
   - **"Aktivitas terbaru"** timeline — list of last 5 activity items aggregated from registrations + payments + certificates (each rendered as a row with icon, title, timestamp).
4. **Also wire** the related `/certificates` route (`CertificateController@index`) so the existing `certificates.blade.php` view stops 404'ing. View already expects `$certificates`.

Both `/dashboard` and `/certificates` must:
- Require auth (redirect guests to `/login`).
- Eager-load relations to avoid N+1.
- Render in both light and dark mode (already styled).

## EXAMPLES:

Mirror these existing files (read them before generating the PRP):

- `app/Http/Controllers/UserCourseController.php` — canonical pattern for "list things for the current user with eager loading." Note `Registration::with(['course', 'payment', 'user'])->where('user_id', auth()->id())->latest()->get()`.
- `resources/views/user/courses.blade.php` — example of card-list rendering with semantic tokens, status badges via `match()`.
- `resources/views/dashboard.blade.php` — current state. Already expects `$coursesCount` and `$certificatesCount`. Add new sections **after** the KPI grid, **before** the existing "Akses cepat" section. Reuse its card styling.
- `resources/views/certificates.blade.php` — already-styled list view; needs only its controller + route.

## DOCUMENTATION:

- Laravel routing: <https://laravel.com/docs/12.x/routing#middleware>
- Eloquent eager loading: <https://laravel.com/docs/12.x/eloquent-relationships#eager-loading>
- Carbon localization for `translatedFormat()`: <https://carbon.nesbot.com/docs/#api-localization>
- `design-system/MASTER.md` §5 (Components), §6 (Motion) — use card pattern from §5; activity row should follow list-item conventions.

## OTHER CONSIDERATIONS:

- **Carbon locale.** `translatedFormat('d M Y')` will output English unless `\Carbon\Carbon::setLocale('id')` is called. Check `app/Providers/AppServiceProvider.php` — add it in `boot()` if missing.
- **Route naming.** Use `dashboard` (singular, no prefix). Keep `certificates.index` for the certificate list — `certificate.download` already exists.
- **Navbar.** The existing layout (`resources/views/layouts/app.blade.php`) already links to `/dashboard`, `/my-courses`, `/certificates` in the `@auth` section. After wiring, the links work automatically.
- **Activity timeline aggregation.** A pragmatic approach: in PHP, merge three Eloquent collections (registrations, payments, certificates) ordered by `created_at` (or `issued_at` / `paid_at`), then `->sortByDesc()->take(5)`. Each entry should expose `type`, `title`, `timestamp`, `icon`. Do not over-engineer this — no separate "activities" table.
- **No new migrations** unless absolutely necessary. The data model already supports everything above.
- **Tokens only.** Any new markup must use `bg-(--color-*)`, never raw Tailwind colors.
- **N+1 risk.** Each activity item references its related model (course title, payment status). Pre-load these.
- **Tests.** Create `tests/Feature/DashboardTest.php`:
  - Guest is redirected to login.
  - Authenticated user sees their stats (counts match seeded data).
  - User does not see another user's stats.
- **i18n copy.** All visible strings in Bahasa Indonesia. Section labels: "Lanjutkan belajar", "Aktivitas terbaru".
- **Empty states.** If user has zero registrations, "Lanjutkan belajar" shows a friendly empty state + CTA to `/#kursus`. If zero activity, show a placeholder line.
- **Existing dashboard.blade.php must remain valid.** The new sections are *additions*; don't break what's already rendered.
