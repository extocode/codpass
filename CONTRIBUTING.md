# Contributing to codPass

Thanks for your interest in improving codPass. This guide covers how to set up
the project, run the checks, and get a change merged.

## About the project

codPass is an unofficial community fork of
[sysPass](https://github.com/nuxsmin/sysPass) by Rubén Domínguez (nuxsmin), a
PHP web-based password manager. Upstream has had no release since 3.2.11, so
codPass continues maintenance and modernization (PHP 8.4+, Symfony 8,
PHPUnit 13, a Vite asset pipeline).

codPass is licensed under the **GNU GPLv3**, the same license as sysPass. All
original copyright notices in source headers are retained — please keep them
intact, and add your own only where you create new files. By contributing you
agree to license your work under GPLv3.

## Requirements

- **PHP 8.4+** with extensions: `pdo`, `dom`, `gd`, `json`, `gettext`,
  `fileinfo`, `zlib`, `libxml`, `mbstring` (plus `ldap`/`curl` for those
  features, and `xdebug` if you want coverage).
- **MariaDB / MySQL** (CI runs against MariaDB 10.6).
- **Composer** (v2).
- **Node.js 18+** and npm 9+ (only needed to build front-end assets).

## Local setup

Clone the repo, then install dependencies and build the assets:

```bash
composer install          # include dev deps for tooling and tests
npm install
npm run build             # compiles resources/ → public/dist/ via Vite
```

Open the app in a browser and follow the web installer. See
[docs/INSTALL.md](docs/INSTALL.md) and [docs/CONFIGURATION.md](docs/CONFIGURATION.md)
for details.

### Docker (recommended for a quick dev stack)

A minimal PHP 8.4 + Apache + MariaDB stack lives in [docker/](docker/):

```bash
docker compose up --build
```

Then open <http://localhost:8080> and run the installer. Full details and the
database settings to enter are in [docker/README.md](docker/README.md).

While iterating on front-end assets, `npm run dev` (or `npm run watch`) serves
them through Vite instead of the built bundle.

## Running tests

Tests use PHPUnit 13 and are configured by `tests/phpunit.xml`:

```bash
composer test            # full suite
```

The config enables coverage by default. For a fast local run without Xdebug,
add `--no-coverage`:

```bash
vendor/bin/phpunit --configuration tests/phpunit.xml --no-coverage
```

Two testsuites can be run in isolation with `--testsuite`:

- **Core** — everything under `tests/SP/` except the modules.
- **Modules** — the web/API module tests under `tests/SP/Modules/`.

Tests are also tagged by group — `#[Group('unit')]` (no DB/container, pure
logic) and `#[Group('integration')]` (needs DB/DI):

```bash
vendor/bin/phpunit --configuration tests/phpunit.xml --no-coverage --group unit
```

Test data is built with Faker generators under `tests/SP/Generators/`.

Many tests hit a real database. CI provisions a `syspass_test` MariaDB and
passes connection details via `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD`
(see `.github/workflows/ci.yml`); set the same variables locally if you run the
database-backed suites.

## Code style

codPass follows **PSR-12**, enforced by php-cs-fixer (config:
`.php-cs-fixer.php`, scope: `lib/SP`, `app/modules/web`, `app/modules/api`).

```bash
composer cs-check         # dry-run, shows the diff
composer cs-fix           # apply fixes
```

Run `composer cs-check` before opening a PR — CI fails on style violations.

## Static analysis

PHPStan is configured in `phpstan.neon` with a baseline
(`phpstan-baseline.neon`):

```bash
composer phpstan
```

Don't add new errors. If a finding is a genuine false positive, prefer fixing
the code; only regenerate the baseline (`composer phpstan:baseline`) when
agreed in review.

## Architecture orientation

A quick map so you know where things live:

- **`lib/SP/`** — the core library, PSR-4 namespace `SP\` (flat layout:
  `Core`, `Config`, `DataModel`, `Repositories`, `Services`, `Mvc`, `Http`,
  `Html`, `Providers`, `Storage`, `Util`, `Plugin`).
- **`app/modules/web/`** — the web UI module, namespace `SP\Modules\Web\`
  (controllers, views, themes).
- **`app/modules/api/`** — the JSON-RPC API module, namespace `SP\Modules\Api\`.
- **`resources/`** — front-end sources (JS/SCSS) compiled by Vite into
  `public/dist/`; `vite.config.js` is at the repo root.
- **`tests/SP/`** — the test suite, namespace `SP\Tests\`, mirroring the
  library layout.
- Front controllers: `index.php` (web) and `api.php` (API); `router.php` is the
  PHP built-in-server router.

## Branch & PR flow

1. Branch off `main`.
2. Make focused commits using
   [Conventional Commits](https://www.conventionalcommits.org/) — e.g.
   `feat:`, `fix:`, `chore:`, `docs:`, `refactor:`, `test:`.
3. Before pushing, run the same gates CI runs:
   `composer cs-check`, `composer phpstan`, and the PHPUnit suite.
4. Open a PR against `main` with a clear description of what and why. Link any
   related issue. Issue/PR templates live in
   [.github/ISSUE_TEMPLATE](.github/ISSUE_TEMPLATE).
5. Keep PRs scoped to one concern — smaller is easier to review and merge.

## Reporting security issues

Please **do not** open public issues for vulnerabilities. Report them privately
as described in [SECURITY.md](SECURITY.md), and allow time for a fix before
disclosure.
