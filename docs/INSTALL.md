<!--
  codPass — a community fork of sysPass

  Based on sysPass by Rubén Domínguez (nuxsmin) — https://syspass.org
  Original work copyright 2012-2019, Rubén Domínguez.

  This file is part of codPass / sysPass.

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->

# Installation

codPass is a PHP web application. You install it by deploying the code, running
the dependency/asset build steps, then completing the web-based installer in a
browser, which writes the configuration and sets up the database.

## Requirements

- **PHP 8.4+** with extensions: `pdo`, `dom`, `gd`, `json`, `gettext`,
  `fileinfo`, `zlib`, `libxml`, `mbstring`
- **MariaDB / MySQL** database server
- **Composer** (PHP dependency manager)
- **Node.js** — only needed to build the front-end assets

## Manual installation

1. Get the code into your web root (clone the repository or extract a release).

2. Install PHP dependencies:
   ```bash
   composer install --no-dev
   ```

3. Build the front-end assets:
   ```bash
   npm install
   npm run build
   ```

4. Make sure the web server can write to the application's runtime
   directories under `app/` (e.g. `app/config`, `app/cache`, `app/backup`,
   `app/temp`). The web installer creates `app/config/config.xml` here.

5. Point your web server's document root at the **project root** — the
   `index.php` and `api.php` front controllers live there, and assets are
   served from `public/`. Deny web access to the `app/` directory and serve
   the app over HTTPS. (See `docker/apache-vhost.conf` for a working example.)

6. Open the app in a browser and follow the **web installer**. It asks for the
   database connection and admin/master-password details, creates the database
   schema, and writes `app/config/config.xml`.

After the installer finishes, see [CONFIGURATION.md](CONFIGURATION.md) for the
configurable areas (authentication, mail, backup, encryption).

## Docker

sysPass historically shipped Docker images and a `docker-compose` setup. To run
codPass in a container, the repository root ships a `Dockerfile` and
`docker-compose.yml` (PHP 8.4 + Apache + MariaDB) — run `docker compose up
--build` and open http://localhost:8080. See `docker/README.md` for details.

## Further help

Historical upstream documentation and FAQ lived at `https://doc.syspass.org`.
That site may now be unavailable; the docs in this folder are the maintained
reference for codPass.
