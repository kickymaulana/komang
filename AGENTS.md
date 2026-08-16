# AGENTS.md

## Project
Komang — aplikasi pencarian pelanggan PLN berbasis Laravel. Sumber spesifikasi: `.agents/1-PRD.md`, `.agents/2-TECH-SPEC.md`, `.agents/3-TASKS.md`. Sudah terimplementasi (V1) di repo ini.

## Environment
- Root project: `D:\Apache24\htdocs\komang` (docroot Apache → akses `http://localhost/komang/public`).
- ⚠️ **Deploy di subdirektori** → `.env` WAJIB punya `ASSET_URL=/komang/public` (base Vite saat build = `ASSET_URL./build/`). Tanpa ini, lazy chunk JS/CSS memakai `/build/...` root-absolute → 404. Setelah ubah `ASSET_URL`/`.env`, jalankan `npm run build` lagi.
- Jika diuji via `php artisan serve` (root URL), nonaktifkan sementara `ASSET_URL` di `.env` lalu rebuild, atau pakai vhost ke folder `public`.
- Stack: Laravel 13.25 + Inertia.js v3 + Vue 3 + **TypeScript** + Varlet UI + Ziggy + Spatie Laravel Permission v8, MySQL/MariaDB 11.8.
- DB: `komang` (user `root`, password `kicky123` — ada di `.env`). Tabel `master` **read-only** (32.001 baris).
- Login Laravel wajib untuk semua halaman data (auth session manual, tanpa registrasi publik).
- Admin awal: `admin@komang.test` / `password` (dari `db:seed`).

## Architecture
- Monolith Inertia: controller → `Inertia::render('Page', props)` → Vue page di `resources/js/pages/**/*.vue` (`<script setup lang="ts">`). JANGAN ubah ke huruf kapital `Pages` — `app.blade.php` mereferensikan `resources/js/pages/{$page['component']}.vue` (huruf kecil).
- Root template `resources/views/app.blade.php`: `<x-inertia::head />` + `<x-inertia::app />` + `@routes`.
- Vite: `laravel()` + `vue()` + `inertia()`; entry `resources/js/app.ts` (`createInertiaApp()` + `app.use(Varlet)` + `app.use(ZiggyVue)`); `resources/css/app.css` = body style saja (style Varlet di-import dari app.ts).
- Route frontend via Ziggy `route()` (types: `php artisan ziggy:generate --types`; hasil `resources/js/ziggy.{js,d.ts}` DI-IGNORE eslint, jangan diedit manual).
- UI = **Varlet** (`var-button`, `var-cell`, `var-card`, dll. ter-register global). Inertia v3 pakai XHR client bawaan (tanpa Axios).
- RBAC Spatie: 2 role `admin` (super-admin via `Gate::before` di `AppServiceProvider`) & `user`. Group `/admin/*` → `middleware('role:admin')`.
- Auth manual: `app/Http/Controllers/Auth/LoginController.php` (`Auth::attempt` + session), route `login` throttle `5,1`. Fortify TIDAK dipakai.

## Database & Migrations
- Tabel `master` (VERIFIED dari `SHOW CREATE TABLE`): `id` int auto-inc PK, `idpel` varchar(12), `nama` varchar(120), `alamat` varchar(120), `trf` varchar(60), `daya` varchar(10), `kddk` varchar(12). Engine InnoDB, latin1. **Tidak ada timestamps.**
- Migration `create_master_table` **guarded** (`Schema::hasTable('master')` → skip) — hanya membuat struktur saat fresh setup, tidak menimpa tabel manual. JANGAN hapus guard ini.
- Migrations lain: users, sessions, cache, jobs, password_reset_tokens, tabel Spatie (`create_permission_tables`).
- Workflow fresh setup: `php artisan migrate` → import dump master (`mysqldump -u root -p komang master > master_dump.sql`) → `php artisan db:seed`.

## Domain
- Pencarian pelanggan (`PelangganController@index`): kolom di-whitelist `['idpel','nama','alamat','trf','daya','kddk']`, case-insensitive (`LOWER(col) LIKE ?` via `whereRaw`), paginate 25, query `?kolom=&q=&page=`.
- Format kddk = **prefix 7 huruf + suffix 5 digit** (contoh `BLBBBEA` + `09200`).
- List kddk (`KddkController@show`): ambil semua kddk ber-prefix sama, urut naik `ORDER BY CAST(SUBSTRING(kddk, -5) AS UNSIGNED)` — verifikasi memenuhi kasus suffix non-bulat (`02400 → 02401 → 02500`).
- URL list kddk memakai fragment `#<id>` (dari Detail → `route('kddk.show',{kode}) + '#'.id`); `Kddk/Index.vue` scroll + highlight target via `window.location.hash`.

## Commands
- Dev: `npm run dev` (Vite) + `php artisan serve` atau via Apache `http://localhost/komang/public`.
- Build prod: `npm run build`.
- Check: `npm run lint:check` (eslint), `npm run types:check` (vue-tsc --noEmit), `composer lint` (pint).
- Routes: `php artisan route:list`; Ziggy types: `php artisan ziggy:generate --types`.
- ⚠️ **`composer lint` (pint) TIDAK bisa jalan di environment ini** — Box-phar pint gagal menulis temp ke `C:\WINDOWS\TEMP` yang tidak writable untuk user non-admin (PHPUnit yang juga phar jalan normal). Fix: jalankan dari shell yang di-elevasi/ubah ACL `C:\WINDOWS\TEMP`. Ganti sementara: `php -l` per file (sintaks) + gaya PSR-12 diikuti manual.

## Catatan
- UI **mobile-first** (pengguna utama pakai HP): halaman hasil/kddk = kartu di layar <768px, tabel di ≥768px; input idpel `inputmode="numeric"`.
- V1 selesai: auth + RBAC 2 role + pencarian + detail + list kddk. CRUD master, export Excel, role lanjutan, dan test PHPUnit-feature masuk v2.
- `npm run lint` memakai `--fix`; file generated (`ziggy.*`) di-ignore di `eslint.config.js`.

## Fitur Batas Sewa (Expiry)
- Konfigurasi di `.env`: `APP_EXPIRY_ENABLED=true`, `APP_EXPIRED_AT=YYYY-MM-DD`, `APP_EXPIRY_MESSAGE="..."` (nilai ber-spasi WAJIB dikutip).
- Middleware `App\Http\Middleware\CheckAppExpiry` (prepend di group `web`, `bootstrap/app.php`): jika `now() >= APP_EXPIRED_AT` → SEMUA halaman web diganti `resources/views/expired.blade.php` (HTTP 403, standalone tanpa asset Vite).
- Cara pakai: saat klien bayar bulanan, perbarui `APP_EXPIRED_AT` ke akhir bulan berikutnya + `config:clear` (bila config di-cache, restart PHP-FPM).
