# SIMSET SBH

Aplikasi manajemen aset berbasis Laravel.

## Kebutuhan

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- Database SQLite/MySQL sesuai konfigurasi `.env`

## Instalasi Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Jika memakai SQLite, buat file database lokal terlebih dahulu:

```bash
type nul > database/database.sqlite
```

Lalu set konfigurasi berikut di `.env`:

```env
DB_CONNECTION=sqlite
```

## Development

```bash
composer run dev
```

Perintah ini menjalankan server Laravel, queue listener, log watcher, dan Vite secara bersamaan.

## Testing

```bash
composer test
```

## Catatan Upload GitHub

File yang tidak boleh di-commit sudah diabaikan lewat `.gitignore`, termasuk `.env`, `vendor/`, `node_modules/`, cache, log, dan database SQLite lokal. Commit file source code, migration, konfigurasi, `composer.lock`, dan `package-lock.json` agar instalasi di mesin lain tetap konsisten.
