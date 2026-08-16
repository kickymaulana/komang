# Tech Spec Komang — Aplikasi Pencarian Pelanggan PLN

## BAGIAN 1: Tech Stack & Arsitektur

### Tech Stack
| Layer | Technology | Version |
|-------|------------|---------|
| Frontend | Vue 3 + Varlet UI (halaman = Vue pages) | Vue 3.x / Varlet 3.x |
| Language | TypeScript | 5.x |
| SPA glue | Inertia.js v3 | 3.x (rilis 26/03/2026) |
| Routes di JS | Ziggy (`ziggy-js` + `@routes` Blade) | 2.x |
| Backend | Laravel | 13 (latest) |
| Database | MySQL | 8.x |
| ORM | Eloquent | bawaan Laravel |
| Auth | Session manual (bcrypt) | - |
| RBAC | Spatie Laravel Permission | v8 |
| Build | Vite 7/8 + `laravel-vite-plugin` + `@vitejs/plugin-vue` | - |
| Hosting | Apache 2.4 lokal (docroot `D:\Apache24\htdocs\komang`) | - |

### Arsitektur Sistem
Modern monolith Inertia — controller merender Vue page (TypeScript) tanpa API terpisah:

```
User → Route (web.php) → Controller → Inertia::render('Page', props)
                                       ↓
             Vue 3 page (resources/js/Pages/*.vue, <script setup lang="ts">)
                                       ↓
                       Ziggy (route()) → URL; Eloquent → MySQL

RBAC: spatie/laravel-permission → role/permission di gate + middleware
```

### Setup Inti (verifikasi dari dokumen resmi)
**Inertia v3:**
- `composer require inertiajs/inertia-laravel`
- Root template `resources/views/app.blade.php`: `<x-inertia::head />` + `<x-inertia::app />`
- `php artisan inertia:middleware` → `HandleInertiaRequests` di group `web` (`bootstrap/app.php`)
- Client: `npm install @inertiajs/vue3 @inertiajs/vite vue @vitejs/plugin-vue @varlet/ui`
- Vite: plugins `laravel()` + `vue()` + `inertia()`; input `resources/js/app.ts`
- Entry `app.ts`: `createInertiaApp()` + `withApp(app => app.use(Varlet))` — plugin Vite auto-resolve halaman

**Ziggy 2.x (untuk route() di TS):**
- `composer require tightenco/ziggy`
- `@routes` di `app.blade.php` sebelum JS
- Vue plugin via Inertia: `app.use(ZiggyVue)`
- Generate tipe TS: `php artisan ziggy:generate --types`
- Declaration `ziggy-js` + `ComponentCustomProperties.route` di `.d.ts`

**Spatie Laravel Permission v8:**
- `composer require spatie/laravel-permission`
- Publish migration (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`)
- Trait `HasRoles` pada `User`, daftarkan middleware role/permission di `bootstrap/app.php`
- Seeder: role `admin` (super-admin, bypass check) + `user`

### Struktur Folder
```
komang/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/               # LoginController (manual), LogoutController
│   │   │   ├── DashboardController
│   │   │   ├── PelangganController # pencarian multi-kriteria
│   │   │   ├── KddkController      # list kddk + anchor #id
│   │   │   └── Admin/UserController # kelola user (role:admin)
│   │   └── Middleware/HandleInertiaRequests.php
│   ├── Models/
│   │   ├── User.php                # + HasRoles trait
│   │   └── Master.php              # read-only → tabel `master`
│   └── Providers/
├── bootstrap/app.php               # middleware + auth + spatie
├── config/ziggy.php                # filter route yang di-expose (opsional)
├── database/
│   ├── migrations/                 # users, sessions, spatie, master (guarded)
│   └── seeders/                    # RoleSeeder + user admin awal
├── resources/
│   ├── views/app.blade.php         # root template Inertia + @routes
│   └── js/
│       ├── app.ts                  # createInertiaApp() + Varlet + ZiggyVue
│       ├── types/                  # ziggy.d.ts, env.d.ts
│       ├── css/
│       └── Pages/                  # Login, Dashboard, Pelanggan, Kddk, Admin (.vue)
├── routes/web.php
├── tsconfig.json
└── vite.config.ts
```

### Justifikasi
- **Inertia v3:** SPA feel tanpa API terpisah — controller + Vue page tetap memakai pola Laravel klasik.
- **Varlet UI:** komponen Vue **mobile-first** — target utama pengguna pakai HP (petugas lapangan), tetap nyaman di desktop.
- **TypeScript:** type-safe di semua Vue page + prop Inertia + tipe route Ziggy.
- **Ziggy:** nama route Laravel dipakai langsung di frontend — tidak ada URL hardcode.
- **Spatie Permission v8:** RBAC terkelola di DB, integrasi langsung dengan gate Laravel & Inertia middleware.
- **MySQL:** database `komang` + tabel `master` sudah ada.
- **Apache lokal:** project langsung di docroot `htdocs\komang`.

## BAGIAN 2: Database Design

### Ringkasan Database
| Item | Detail |
|------|--------|
| Database | MySQL `komang` |
| ORM | Eloquent |
| Pendekatan | Relational |
| Tools Migrasi | Laravel migration (kecuali `master` — guarded hasTable) |

### Entity Overview
| Entity | Key Fields | Relasi |
|--------|-----------|--------|
| master (EXISTING, read-only) | id, idpel, nama, alamat, trf, daya, kddk + kolom lain* | tidak ada relasi |
| users | id, name, email, password | → roles (N:M via spatie) |
| roles (spatie) | id, name, guard_name | ← users, → permissions (N:M) |
| permissions (spatie) | id, name, guard_name | ← roles |
| model_has_roles (spatie) | role_id, model_type, model_id | pivot users↔roles |
| model_has_permissions (spatie) | permission_id, model_type, model_id | pivot users↔permissions |
| role_has_permissions (spatie) | role_id, permission_id | pivot roles↔permissions |
| sessions | id, user_id, ip_address, ... | Laravel default |

> *Struktur kolom tabel `master` belum diverifikasi — konfirmasi nama kolom aktual saat implementasi (query pertama `SHOW CREATE TABLE master`).

### Index Strategy
- `master.idpel` — index pencarian idpel
- `master.nama` — index pencarian nama
- `master.kddk` — index untuk pengurutan list kddk (digit akhir)
- `master.alamat` — index pencarian alamat
- *Penambahan index dilakukan via SQL manual di DB `komang` (tabel `master` tidak boleh dimigrasi/ditimpa aplikasi)*

### Data Flow
1. Admin menambah user → user diberi role `user` → user dapat login.
2. User login (session) → membuka halaman pencarian.
3. Pencarian membaca tabel `master` (read-only) berdasarkan idpel/nama/alamat/trf/daya/kddk → daftar hasil.
4. Klik hasil → halaman detail pelanggan.
5. Dari detail → halaman list kddk: daftar pelanggan dalam kode kddk yang sama, diurutkan naik berdasarkan digit terakhir kode (misal target `BLBBBEA09200` → `00100`, `00200`, ...).
6. URL list kddk memuat fragment `#<id>` → browser scroll + highlight pelanggan target.

### Migration Tabel `master` (DIKERJAKAN — guarded)
Pola aman: skip jika tabel sudah ada, create jika belum (fresh setup).

```php
public function up(): void
{
    if (Schema::hasTable('master')) {
        return;
    }

    Schema::create('master', function (Blueprint $table) {
        // definisi kolom dari hasil: SHOW CREATE TABLE master
    });
}
```

**Workflow export → import:**
```
1. Export struktur + data tabel master dari DB lama:
   mysqldump -u root -p komang master > master_dump.sql
   (atau via phpMyAdmin: Export > tabel master)

2. Di DB baru: composer install && cp .env && php artisan migrate
   → tabel master dibuat otomatis dari migration

3. Import data:
   mysql -u root -p komang < master_dump.sql
```

**Syarat:** definisi kolom migration harus persis sama dengan tabel manual. Urutan:
1. Jalankan `SHOW CREATE TABLE master;` di DB `komang`
2. Tulis migration `create_master_table` mengikuti struktur asli (nama kolom + tipe + index)
3. Guard `Schema::hasTable()` seperti di atas

**Daftar migration aplikasi:**
- `create_users_table`, `create_sessions_table`, `create_password_reset_tokens_table`
- `create_permission_tables` (Spatie)
- `create_master_table` (guarded, sesuai struktur aktual)

> Aplikasi tetap read-only terhadap data `master` — migrasi hanya mengatur struktur saat fresh setup.

## BAGIAN 3: Interface Design

### Routes (routes/web.php)
| Method | Path | Controller/Page | Deskripsi | Auth |
|--------|------|-----------------|-----------|------|
| GET | `/` | redirect → dashboard/login | Titik masuk | No |
| GET | `/login` | `Auth\LoginController@create` → `Pages/Auth/Login.vue` | Form login | No (guest) |
| POST | `/login` | `Auth\LoginController@store` | Proses autentikasi (session) | No |
| POST | `/logout` | `Auth\LogoutController@destroy` | Hapus sesi | Yes |
| GET | `/dashboard` | `DashboardController@index` → `Pages/Dashboard.vue` | Halaman pencarian utama | Yes |
| GET | `/pelanggan` | `PelangganController@index` → `Pages/Pelanggan/Index.vue` | Pencarian multi-kriteria | Yes |
| GET | `/pelanggan/{master}` | `PelangganController@show` → `Pages/Pelanggan/Detail.vue` | Detail pelanggan | Yes |
| GET | `/kddk/{kode}` | `KddkController@show` → `Pages/Kddk/Index.vue` | List pelanggan dalam kddk, urut digit akhir, anchor `#<id>` | Yes |
| GET | `/admin/users` | `Admin\UserController@index` → `Pages/Admin/Users/Index.vue` | Kelola user | role:admin |
| POST | `/admin/users` | `Admin\UserController@store` | Tambah user + assign role | role:admin |
| DELETE | `/admin/users/{user}` | `Admin\UserController@destroy` | Nonaktifkan/hapus user | role:admin |

### Middleware / Group
- `web` + `HandleInertiaRequests` (semua)
- Group `auth` → dashboard, pelanggan, kddk, logout
- Group `auth` + `role:admin` → `/admin/*`
- Guest → login saja

### Halaman (Vue pages, `<script setup lang="ts">`, komponen Varlet)
| Page | Isi |
|------|-----|
| `Auth/Login.vue` | `var-login`/`var-input` email+password, submit via `useForm` Inertia |
| `Dashboard.vue` | Ringkasan + link cepat ke pencarian |
| `Pelanggan/Index.vue` | Form pencarian: kolom (idpel/nama/alamat/trf/daya/kddk) + kata kunci; `var-table` hasil; `var-pagination` |
| `Pelanggan/Detail.vue` | Info lengkap pelanggan + tombol "Lihat List kddk" → route `kddk.show` |
| `Kddk/Index.vue` | Daftar pelanggan dalam kddk terurut naik digit akhir; baris target di-highlight dari `#<id>` |
| `Admin/Users/Index.vue` | `var-table` user + form tambah user (name, email, password, role) |

### Alur URL anchor `#id`
- Route `kddk.show` diakses dengan fragment: `route('kddk.show', { kode }) + '#12'`
- Di `Kddk/Index.vue`: `onMounted` baca `window.location.hash`, scroll ke elemen `id` + styling highlight (Varlet).

### Prinsip Mobile-First UX
- **Target utama: smartphone** (petugas lapangan); tablet/desktop menyusul.
- Hasil pencarian: `var-table` untuk desktop, berubah jadi **kartu list** (`var-cell`/`var-card`) di layar kecil.
- Ukuran tap/komponen **≥ 44px**; tombol & input besar, aksi utama mudah dijangkau ibu jari.
- Input idpel memakai `type="tel"`/`inputmode="numeric"` agar muncul keyboard angka di HP.
- Pagination touch-friendly (`var-pagination` / tombol "Muat lebih").
- Detail pelanggan & list kddk: layout kartu, highlight target `#id` jelas dan menonjol.

### Data props yang dikirim `Inertia::render`
- `Pelanggan/Index`: `results` (paginated), `kriteria`, `query`
- `Pelanggan/Detail`: `pelanggan` (data master)
- `Kddk/Index`: `kode`, `list` (sorted), `targetId`
- `Admin/Users/Index`: `users`, `roles`

## BAGIAN 4: Alur Logika & Business Rules

### Alur Autentikasi (Login Manual)
1. User buka `/` → redirect ke `/login` (guest) atau `/dashboard` (sudah login).
2. `POST /login` → `LoginController@store`: validasi email+password → `Auth::attempt()`.
3. Berhasil → regenerate session → redirect `/dashboard`. Gagal → error "Email atau password salah".
4. `POST /logout` → invalidate session + regenerate token → redirect `/login`.

### Alur RBAC (Spatie, 2 role)
1. Seeder membuat role `admin` + `user`; user `admin` awal di-assign role `admin` (Spatie super-admin bypass: `Gate::before`).
2. `Admin\UserController` diproteksi `role:admin` — hanya admin yang akses `/admin/*`.
3. Admin `POST /admin/users`: buat user + `assignRole('user')`.
4. User (role `user`): hanya bisa login, cari pelanggan, lihat detail, lihat list kddk — tidak bisa akses `/admin/*`.

### Alur Pencarian Pelanggan
1. `GET /pelanggan` → `PelangganController@index`: tampilkan form + hasil kosong.
2. Submit (GET `?kolom=...&q=...`) → query ke tabel `master`:
   - `q` di-trim; kosong → tidak query.
   - `where(DB::raw('LOWER(kolom)'), 'like', '%'.strtolower($q).'%')` → case-insensitive; kolom di-whitelist.
   - Pagination 25/baris → hasil: idpel, nama, alamat, trf, daya, kddk.
3. `Inertia::render('Pelanggan/Index', [...])`.

### Alur Detail Pelanggan
1. Klik baris hasil → `GET /pelanggan/{master}` (route-model binding ke tabel `master`).
2. `PelangganController@show` → render Detail.
3. Tombol "List kddk" → `route('kddk.show', { kode: pelanggan.kddk })`.

### Alur List kddk + Anchor `#id` (fitur inti)
1. `GET /kddk/{kode}` → `KddkController@show`:
   - Ambil semua pelanggan dengan kode kddk sama.
   - Urutkan naik berdasarkan digit akhir kode: suffix numerik (`BLBBBEA09200` → `09200`), `orderBy` numerik. (Konvensi persis dikonfirmasi saat lihat data aktual.)
2. URL `route('kddk.show', { kode }) . '#<targetId>'` → prop `targetId`.
3. `Kddk/Index.vue` `onMounted`: `location.hash` → `scrollIntoView()` + highlight.

### Business Rules (dari PRD)
- Semua halaman data wajib login; tanpa registrasi publik.
- Pencarian case-insensitive, parsial, hasil di-paginate.
- Urutan list kddk selalu naik sesuai digit akhir kode.
- `#id` anchor: jika id tidak ada → halaman tetap tampil tanpa highlight.
- Tabel `master` read-only di aplikasi (tidak ada CRUD).
- Role `admin` = kelola user; role `user` = pencarian pelanggan.
- Eloquent/Query Builder (prepared statement) — aman dari SQL injection.

## BAGIAN 5: Keamanan, Performa & Deployment

### Keamanan
- Auth session manual (bcrypt) — semua halaman data di group `auth`; middleware `HandleInertiaRequests` membagikan data user.
- RBAC Spatie: route `/admin/*` middleware `role:admin`; super-admin `admin` via `Gate::before`.
- SQL injection: Eloquent/Query Builder (prepared statement); kolom pencarian di-whitelist.
- Mass assignment: `$fillable`/`$guarded` benar; form user divalidasi (email unik, password minimal).
- CSRF: token Laravel di semua POST/DELETE (Inertia otomatis sertakan `X-XSRF-TOKEN`).
- Throttle pada `POST /login` (RateLimiter).
- Session regenerate id saat login/logout; cookie `HttpOnly`.
- HTTPS saat production.

### Performa
- Index DB pada `master.idpel`, `master.nama`, `master.kddk`, `master.alamat` (manual di DB).
- Pencarian paginated (25/halaman).
- Inertia lazy-load pages (bawaan v3) → bundle per halaman.
- Hindari N+1: data master di-query sekali per halaman.

### Deployment
- Lokal V1: docroot Apache `D:\Apache24\htdocs\komang` → `http://localhost/komang/public` (atau vhost ke folder `public`).
- `.env` (`DB_DATABASE=komang`, `APP_URL`).
- Build: `npm run build`; dev: `npm run dev`.
- Deploy fresh: `composer install` → `.env` → `php artisan migrate` (master dibuat dari migration) → import dump master → `php artisan db:seed` → `npm ci && npm run build`.

### Development Setup
```bash
# 1. Buat project Laravel 13 + TS (starter kit Inertia Vue + TS):
composer create-project laravel/laravel komang
# (atau: laravel new komang --vue --typescript)

# 2. Dependensi server:
composer require inertiajs/inertia-laravel tightenco/ziggy spatie/laravel-permission

# 3. Middleware Inertia:
php artisan inertia:middleware

# 4. Dependensi client (TypeScript):
npm install @inertiajs/vue3 @inertiajs/vite vue @vitejs/plugin-vue @varlet/ui ziggy-js

# 5. Setup file inti: app.ts, vite.config.ts, tsconfig.json, Pages/*.vue

# 6. Tipe route Ziggy:
php artisan ziggy:generate --types

# 7. Migration (users, sessions, spatie, master-guarded):
php artisan migrate

# 8. Seeder role + admin:
php artisan db:seed

# 9. Jalankan dev:
npm run dev   # di terminal terpisah
php artisan serve   # atau via Apache docroot
```

> Catatan: Laravel 13 + starter kit Inertia Vue TS sangat baru — konvensi persis (versi package, nama flag) dikonfirmasi saat implementasi dari `laravel.com/docs/releases` dan starter kit resmi.
