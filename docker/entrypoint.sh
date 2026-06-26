#!/bin/sh
# Make the runtime-writable dirs owned by Apache. They are bind-mounted from the
# host (or named volumes), so ownership is fixed up on every start.
set -e

for dir in app/config app/cache app/temp app/backup; do
    mkdir -p "/var/www/html/$dir"
    chown -R www-data:www-data "/var/www/html/$dir"
done

# sysPass requires the config dir to be exactly mode 750 (ConfigUtil::checkConfigDir).
chmod 750 /var/www/html/app/config

# Install PHP deps if the host hasn't (vendor/ is gitignored and excluded from
# the build context). Skips automatically once vendor/autoload.php exists.
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "vendor/ missing — running composer install..."
    composer install --no-interaction --prefer-dist --working-dir=/var/www/html
fi

exec "$@"
