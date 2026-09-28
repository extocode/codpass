# Contributing to codPass

Thank you for your interest in codPass. This guide tells you how to set up
the project, run the checks, and get a change merged.

## About the project

codPass is an unofficial community fork of
[sysPass](https://github.com/nuxsmin/sysPass) by Rubén Domínguez (nuxsmin).
sysPass is a PHP web-based password manager. Upstream has no release after 3.2.11.
Thus, codPass continues the maintenance and modernization (PHP 8.4+, Symfony 8,
PHPUnit 13, a Vite asset pipeline).

codPass uses the **GNU GPLv3** license, the same license as sysPass. All
original copyright notices in the source headers stay. Keep them intact.
Add your own notices only in new files that you create. When you contribute,
you agree to license your work under GPLv3.

## Requirements

- **PHP 8.4+** with extensions: `pdo`, `dom`, `gd`, `json`, `gettext`,
  `fileinfo`, `zlib`, `libxml`, `mbstring` (plus `ldap`/`curl` for those
  features, and `xdebug` if you want coverage).
- **MariaDB / MySQL** (CI runs against MariaDB 10.6).
- **Composer** (v2).
- **Node.js 18+** and npm 9+ (necessary only to build the front-end assets).

## Local setup

Clone the repo. Then install the dependencies and build the assets:

```bash
composer install          # include dev deps for tooling and tests
npm install
npm run build             # compiles resources/ → public/dist/ via Vite
```

Open the app in a browser. Then follow the steps of the web installer. For details, see
[docs/INSTALL.md](docs/INSTALL.md) and [docs/CONFIGURATION.md](docs/CONFIGURATION.md).

### Docker (recommended for a quick dev stack)

A minimal PHP 8.4 + Apache + MariaDB stack is in [docker/](docker/):

```bash
docker compose up --build
```

Then open <http://localhost:8080>. Run the installer. [docker/README.md](docker/README.md)
gives the full details and the database settings for the installer.

When you work on the front-end assets, use `npm run dev` (or `npm run watch`).
This command serves the assets through Vite instead of the built bundle.

## Run the tests

The tests use PHPUnit 13. `tests/phpunit.xml` configures them:

```bash
composer test            # full suite
```

The config enables coverage by default. For a fast local run without Xdebug,
add `--no-coverage`:

```bash
vendor/bin/phpunit --configuration tests/phpunit.xml --no-coverage
```

You can run two testsuites in isolation with `--testsuite`:

- **Core**: everything under `tests/SP/`, except the modules.
- **Modules**: the web/API module tests under `tests/SP/Modules/`.

The tests also have group tags: `#[Group('unit')]` (no DB/container, pure
logic) and `#[Group('integration')]` (needs DB/DI):

```bash
vendor/bin/phpunit --configuration tests/phpunit.xml --no-coverage --group unit
```

Faker generators under `tests/SP/Generators/` build the test data.

Many tests use a real database. CI provisions a `syspass_test` MariaDB. It gives
the connection details through `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD`
(see `.github/workflows/ci.yml`). If you run the database-backed suites locally,
set the same variables.

## Code style

codPass follows **PSR-12**. php-cs-fixer enforces it (config:
`.php-cs-fixer.php`, scope: `lib/SP`, `app/modules/web`, `app/modules/api`).

```bash
composer cs-check         # dry-run, shows the diff
composer cs-fix           # apply fixes
```

CI fails on style violations. Thus, run `composer cs-check` before you open a PR.

## Static analysis

`phpstan.neon` configures PHPStan with a baseline
(`phpstan-baseline.neon`):

```bash
composer phpstan
```

Do not add new errors. If a finding is a genuine false positive, it is better to
fix the code. Regenerate the baseline (`composer phpstan:baseline`) only when
the review agrees.

## Architecture orientation

This quick map shows where things are:

- **`lib/SP/`**: the core library, PSR-4 namespace `SP\` (flat layout:
  `Core`, `Config`, `DataModel`, `Repositories`, `Services`, `Mvc`, `Http`,
  `Html`, `Providers`, `Storage`, `Util`, `Plugin`).
- **`app/modules/web/`**: the web UI module, namespace `SP\Modules\Web\`
  (controllers, views, themes).
- **`app/modules/api/`**: the JSON-RPC API module, namespace `SP\Modules\Api\`.
- **`resources/`**: front-end sources (JS/SCSS). Vite compiles them into
  `public/dist/`. `vite.config.js` is at the repo root.
- **`tests/SP/`**: the test suite, namespace `SP\Tests\`. Its layout is the
  same as the library layout.
- Front controllers: `index.php` (web) and `api.php` (API). `router.php` is the
  router for the PHP built-in server.

## Branch & PR flow

1. Make a branch from `main`.
2. Make focused commits. Use
   [Conventional Commits](https://www.conventionalcommits.org/), for example
   `feat:`, `fix:`, `chore:`, `docs:`, `refactor:`, `test:`.
3. Before you push, run the same gates as CI:
   `composer cs-check`, `composer phpstan`, and the PHPUnit suite.
4. Open a PR against `main`. Give a clear description of what you changed and why.
   Link the related issues, if there are any. The issue/PR templates are in
   [.github/ISSUE_TEMPLATE](.github/ISSUE_TEMPLATE).
5. Keep each PR to one concern. Smaller PRs are easier to review and merge.

## Report security issues

**Do not** open public issues for vulnerabilities. Report them privately,
as [SECURITY.md](SECURITY.md) describes. Before disclosure, give time for a fix.
