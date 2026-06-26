# Run codPass with Docker (local development)

A minimal dev stack: PHP 8.4 + Apache for the app, MariaDB 10.6 for the database.

## Start

```sh
docker compose up --build
```

Then open <http://localhost:8080> and run through the codPass installer.

## Database settings for the installer

| Field    | Value         |
|----------|---------------|
| Host     | `db`          |
| Name     | `syspass`     |
| User     | `syspass`     |
| Password | `syspass`     |

(The MariaDB root password is `root`. Port `3306` is also exposed on the host.)

## How it is wired

- **Docroot is the repo root** — codPass front controllers are `index.php`
  (web) and `api.php` (API). Assets live under `public/` and are served as
  real files; everything else falls back to `index.php`
  (`docker/apache-vhost.conf`), mirroring the repo's `router.php`.
- **Writable dirs** (`app/config`, `app/cache`, `app/temp`, `app/backup`) are
  named volumes owned by `www-data`; `docker/entrypoint.sh` fixes ownership on
  every start.
- **`vendor/`** is a named volume. If it is empty the entrypoint runs
  `composer install` automatically on first boot.
- The project source is **bind-mounted**, so edits on the host are live.

## PHP extensions

The image installs: `pdo_mysql`, `gd`, `gettext`, `zlib`, `ldap`, plus the
base-image bundled `dom`, `json`, `libxml`, `mbstring`, `fileinfo`.

## Reset

```sh
docker compose down -v   # also drops the DB and named volumes
```
