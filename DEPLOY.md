# AfroISO website: setup and cPanel deployment

Laravel 13, PHP 8.3 or newer, MySQL. No Node build step: the styles are plain CSS in `public/css`.

## 1. Run it on your computer first

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

- `ADMIN_EMAIL` and `ADMIN_PASSWORD`: the first admin login. No admin account is created if the password is empty.
- Database: the default is SQLite. For MySQL set `DB_CONNECTION=mysql` and fill in `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

```bash
touch database/database.sqlite   # SQLite only
php artisan migrate --seed
php artisan test
php artisan serve
```

The site is at http://localhost:8000 and the admin panel at http://localhost:8000/admin.

## 2a. Deploy with cPanel Git Version Control (no SSH needed)

1. cPanel > Git Version Control > Create. Clone URL: the GitHub repository. Repository path: `afroiso-site` (in the home folder, not inside `public_html`).
2. Open Manage > Pull or Deploy and click "Deploy HEAD Commit". The first deploy installs the packages, creates `.env` and then stops on purpose.
3. In File Manager edit `afroiso-site/.env`: set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, the MySQL details, `ADMIN_EMAIL` and `ADMIN_PASSWORD`.
4. Click "Deploy HEAD Commit" again. This creates the tables and starting content and publishes the site into `public_html`.
5. For later updates: "Update from Remote", then "Deploy HEAD Commit".

Each deploy writes what it did to `afroiso-deploy.log` in the home folder. The steps are in `deploy.sh`.

## 2b. Deploy to cPanel by hand

1. In cPanel create a MySQL database and user, and give the user all privileges on it.
2. On your computer run `composer install --no-dev --optimize-autoloader`.
3. Upload the whole project, including `vendor`, to a folder outside `public_html`, for example `/home/USER/afroiso`.
4. Point the domain's document root at `/home/USER/afroiso/public` (cPanel > Domains). If the host will not let you change it, upload the project into `public_html` instead; the `.htaccess` in the project root then sends every request to `public/`.
5. Create `.env` on the server from `.env.example` and set:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`
   - `APP_KEY`: copy the value from your local `.env`, or run `php artisan key:generate` in the cPanel Terminal
   - the MySQL details from step 1
   - `ADMIN_EMAIL`, `ADMIN_PASSWORD`
   - mail settings (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`) so enquiries are emailed to `OFFICE_EMAIL`
6. In the cPanel Terminal, from the project folder: `php artisan migrate --seed --force`, then `php artisan config:cache`.
   No Terminal on the hosting plan? Run the migration against a local MySQL database, export it, and import it with phpMyAdmin.
7. Make sure `storage`, `bootstrap/cache` and `public/uploads` are writable by the web server (755 is normally enough on cPanel).
8. Turn on SSL for the domain (cPanel > SSL/TLS Status).

## 3. Content you can change without code

- Courses, fees, dates, publications (with cover images), certificates and enquiries: the admin panel at `/admin`.
- Hero photo: save a wide landscape image as `public/images/hero.jpg`. It appears behind the homepage heading with a navy overlay.
- Social media icons: add the links in `.env` (`SOCIAL_FACEBOOK`, `SOCIAL_LINKEDIN`, ...). Only networks with a link are shown. Run `php artisan config:cache` afterwards.
- Address, phone and email: `config/afroiso.php`.
- Policy pages: `resources/views/pages/policy.blade.php`.

## 4. Not built yet

- E-learning portal (learner logins, lessons, exams, certificates): planned as the next phase in this same project.
- Online payment: enrol and order buttons currently open the enquiry form, and the office invoices the customer.
