# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project context

`ceidnotes` is a Laravel 5.5 (PHP >= 7.0) web app that browses and downloads class notes — directories of files organized by semester and lesson. It is a rewrite of an older PHP system; every domain model carries a `legacy_id` so records can be matched back to the previous database during migration.

`readme.md` is the unmodified Laravel framework readme and is not project-specific.

## Common commands

```bash
# PHP / Laravel
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan import:legacy        # migrates data from the "old" DB connection (see config/database.php)
php artisan serve
php artisan tinker

# Tests (PHPUnit 6, suites defined in phpunit.xml)
vendor/bin/phpunit                                   # all tests
vendor/bin/phpunit --testsuite Feature               # Feature suite only
vendor/bin/phpunit --testsuite Unit                  # Unit suite only
vendor/bin/phpunit tests/Feature/ExampleTest.php     # single file
vendor/bin/phpunit --filter testMethodName           # single test

# Frontend (Laravel Mix / webpack)
npm install
npm run dev        # one-off dev build
npm run watch      # rebuild on change
npm run prod       # production build
```

There is no lint config in the repo.

## Architecture

### Routing and request flow

`routes/web.php` is intentionally tiny. The interesting route is:

```
Route::get('notes/{path}', 'NotesController@show')->where('path', '.*')
```

`{path}` is regex-unrestricted so the same URL space addresses both directories and files. `NotesController@show` first tries `Directory::where('path', $path)` and renders `notes.index` if found; if not, it falls through to `File::where('path', $path)`, fetches the blob from `Storage::cloud()` keyed by `md5`, and returns it as a raw response. `notes/{id}` (numeric) is a separate route that hits `SemestersController@show`. Order matters in `web.php`: the numeric semester route is declared before the catch-all `notes/{path}` so it wins for digits.

`Auth::routes()` is commented out — login/register views exist but auth is not wired up. Don't assume an authenticated user in controllers.

### Path strings are the primary key for browsing

`Directory::calculatePath()` walks the parent chain to build a slash-separated path, passing each segment through the global `prepair_path()` helper (`app/Http/helpers.php`, autoloaded via `composer.json` `autoload.files`) which replaces spaces with `+`. `File::calculatePath()` concatenates its directory's path with the filename (filenames are *not* run through `prepair_path`'s space substitution — the `$is_file` branch returns the name unchanged). These `path` columns are what the router matches against, so any code that mutates a directory or file name must call `calculatePath()->save()` afterward, and a directory rename in principle requires recalculating every descendant's path (not currently implemented anywhere — be aware before adding write paths).

### Domain models

All models live directly in `app/` (Laravel 5.x convention, no `app/Models/`). The shape:

- `Directory` is self-referential (`directory_id` → parent) and `hasMany` files.
- `Semester` `hasMany` `Lesson`, and a `Lesson` points to a `Directory` — that's how the semester/lesson browse landing pages connect into the directory tree.
- `Like`, `Report`, `Edit` are **polymorphic** (`morphTo` on `likeable` / `reportable` / `editable`) — they can attach to either a `File` or a `Directory`. When adding a new attachable type, register it on both sides.
- `File` ↔ `Label` is a `belongsToMany` with timestamps on the pivot.
- Every domain model uses `SoftDeletes` and stores a `deleted_by_user_id`. Most models define a custom `scopeWithoutTimestamps()` that flips `$this->timestamps = false` and returns `$this` — used to bulk-update rows (e.g. the download counter increment in `NotesController@show`) without bumping `updated_at`. Chain it before `save()`/`update()`.

### File storage and caching

Uploaded file blobs are stored on `Storage::cloud()` (S3 by default; Azure also configured in `config/filesystems.php`) at a key equal to the file's `md5` column. On every download, `NotesController@show` does `Cache::store('file')->rememberForever("file_{$md5}", ...)` to cache the full response body on the **local file cache** (`storage/framework/cache/data`), so the second hit avoids S3. Implications:

- Cached responses include the `Content-Type` header captured at first fetch.
- There is no eviction — when a file's blob is replaced, the `md5` should change (which naturally invalidates the cache key); never reuse an `md5` for different content.
- The `total_downloads` / `total_overall` counters increment on every request including cache hits, written via `withoutTimestamps()->save()`.

### Two database connections

`config/database.php` defines `mysql` (default) and a second `old` connection driven by `OLD_DB_DATABASE`. The only consumer is `app/Console/Commands/ImportLegacyData.php` (`php artisan import:legacy`), which reads from `old` and upserts into the new schema keyed by `legacy_id`. The import order matters — users → phones → labels → directories → files → semesters → lessons → likes → reports → edits — because later steps look up earlier `legacy_id`s. If you add a new model that needs legacy import, follow the same `updateOrCreate(['legacy_id' => ...], [...])` pattern and add it in dependency order.

### Views and frontend

Blade templates in `resources/views/` extend `layouts.app`. The UI is **Bulma** (see `notes/index.blade.php` classes like `is-bordered`, `is-narrow`). `package.json` also pulls in `bootstrap-sass`, `jquery`, and `vue` from the default Laravel scaffolding but Bulma is what's actually rendered. `webpack.mix.js` compiles `resources/assets/js/app.js` → `public/js` and `resources/assets/sass/app.scss` → `public/css`; compiled bundles are gitignored.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
