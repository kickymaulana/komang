# Task List Komang — Aplikasi Pencarian Pelanggan PLN

Sumber acuan: `.agents/1-PRD.md` dan `.agents/2-TECH-SPEC.md`. Cakupan: semua V1 sekaligus.

## MODUL 1: Setup Project

**T-01: Setup Laravel 13 + TypeScript**
- **Deskripsi:** Buat project Laravel 13 dengan starter kit Inertia + Vue + TypeScript (`composer create-project laravel/laravel komang` atau `laravel new komang --vue --typescript`). Pastikan jalan di docroot `D:\Apache24\htdocs\komang`.
- **Modul:** Setup Project | **Prioritas:** High | **Status:** Done
- **Dependensi:** - | **Tanggal:** - | **Estimasi:** 1 hari
- **File:** seluruh root project

**T-02: Install dependensi server**
- **Deskripsi:** `composer require inertiajs/inertia-laravel tightenco/ziggy spatie/laravel-permission`
- **Modul:** Setup Project | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-01 | **Estimasi:** 0.5 hari
- **File:** `composer.json`

**T-03: Install dependensi client (TypeScript)**
- **Deskripsi:** `npm install @inertiajs/vue3 @inertiajs/vite vue @vitejs/plugin-vue @varlet/ui ziggy-js` + TypeScript (`typescript`, `@vue/tsconfig`)
- **Modul:** Setup Project | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-01 | **Estimasi:** 0.5 hari
- **File:** `package.json`

**T-04: Konfigurasi inti Inertia + Vite + Varlet + Ziggy**
- **Deskripsi:** `vite.config.ts` (plugin `laravel()` + `vue()` + `inertia()`, input `resources/js/app.ts`), `tsconfig.json`, `resources/js/app.ts` (`createInertiaApp()` + `withApp(app => app.use(Varlet); app.use(ZiggyVue))`), `resources/views/app.blade.php` (`<x-inertia::head />` + `<x-inertia::app />` + `@routes`), publish & daftarkan `HandleInertiaRequests` di `bootstrap/app.php`.
- **Modul:** Setup Project | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-02, T-03 | **Estimasi:** 1 hari
- **File:** `vite.config.ts`, `tsconfig.json`, `resources/js/app.ts`, `resources/views/app.blade.php`, `bootstrap/app.php`, `app/Http/Middleware/HandleInertiaRequests.php`

**T-05: Generate tipe Ziggy + declarations**
- **Deskripsi:** `php artisan ziggy:generate --types`; tambah `.d.ts` untuk `ziggy-js` (global `route`) dan `ComponentCustomProperties.route` di `resources/js/types/`.
- **Modul:** Setup Project | **Prioritas:** Mid | **Status:** Done
- **Dependensi:** T-04 | **Estimasi:** 0.5 hari
- **File:** `resources/js/ziggy.js`, `resources/js/types/*.d.ts`

## MODUL 2: Database & Model

**T-06: Verifikasi struktur tabel `master`**
- **Deskripsi:** Jalankan `SHOW CREATE TABLE master;` di DB `komang`; catat nama kolom, tipe, dan index aktual (asumsi: idpel, nama, alamat, trf, daya, kddk). Dokumentasikan hasil sebagai acuan migration.
- **Modul:** Database & Model | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-01 | **Estimasi:** 0.5 hari
- **File:** - (query DB, dokumentasi)

**T-07: Migration tabel `master` (guarded)**
- **Deskripsi:** Buat `create_master_table` dengan `Schema::hasTable('master')` → skip jika sudah ada; definisi kolom persis dari T-06 (termasuk index). Aplikasi tetap read-only terhadap data.
- **Modul:** Database & Model | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-06 | **Estimasi:** 0.5 hari
- **File:** `database/migrations/*_create_master_table.php`

**T-08: Model `Master` (read-only)**
- **Deskripsi:** `app/Models/Master.php` — `$table='master'`, `$primaryKey` sesuai aktual, `$incrementing`/`casts` benar, tanpa timestamps jika kolom tidak ada; baca-only (tidak ada create/update/delete method aktif).
- **Modul:** Database & Model | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-07 | **Estimasi:** 0.5 hari
- **File:** `app/Models/Master.php`

**T-09: Migrasi users + Spatie + jalankan migrate**
- **Deskripsi:** Pastikan migration `users`, `sessions`, `password_reset_tokens`, dan publish Spatie (`php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`) ada; jalankan `php artisan migrate` (harus sukses di DB `komang` tanpa bentrok dengan tabel `master`).
- **Modul:** Database & Model | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-07 | **Estimasi:** 0.5 hari
- **File:** `database/migrations/*`, `config/permission.php`

## MODUL 3: Autentikasi

**T-10: LoginController (manual, session)**
- **Deskripsi:** `Auth\LoginController` — `create()` render `Pages/Auth/Login.vue`, `store()` validasi email+password, `Auth::attempt()`, regenerate session, redirect `/dashboard`; gagal → flash error "Email atau password salah". Password bcrypt.
- **Modul:** Autentikasi | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-04, T-09 | **Estimasi:** 1 hari
- **File:** `app/Http/Controllers/Auth/LoginController.php`, `routes/web.php`

**T-11: LogoutController**
- **Deskripsi:** `Auth\LogoutController@destroy` — invalidate session + regenerate token → redirect `/login`.
- **Modul:** Autentikasi | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-10 | **Estimasi:** 0.5 hari
- **File:** `app/Http/Controllers/Auth/LogoutController.php`

**T-12: Halaman Login.vue (mobile-first, Varlet)**
- **Deskripsi:** `Pages/Auth/Login.vue` `<script setup lang="ts">`: `var-input` email + password, tombol besar, submit via `useForm` Inertia (`POST /login`), tampilkan error validasi. Guest-only (`middleware('guest')`), redirect jika sudah login. Layout mobile-first (Varlet).
- **Modul:** Autentikasi | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-10 | **Estimasi:** 1 hari
- **File:** `resources/js/Pages/Auth/Login.vue`

## MODUL 4: RBAC (Spatie Permission v8)

**T-13: RoleSeeder + user admin awal**
- **Deskripsi:** Seeder membuat role `admin` + `user`; buat user admin awal (name, email, password) + `assignRole('admin')`. Super-admin bypass via `Gate::before` (role admin = semua permission).
- **Modul:** RBAC | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-09 | **Estimasi:** 0.5 hari
- **File:** `database/seeders/RoleSeeder.php`, `database/seeders/DatabaseSeeder.php`

**T-14: Trait HasRoles + proteksi route admin**
- **Deskripsi:** `HasRoles` pada `app/Models/User.php`; daftarkan middleware Spatie (`role:...`) di `bootstrap/app.php`; route `/admin/*` diproteksi `role:admin`. Validasi akses: user role `user` tidak bisa akses halaman admin.
- **Modul:** RBAC | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-13, T-10 | **Estimasi:** 1 hari
- **File:** `app/Models/User.php`, `bootstrap/app.php`, `routes/web.php`

## MODUL 5: Pencarian Pelanggan

**T-15: DashboardController + Dashboard.vue**
- **Deskripsi:** `DashboardController@index` render `Pages/Dashboard.vue`; halaman berisi form pencarian langsung + link ke route `pelanggan.index`. Redirect `/` ke sini saat sudah login.
- **Modul:** Pencarian | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-04, T-12 | **Estimasi:** 0.5 hari
- **File:** `app/Http/Controllers/DashboardController.php`, `resources/js/Pages/Dashboard.vue`

**T-16: PelangganController@index (query aman + paginated)**
- **Deskripsi:** Terima `?kolom=&q=`; whitelist kolom (`idpel`, `nama`, `alamat`, `trf`, `daya`, `kddk`); query Eloquent case-insensitive (`LOWER(col) LIKE`), trim q (kosong → no query); paginate 25; render `Pages/Pelanggan/Index.vue` dengan props `results`, `kriteria`, `query`.
- **Modul:** Pencarian | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-08, T-10 | **Estimasi:** 1 hari
- **File:** `app/Http/Controllers/PelangganController.php`

**T-17: Pelanggan/Index.vue (mobile-first, Varlet)**
- **Deskripsi:** Form: dropdown kolom + input kata kunci (idpel → `inputmode="numeric"`), tombol cari besar; hasil: `var-table` di desktop, **kartu list di layar kecil**; `var-pagination`; tiap baris → link detail `route('pelanggan.show', {master})` via Ziggy.
- **Modul:** Pencarian | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-16, T-05 | **Estimasi:** 1.5 hari
- **File:** `resources/js/Pages/Pelanggan/Index.vue`

## MODUL 6: Detail Pelanggan

**T-18: PelangganController@show + Pelanggan/Detail.vue**
- **Deskripsi:** `show(Master $master)` (route-model binding tabel `master`) render `Pages/Pelanggan/Detail.vue` dengan prop `pelanggan`. Halaman: kartu info lengkap (idpel, nama, alamat, trf, daya, kddk) — layout kartu mobile-first Varlet; tombol besar **"Lihat List kddk"** → `route('kddk.show', { kode })`.
- **Modul:** Detail | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-16, T-17 | **Estimasi:** 1 hari
- **File:** `app/Http/Controllers/PelangganController.php`, `resources/js/Pages/Pelanggan/Detail.vue`

## MODUL 7: List Kddk + Anchor `#id` (fitur inti)

**T-19: KddkController@show (urut digit akhir)**
- **Deskripsi:** Terima kode kddk → ambil semua pelanggan dengan kode sama dari tabel `master` → **urut naik berdasarkan digit akhir kode** (suffix numerik, misal `BLBBBEA09200` → `09200`; urut `00100`, `00200`, ..., `09200`; konvensi dipastikan dari data aktual). Render `Pages/Kddk/Index.vue` dengan props `kode`, `list` (sorted), `targetId`.
- **Modul:** List Kddk | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-08, T-16 | **Estimasi:** 1 hari
- **File:** `app/Http/Controllers/KddkController.php`

**T-20: Kddk/Index.vue (kartu list mobile + highlight target)**
- **Deskripsi:** Daftar pelanggan dalam kddk sebagai kartu/baris (mobile-first); setiap baris punya `id` elemen = id pelanggan. `onMounted`: baca `window.location.hash` → `scrollIntoView()` + class highlight Varlet pada baris target. Tombol kembali ke detail/hasil.
- **Modul:** List Kddk | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-19, T-05 | **Estimasi:** 1.5 hari
- **File:** `resources/js/Pages/Kddk/Index.vue`

**T-21: Integrasi link dengan fragment `#id`**
- **Deskripsi:** Dari detail & hasil, tombol/link ke list kddk menyertakan fragment: `route('kddk.show', { kode }) + '#<idPelanggan>'`; pastikan target langsung tersorot. Uji: target ada → highlight; target tidak ada → halaman tetap tampil tanpa highlight.
- **Modul:** List Kddk | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-20, T-18 | **Estimasi:** 1 hari
- **File:** `resources/js/Pages/Pelanggan/*.vue`, `resources/js/Pages/Kddk/Index.vue`

## MODUL 8: Admin Kelola User

**T-22: Admin\UserController@index + store**
- **Deskripsi:** `Admin\UserController` — `index()` render `Pages/Admin/Users/Index.vue` dengan prop `users`, `roles`; `store()` validasi (name, email unik, password min. 8), buat user + `assignRole('user')`. Route group `/admin/*` middleware `role:admin`.
- **Modul:** Admin | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-14, T-09 | **Estimasi:** 1 hari
- **File:** `app/Http/Controllers/Admin/UserController.php`, `routes/web.php`

**T-23: Admin\UserController@destroy**
- **Deskripsi:** Nonaktifkan/hapus user (definisi final: soft-delete atau hard-delete sesuai keputusan); cegah menghapus diri sendiri / user admin terakhir; validasi role.
- **Modul:** Admin | **Prioritas:** Mid | **Status:** Done
- **Dependensi:** T-22 | **Estimasi:** 0.5 hari
- **File:** `app/Http/Controllers/Admin/UserController.php`

**T-24: Admin/Users/Index.vue (mobile-first)**
- **Deskripsi:** `var-table`/kartu daftar user + form tambah user (name, email, password, role) — `useForm` Inertia; error validasi ditampilkan; layout mobile-first Varlet.
- **Modul:** Admin | **Prioritas:** Mid | **Status:** Done
- **Dependensi:** T-22 | **Estimasi:** 1 hari
- **File:** `resources/js/Pages/Admin/Users/Index.vue`

## MODUL 9: QA & Finalisasi

**T-25: Seeder lengkap + import data master**
- **Deskripsi:** Jalankan `php artisan db:seed` (role + admin). Import data `master` dari dump existing ke DB `komang` (via `mysqldump`/phpMyAdmin) bila DB fresh; verifikasi data terbaca aplikasi (query `master`).
- **Modul:** QA | **Prioritas:** High | **Status:** Done
- **Dependensi:** T-13, T-07 | **Estimasi:** 0.5 hari
- **File:** `database/seeders/*`, dump SQL

**T-26: Tes end-to-end manual (perangkat HP + desktop)**
- **Deskripsi:** Verifikasi alur penuh: login (benar/salah password) → pencarian tiap kolom (idpel/nama/alamat/trf/daya/kddk) → detail → list kddk urut digit akhir → anchor `#id` highlight target → RBAC (user tidak bisa akses `/admin/*`, admin bisa tambah user) → logout. Uji di viewport mobile (HP) & desktop.
- **Modul:** QA | **Prioritas:** High | **Status:** Done
- **Dependensi:** semua modul fitur (T-12, T-17, T-18, T-20, T-21, T-24) | **Estimasi:** 2 hari
- **File:** - (manual test)

**T-27: Lint, build produksi & dokumentasi final**
- **Deskripsi:** Jalankan typecheck/`vue-tsc` + `npm run build` sukses; rapikan error; pastikan `php artisan route:list` sesuai spec; perbarui `AGENTS.md` jika ada penyimpangan dari spec (struktur kolom master, konvensi urut kddk, dsb.).
- **Modul:** QA | **Prioritas:** Mid | **Status:** Done
- **Dependensi:** T-26 | **Estimasi:** 1 hari
- **File:** `AGENTS.md`, hasil build

---

## Ringkasan
| Modul | Task |
|-------|------|
| 1. Setup Project | T-01 – T-05 |
| 2. Database & Model | T-06 – T-09 |
| 3. Autentikasi | T-10 – T-12 |
| 4. RBAC | T-13 – T-14 |
| 5. Pencarian | T-15 – T-17 |
| 6. Detail | T-18 |
| 7. List Kddk | T-19 – T-21 |
| 8. Admin | T-22 – T-24 |
| 9. QA & Finalisasi | T-25 – T-27 |

**Total: 27 task**
