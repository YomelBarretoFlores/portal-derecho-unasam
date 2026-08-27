# Repository Guidelines

## Project Structure & Module Organization

This Laravel 13 portal uses Filament 5 for its CMS. Application code lives in `app/`: controllers in `Http/Controllers`, models in `Models`, reusable operations in `Services`, and admin resources in `Filament/Resources`. Routes are in `routes/`; Blade pages and shared sections are in `resources/views`. Store public images and built assets in `public/`. Migrations, factories, and seeders belong in `database/`. Put isolated tests in `tests/Unit` and route, authorization, or database tests in `tests/Feature`. Architecture and content provenance are documented in `ARQUITECTURA.md` and `docs/`.

## Build, Test, and Development Commands

- `composer run setup`: install dependencies, initialize `.env`, migrate, and build assets.
- `php artisan storage:link`: expose locally uploaded media through `public/storage`.
- `php artisan migrate --seed`: build and populate the local database.
- `composer run dev`: start the server, queue, logs, and Vite watcher.
- `composer test` or `php artisan test`: clear configuration and run PHPUnit.
- `./vendor/bin/pint --test`: check PHP formatting; omit `--test` to apply fixes.
- `npm run build`: create the production Vite bundle.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF endings, four-space indentation, and two spaces for YAML. Use PascalCase classes (`RevistaService`), camelCase methods, snake_case database fields, and timestamp-prefixed migrations. Name Blade files and route slugs in lowercase kebab-case. Format PHP with Laravel Pint. Keep controllers thin; place reusable domain logic in services or model concerns.

## Testing Guidelines

PHPUnit 12 uses in-memory SQLite through `phpunit.xml`. End test class names with `Test.php`, for example `EditorialPublishingTest.php`. Add focused regression coverage for visibility, editorial workflows, permissions, uploads, and changed routes. No coverage percentage is mandated. Before opening a pull request, run tests, Pint, and the frontend build.

## Commit & Pull Request Guidelines

Recent commits use short, imperative Spanish summaries, such as `Corrige vistas previas y avatares docentes`. Keep each commit focused. Pull requests should explain the problem and solution, identify migrations or environment changes, link issues, and include screenshots for public or Filament UI changes. Confirm CI passes before requesting review.

## Security & Configuration

Copy `.env.example` and keep secrets, credentials, and personal data out of Git. Remove temporary `ADMIN_*` values after bootstrapping an administrator. Do not enable uploads on ephemeral production storage; follow `README.md` and `DEPLOY.md`.
