#!/bin/bash
# Runs on the server each time you click "Deploy HEAD Commit" in cPanel > Git Version Control.
# Output goes to ~/afroiso-deploy.log (open it in File Manager to see what happened).

cd "$(dirname "$0")" || exit 1
REPO="$(pwd)"
WEB="$HOME/public_html"

echo ""
echo "===== Deploy started $(date) in $REPO ====="

# 1. Find a PHP command line that is version 8.3 or newer.
PHP=""
for candidate in \
    /opt/cpanel/ea-php85/root/usr/bin/php /opt/cpanel/ea-php84/root/usr/bin/php /opt/cpanel/ea-php83/root/usr/bin/php \
    /opt/alt/php85/usr/bin/php /opt/alt/php84/usr/bin/php /opt/alt/php83/usr/bin/php \
    /usr/local/bin/php /usr/bin/php php; do
    if command -v "$candidate" >/dev/null 2>&1 && "$candidate" -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);' 2>/dev/null; then
        PHP="$candidate"
        break
    fi
done
if [ -z "$PHP" ]; then
    echo "STOPPED: no PHP 8.3 or newer was found on this server. Ask the host to make PHP 8.3+ available."
    exit 1
fi
echo "Using PHP: $PHP ($("$PHP" -r 'echo PHP_VERSION;'))"

# 2. Find Composer, or download it into the home folder.
COMPOSER=""
for candidate in /opt/cpanel/composer/bin/composer "$(command -v composer 2>/dev/null)" "$HOME/composer.phar"; do
    if [ -n "$candidate" ] && [ -f "$candidate" ]; then
        COMPOSER="$candidate"
        break
    fi
done
if [ -z "$COMPOSER" ]; then
    echo "Composer not found, downloading it to $HOME/composer.phar"
    "$PHP" -r "copy('https://getcomposer.org/download/latest-stable/composer.phar', getenv('HOME') . '/composer.phar');"
    COMPOSER="$HOME/composer.phar"
fi
if [ ! -s "$COMPOSER" ]; then
    echo "STOPPED: Composer could not be found or downloaded."
    exit 1
fi

# 3. Install Laravel's packages.
"$PHP" "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction || { echo "STOPPED: composer install failed."; exit 1; }

# 4. First deploy only: create .env and stop so the database details can be filled in.
if [ ! -f .env ]; then
    cp .env.example .env
    "$PHP" artisan key:generate --force
    echo "STOPPED ON PURPOSE: .env was created. Edit $REPO/.env (database, admin login, APP_URL), then click Deploy again."
    exit 0
fi

# 5. Database tables, and the starting content the first time only.
"$PHP" artisan migrate --force || { echo "STOPPED: migrate failed. Check the database details in .env."; exit 1; }
if [ ! -f storage/app/.seeded ]; then
    "$PHP" artisan db:seed --force && touch storage/app/.seeded
fi

# 6. Publish the public files into public_html and point its index.php at this project.
mkdir -p "$WEB/uploads"
cp -R public/. "$WEB/"
cat > "$WEB/index.php" <<PHPFILE
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

\$base = '$REPO';

if (file_exists(\$maintenance = \$base . '/storage/framework/maintenance.php')) {
    require \$maintenance;
}

require \$base . '/vendor/autoload.php';

\$app = require_once \$base . '/bootstrap/app.php';
\$app->usePublicPath(__DIR__);
\$app->handleRequest(Request::capture());
PHPFILE

for leftover in index.html index.htm default.html; do
    if [ -f "$WEB/$leftover" ]; then
        echo "NOTE: $WEB/$leftover exists and may show instead of the site. Rename or remove it."
    fi
done

# 7. Refresh Laravel's caches.
"$PHP" artisan config:cache
"$PHP" artisan view:clear
chmod -R u+rwX storage bootstrap/cache

echo "===== Deploy finished $(date) ====="
