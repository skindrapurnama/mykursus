# Execute BASE PRP

Implement a feature using the PRP file.

## PRP File: $ARGUMENTS

## Execution Process

1. **Load PRP**
   - Read the specified PRP file.
   - Read `CLAUDE.md` for global project rules (Laravel/Tailwind v4/Alpine conventions).
   - Read `design-system/MASTER.md` if the PRP involves UI work.
   - Understand all context and requirements.
   - Follow instructions in the PRP and extend research if needed.
   - Verify referenced files/routes/columns still exist (`Grep` / `Read`).
   - Do more web searches and codebase exploration as needed.

2. **ULTRATHINK**
   - Plan before executing. Address all PRP requirements in one clear plan.
   - Break down into small steps using `TaskCreate`.
   - Identify patterns in existing code to follow (controllers, blade structure, validation).
   - Confirm route wiring is part of the plan — myKursus has had unwired views before.

3. **Execute the plan**
   - Implement the code: migrations, models, controllers, views, routes, in that order.
   - Use semantic design-system tokens for any Blade markup (`bg-(--color-brand-600)` not `bg-blue-600`).
   - Use `@vite(['resources/css/app.css', 'resources/js/app.js'])` patterns from layouts.
   - Add `@section('title', '...')` for every new page.

4. **Validate**
   - Run each validation command from the PRP.
   - `vendor/bin/pint` for style.
   - `php artisan test` (filter to feature when appropriate) for behavior.
   - `npm run build` if Blade/CSS changed.
   - `php artisan migrate --pretend` (or `--seed` in dev) for schema changes.
   - Fix any failures. Re-run until all gates pass. Don't mock to pass.

5. **Complete**
   - Verify every checklist item is done.
   - Run final validation suite.
   - Read the PRP again to ensure full coverage.
   - Report what was built, what tests pass, and any deviations from the PRP.

6. **Reference the PRP**
   - Refer back as needed during execution. Update the PRP if discoveries warrant.

**Note:** If validation fails, use the error patterns in the PRP to fix and retry. Never use `--no-verify` to bypass hooks.
