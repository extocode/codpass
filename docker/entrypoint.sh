#!/bin/sh
# Give Apache the ownership of the runtime-writable dirs. They are bind-mounted from
# the host (or are named volumes). Thus, the script corrects the ownership at each start.
set -e

for dir in app/config app/cache app/temp app/backup; do
    mkdir -p "/var/www/html/$dir"
    chown -R www-data:www-data "/var/www/html/$dir"
done

# sysPass needs exactly mode 750 on the config dir (ConfigUtil::checkConfigDir).
chmod 750 /var/www/html/app/config

# Install PHP deps if the host did not install them (vendor/ is gitignored and is not
# in the build context). When vendor/autoload.php exists, the script skips this step.
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "vendor/ missing — running composer install..."
    composer install --no-interaction --prefer-dist --working-dir=/var/www/html
fi

exec "$@"
