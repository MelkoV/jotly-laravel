# AGENTS.md

## Project Overview

- Stack: Laravel 12, PHP 8.4, PostgreSQL 17, Vite 7, Tailwind CSS 4.
- Purpose: backend/API for Jotly with JWT-based auth, list management, and list item workflows.
- Main runtime modes:
  - local Laravel app via `php artisan serve`
  - Docker/Sail-style environment via `docker-compose.yml`

## Repository Layout

- `app/Http/API/v1/Controllers`:
  API controllers for `user`, `lists`, and `list-items`.
- `app/Http/API/v1/Requests`:
  request validation and transformation into DTOs.
- `app/Data`:
  Spatie Laravel Data DTOs used across request, service, and repository layers.
- `app/Services`:
  business logic.
- `app/Repositories`:
  persistence and query logic.
- `app/Contracts`:
  interfaces for repositories and services, bound in the container.
- `app/Http/Middleware`:
  JWT and refresh-token authentication middleware.
- `app/Models`:
  Eloquent models.
- `app/Enums`:
  domain enums used in validation and responses.
- `app/Rules`:
  custom validation/access rules.
- `routes/api.php`:
  versioned API endpoints under `/api/v1/...`.
- `tests/Feature`:
  endpoint-level API tests.
- `tests/Unit`:
  unit and repository-level tests.
- `resources`:
  minimal frontend assets plus Swagger/auto-doc views.

## Architecture Conventions

- Follow the existing flow for API changes:
  `Route -> FormRequest -> Data DTO -> ServiceContract -> Service -> RepositoryContract -> Repository`.
- Prefer extending existing contracts and bindings instead of coupling controllers directly to concrete classes.
- `AppServiceProvider` binds repository/service contracts to implementations. Keep new domain services/repositories consistent with this pattern.
- Controllers stay thin and mostly delegate to services.
- Validation and access checks belong in Form Requests and custom Rules when possible.
- DTOs in `app/Data/...` are first-class citizens in this codebase. Reuse them instead of passing loose arrays.
- Domain enums are used heavily for request validation and response values. Prefer enum-backed behavior over string literals.

## Auth and API Notes

- Auth is custom JWT-based using `firebase/php-jwt`, not standard Laravel session auth.
- Protected API routes use:
  - `App\Http\Middleware\HandleJwtToken`
  - `App\Http\Middleware\HandleRefreshJwtToken`
- User endpoints:
  - `POST /api/v1/user/sign-up`
  - `POST /api/v1/user/sign-in`
  - `POST /api/v1/user/refresh-token`
  - `GET /api/v1/user/profile`
- List endpoints live under `/api/v1/lists`.
- List item endpoints live under `/api/v1/list-items`.

## Local Setup

- Quick project bootstrap:
  - `composer run setup`
- Manual Docker flow from `README.md`:
  - copy `.env.example` to `.env`
  - update environment values
  - run `docker-compose up -d`
  - generate app key inside the container
- Dev mode via Composer:
  - `composer run dev`
- Frontend only:
  - `npm run dev`
  - `npm run build`

## Quality Gates

- Tests:
  - `php artisan test`
  - `make t`
- Full local CI-style check:
  - `make ci`
- Static analysis:
  - `./vendor/bin/phpstan analyse`
  - `make stan`
- Lint:
  - `./vendor/bin/phpcs --standard=PSR12 /var/www/html/app`
  - `make lint`
- Auto-fix style:
  - `./vendor/bin/phpcbf --standard=PSR12 /var/www/html/app`
  - `make fix`
- Mutation tests:
  - `./vendor/bin/infection`
  - `make mutate`
- Swagger docs:
  - `php artisan swagger:push-documentation`
  - `make doc`
  - `make test` runs tests and generates docs

## Testing Conventions

- Base test case: `tests/TestCase.php`.
- Tests use `DatabaseTransactions`, so prefer transaction-safe tests over manual cleanup.
- Existing helpers worth reusing:
  - `withJwtToken()`
  - `withUserJwtToken()`
  - `getJwtToken()`
  - `getUserData()`
  - `getListData()`
  - `getListItemData()`
- Feature tests assert JSON shape extensively with `AssertableJson`.
- `RonasIT\AutoDoc\Traits\AutoDocTestCaseTrait` is enabled, so API tests can feed Swagger/auto-doc generation.
- When adding or changing endpoints, update or add Feature tests first if behavior is user-visible.

## Coding Style

- Match the current PHP style used in most application files:
  - `declare(strict_types=1);`
  - constructor property promotion
  - typed return values
  - readonly services/controllers where it fits existing patterns
- Keep controllers thin.
- Keep business rules in services, repositories, request rules, or dedicated domain classes.
- Preserve naming patterns already in the codebase, even if some names are imperfect, unless the task is explicitly a refactor.
- Avoid introducing alternative architectural styles into a single feature.

## Change Guidance For Agents

- For new API behavior:
  - add or update route in `routes/api.php` when needed
  - create/update `FormRequest`
  - create/update DTO in `app/Data`
  - extend contract
  - implement service/repository behavior
  - add Feature tests
- For auth-sensitive work:
  - verify token type expectations carefully
  - add unauthorized and invalid-token coverage
- For list item updates:
  - pay attention to version checks and touched timestamps
- For docs-visible API changes:
  - ensure tests still support Swagger generation

## Known Project Specifics

- `README.md` states PHPStan level 10 and PSR-12 linting are expected.
- Coverage target in the README is high (`94,66%` at the time of writing), so avoid untested feature work.
- There is a `@todo` in `ListService::delete()` about removing users from a list through `leftUser()` in a job.
- The frontend exists but is minimal; this repository is primarily backend/API oriented.

## Environment Caveats

- In this workspace, `git` commands may fail in sandboxed runs because the repository is marked as a dubious ownership directory for the current user context.
- PowerShell profile output currently emits an `oh-my-posh` warning before command results. Ignore that noise when inspecting command output.
