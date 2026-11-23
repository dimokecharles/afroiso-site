#!/bin/bash
# Runs on the server each time you click "Deploy HEAD Commit" in cPanel > Git Version Control.
# Output goes to ~/afroiso-deploy.log (open it in File Manager to see what happened).

cd "$(dirname "$0")" || exit 1
REPO="$(pwd)"

# Reads one setting from .env (empty when the file or the line is missing).
env_value() {
    [ -f .env ] || return 0
    grep -E "^$1=" .env | tail -n 1 | cut -d= -f2- | tr -d '"\r' | sed "s/^'//; s/'\$//"
}

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

# 6. Publish the public files into the web folder and point its index.php at this project.
#    The live site uses public_html. A UAT copy sets DEPLOY_PUBLIC_DIR in its own .env.
WEB_DIR="$(env_value DEPLOY_PUBLIC_DIR)"
case "$WEB_DIR" in
    "") WEB="$HOME/public_html" ;;
    /*) WEB="$WEB_DIR" ;;
    *) WEB="$HOME/$WEB_DIR" ;;
esac
echo "Publishing to: $WEB"
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

# A UAT copy must never be indexed by search engines.
if [ "$(env_value UAT_MODE)" = "true" ]; then
    printf 'User-agent: *\nDisallow: /\n' > "$WEB/robots.txt"
    echo "UAT mode: robots.txt blocks all search engines."
fi

for leftover in index.html index.htm default.html; do
    if [ -f "$WEB/$leftover" ]; then
        echo "NOTE: $WEB/$leftover exists and may show instead of the site. Rename or remove it."
    fi
done

# 7. Refresh Laravel's caches.
"$PHP" artisan config:cache
"$PHP" artisan view:clear

# 8. Admin login. When ADMIN_PASSWORD in .env is different from the last deploy, apply it to the admin account.
#    To change the admin password: edit ADMIN_PASSWORD in .env, then deploy.
ADMIN_MARK="storage/app/.admin-password"
ADMIN_PASSWORD_VALUE="$(env_value ADMIN_PASSWORD)"
if [ -n "$ADMIN_PASSWORD_VALUE" ]; then
    ADMIN_SUM="$(printf '%s' "$ADMIN_PASSWORD_VALUE" | sha256sum | cut -d' ' -f1)"
    if [ "$(cat "$ADMIN_MARK" 2>/dev/null)" != "$ADMIN_SUM" ]; then
        "$PHP" artisan afroiso:sync-admin && echo "$ADMIN_SUM" > "$ADMIN_MARK"
    fi
fi
unset ADMIN_PASSWORD_VALUE
chmod -R u+rwX storage bootstrap/cache

echo "===== Deploy finished $(date) ====="
