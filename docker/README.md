# Run codPass with Docker (local development)

A minimal dev stack: PHP 8.4 + Apache for the app, MariaDB 10.6 for the database.

## Start

```sh
docker compose up --build
```

Then open <http://localhost:8080>. Complete the steps of the codPass installer.

## Database settings for the installer

| Field    | Value         |
|----------|---------------|
| Host     | `db`          |
| Name     | `syspass`     |
| User     | `syspass`     |
| Password | `syspass`     |

(The MariaDB root password is `root`. Port `3306` is also exposed on the host.)

## How it is wired

- **Docroot is the repo root**: the codPass front controllers are `index.php`
  (web) and `api.php` (API). The assets are under `public/`. Apache serves them as
  real files. All other requests go to `index.php`
  (`docker/apache-vhost.conf`). This is the same as `router.php` in the repo.
- **Writable dirs** (`app/config`, `app/cache`, `app/temp`, `app/backup`) are
  named volumes that `www-data` owns. `docker/entrypoint.sh` corrects the
  ownership at each start.
- **`vendor/`** is a named volume. If it is empty, the entrypoint runs
  `composer install` automatically at the first boot.
- The project source is **bind-mounted**. Thus, edits on the host are live.

## PHP extensions

The image installs: `pdo_mysql`, `gd`, `gettext`, `zlib`, `ldap`. The base image
also includes `dom`, `json`, `libxml`, `mbstring`, `fileinfo`.

## Reset

```sh
docker compose down -v   # also drops the DB and named volumes
```
