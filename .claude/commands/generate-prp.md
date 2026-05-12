# Create PRP

## Feature file: $ARGUMENTS

Generate a complete PRP for a feature implementation in this **Laravel 12 + Tailwind v4 + Alpine.js** project. Ensure context is passed to the AI agent to enable self-validation and iterative refinement. Read the feature file (`INITIAL.md` or the supplied path) first to understand what needs to be built, how the examples help, and any considerations.

The AI agent only gets the context appended to the PRP plus training data. Assume it has codebase access and the same knowledge cutoff as you — so research findings must be in the PRP. The agent has WebSearch capabilities; include URLs to docs and examples.

## Research Process

1. **Codebase Analysis**
   - Search for similar features/patterns. Use `Grep` and `Glob` over `app/`, `resources/views/`, `routes/`.
   - Identify files to reference in the PRP (controllers, models, blade views, migrations).
   - Check `routes/web.php` to confirm routing patterns — many myKursus features currently have unrouted views.
   - Note existing conventions: how controllers are organized, how Blade extends layouts, how forms use `@csrf` + FormRequest.
   - Check `design-system/MASTER.md` for relevant component patterns and tokens.
   - Identify test patterns under `tests/Feature/` (if present).

2. **External Research**
   - Laravel docs (specific version 12.x sections) — include URLs.
   - Tailwind v4 docs (`@theme`, CSS variable arbitrary syntax) when styling work is involved.
   - Alpine.js docs for interactivity.
   - Midtrans Snap docs when payment-adjacent.
   - Implementation examples from GitHub/Laravel News/blogs.

3. **User Clarification** (if needed)
   - Specific patterns to mirror and where?
   - Integration requirements (routing, navbar, dashboard, etc.)?
   - Indonesian copy tone?

## PRP Generation

Using `PRPs/templates/prp_base.md` as template:

### Critical Context to Include and pass to the AI agent as part of the PRP
- **Documentation**: URLs with specific sections
- **Code Examples**: Real snippets from the myKursus codebase
- **Gotchas**: Laravel quirks, Tailwind v4 syntax surprises, Alpine.js cloaking, Midtrans constraints, MySQL collation, locale settings
- **Patterns**: Existing controllers/models/views to mirror

### Implementation Blueprint
- Start with high-level approach (pseudocode if helpful, but PHP/Blade are usually clear enough as task lists).
- Reference real files for patterns (e.g. "mirror `UserCourseController::index` for `DashboardController::index`").
- Include error handling and authorization strategy (Policies, middleware).
- List tasks in execution order. Tasks should specify `CREATE` or `MODIFY` and the exact file path.

### Validation Gates (Must be Executable)
```bash
# Syntax / Style
vendor/bin/pint --test            # or `vendor/bin/pint` to autofix
php -l app/Http/Controllers/<NewController>.php

# Tests
php artisan test --filter=<NewFeatureTest>

# Build (UI changes)
npm run build

# Migration (DB changes)
php artisan migrate --pretend
```

*** CRITICAL: AFTER YOU FINISH RESEARCHING AND EXPLORING THE CODEBASE, BEFORE YOU START WRITING THE PRP ***

*** ULTRATHINK ABOUT THE PRP AND PLAN YOUR APPROACH, THEN START WRITING ***

## Output

Save as: `PRPs/{feature-name}.md`

## Quality Checklist

- [ ] All necessary context included (file paths, line numbers when useful)
- [ ] Validation gates are executable (Pint, artisan test, npm build, migrate --pretend)
- [ ] References existing patterns in the myKursus codebase
- [ ] Clear, ordered implementation path
- [ ] Error handling and authorization documented
- [ ] Route wiring explicitly listed (since several myKursus features have unwired views)
- [ ] Design system tokens referenced for UI work (no raw Tailwind colors)
- [ ] Indonesian copy provided where user-facing

Score the PRP on a scale of 1–10 (confidence level to succeed in one-pass implementation using Claude Code).

**Remember:** The goal is one-pass implementation success through comprehensive context.
