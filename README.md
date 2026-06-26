# codPass — COD Password Manager

> **Unofficial community fork of [sysPass](https://github.com/nuxsmin/sysPass)** by Rubén Domínguez (nuxsmin).
> Upstream has had no release since 3.2.11 (July 2022). codPass continues maintenance and
> modernization under the same GNU GPLv3 license. Not affiliated with or endorsed by the
> original author.

![Release](https://img.shields.io/badge/release-v4.0.0-success)
![License](https://img.shields.io/badge/license-GPLv3-blue)
![PHP](https://img.shields.io/badge/PHP-%E2%89%A58.4-777bb4)

PHP web-based password manager for business and personal use.

## Features
- AES-256 encryption (CTR mode)
- RSA for transmitting passwords from forms
- Two-factor authentication
- HTML5 + Ajax interface
- Users, groups and profiles with up to 29 access levels
- MySQL, OpenLDAP and Active Directory authentication
- Tags, custom fields, public links, private accounts, favorites, history
- Email + in-app notifications and event log
- Multilanguage
- JSON-RPC API

## What's different from sysPass
- **Modernized stack:** PHP 8.4+, Symfony 8, PHP-DI 7, phpseclib 3, Monolog 3, PHPUnit 13
- **Security:** all `composer audit` advisories cleared; fixed a config-cache flock deadlock that could hang requests
- **Frontend:** Vite asset build (`resources/` → `public/dist/`); new `inducio` theme
- **API:** global search (`gsearch`) and offset pagination on account search
- **Dev experience:** Docker dev stack, `.env` support, PSR-12 (phpcs) + PHPStan + `composer test`, GitHub Actions CI, `docs/` + `CONTRIBUTING.md`
- **Cleanup:** removed unused dependencies and committed build artifacts

## Requirements
- PHP **8.4+** with extensions: `pdo`, `dom`, `gd`, `json`, `gettext`, `fileinfo`, `zlib`, `libxml`, `mbstring`
- MariaDB / MySQL
- Composer
- Node.js (for building front-end assets)

## Installation
```bash
composer install --no-dev
npm install
npm run build
```
Then open the app in a browser and follow the web installer. See [docs/INSTALL.md](docs/INSTALL.md) for details (and [docker/README.md](docker/README.md) to run it in Docker).

## Documentation
Full documentation lives in [docs/](docs/README.md).

## Security
Found a vulnerability? See [SECURITY.md](SECURITY.md) — please report privately before disclosure.

## Contributing
Issues and PRs welcome. Templates live in [.github/ISSUE_TEMPLATE](.github/ISSUE_TEMPLATE).

## Credits
Built on **sysPass** by Rubén Domínguez (nuxsmin) — https://github.com/nuxsmin/sysPass.
All original copyright is retained in source headers.

## License
GNU GPLv3. Original work © 2012–2019 Rubén Domínguez; fork modifications © 2024–2026 contributors.
Full text in [COPYING](COPYING).
