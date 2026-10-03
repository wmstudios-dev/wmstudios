# WMSPACE

Website for a creative agency (social media, documentation, design, photo & video production, web design & development).
Laravel 12 + Tailwind v4, Indonesian / English, with an admin panel at `/admin`.

## Run locally

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed      # admin@wmspace.test / password + sample content (local only)
php artisan storage:link
npm run build                   # public/build is committed, rebuild after CSS/JS/Blade class changes
php artisan serve
```

Enable PHP's `gd` extension to get automatic photo resizing / WebP conversion on upload
(without it the original files are stored as they are).

## What is where

- `app/Support/AdminResources.php` describes every content type the admin manages (works, services, packages, FAQ, process, space, thoughts, clients, testimonials). Add or change a field there and the generic admin form/list follow.
- `app/Support/SettingsSchema.php` lists the editable site settings (logo, hero text, contact details, socials).
- `app/Support/ImageUploader.php` resizes uploads to WebP (max 1920px) and makes a 640px thumbnail.
- `app/Support/VideoEmbed.php` turns YouTube / Vimeo / Instagram / .mp4 links into players.
- Content is bilingual in the database (`title_id` / `title_en`); UI text lives in `lang/id|en/site.php`.
- Brand colour `#2E59BF` is defined in `resources/css/app.css` (`--color-brand-*`).

## Deploy (Railway)

Set `APP_KEY`, `APP_URL`, `DB_CONNECTION=mysql`, `DB_URL`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and (for emails) `MAIL_MAILER=gmail` + `GMAIL_*`.
Pre-deploy command: `php artisan migrate --force && php artisan db:seed --force`.
Mount a volume at `/app/storage/app/public/uploads` so uploaded photos survive redeploys.
