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
