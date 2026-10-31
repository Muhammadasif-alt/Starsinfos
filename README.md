# InfoHub PK

Multi-niche information website built with Laravel 12, Filament v3 and Blade. The site name comes from `APP_NAME` in `.env`.

> The brief asked for Laravel 11, but Composer refuses every 11.x release because of open security advisories. Laravel 12 has the same structure and passes `composer audit`.

## Local setup

```bash
composer install
cp .env.example .env        # set APP_ENV=local, APP_DEBUG=true and your database
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan make:filament-user
php artisan serve
```

- Site: http://127.0.0.1:8000
- Admin: http://127.0.0.1:8000/admin
- Tests: `php artisan test`
- CSS or JS changes: `npm install && npm run build`, then commit `public/build`.

## Deploying to Hostinger

1. Push the project to GitHub.
2. In hPanel, open **Git**, connect the repository and deploy to the site's folder.
3. Point the document root to `/public`. If that is not possible, the root `.htaccess` forwards requests there.
4. Create a MySQL database in hPanel, copy `.env.example` to `.env` and fill in `APP_URL`, the `DB_*` and the `MAIL_*` values.
5. Over SSH, in the site's folder:

   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --seed --force
   php artisan storage:link
   php artisan filament:assets
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. Create the first admin:

   ```bash
   php artisan make:filament-user
   ```

No Node is needed on the server because `public/build` is committed.

## Before going live

Seeded posts are sample content and say so in their text. Replace or delete them, and check every date and source link.

## Auto-deploy (optional)

`.github/workflows/deploy.yml` deploys over SSH on every push to `main`. Add the secrets listed at the top of that file to use it.
