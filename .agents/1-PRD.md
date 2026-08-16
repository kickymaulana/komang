# PRD Komang — Aplikasi Pencarian Pelanggan PLN

## BAGIAN 1: Visi & Tujuan Produk

### Visi Produk
Komang adalah aplikasi pencarian data pelanggan PLN berbasis web yang membantu admin/marketing menemukan pelanggan dengan cepat hanya dari satu kata kunci — baik itu nomor ID pelanggan, nama, alamat, tarif, daya, maupun kode kddk — lalu menelusuri daftar area (kddk) secara berurutan sampai menemukan titik yang dicari.

### Tujuan Utama
1. **Pencarian cepat multi-kriteria** — hasil pencarian muncul < 2 detik untuk basis data sampai 100.000 baris.
2. **Navigasi kddk berurutan** — klik pelanggan langsung menuju daftar kddk terurut berdasarkan digit akhir (contoh: target `09200` → daftar dimulai `00100`, `00200`, ...), dengan anchor `#id` mengarah ke pelanggan yang dicari.
3. **Akses terkontrol** — hanya pengguna terautentikasi (login Laravel) yang bisa mengakses data.
4. **Data akurat dari master table** — aplikasi membaca langsung dari tabel `master` di database `komang` tanpa duplikasi data.

### Value Proposition
- **Tidak perlu hafal nomor lengkap** — cukup salah satu kolom (idpel, nama, alamat, trf, daya, kddk) untuk menemukan pelanggan.
- **List kddk terurut otomatis** — urutan digit akhir disusun sistem, bukan manual, sehingga target pelanggan mudah dilacak.
- **Langsung menuju titik target** — link memakai `#id` sehingga pelanggan yang dicari langsung tersorot di halaman list.

## BAGIAN 2: User Persona

### Persona 1: Budi — Staff Lapangan PLN
- **Usia/Pekerjaan:** 34, Petugas Lapangan / Surveyor
- **Level Teknis:** Menengah (tidak terlalu paham IT, terbiasa aplikasi internal)
- **Tujuan:** Mencari data pelanggan dari lokasi lapangan dengan cepat — cukup dari idpel, nama, atau alamat, tanpa harus membuka file Excel.
- **Pain Points:** Sering dapat nomor pelanggan tidak lengkap atau hanya nama/alamat; sulit melacak pelanggan yang berada dalam satu area (kddk) karena data tersebar.
- **Motivasi:** Ingin selesai cepat saat survei/pemeriksaan meter di lapangan dan langsung tahu urutan pelanggan dalam satu kddk.

### Persona 2: Sari — Admin Back Office
- **Usia/Pekerjaan:** 28, Admin Data / Operator
- **Level Teknis:** Mahir (sering olah data, mengerti struktur tabel)
- **Tujuan:** Mencari pelanggan berdasarkan trf, daya, atau kddk untuk keperluan rekapan, audit, dan pelaporan.
- **Pain Points:** Pencarian di tabel besar lambat; harus scroll manual dan tidak ada urutan yang konsisten; khawatir salah ambil data.
- **Motivasi:** Ingin hasil pencarian akurat, urut, dan bisa langsung membagikan link (dengan `#id`) ke rekan kerja.

## BAGIAN 3: User Stories

### Modul 1: Autentikasi
- Sebagai pengguna, saya ingin login dengan akun Laravel, agar hanya saya yang bisa mengakses data pelanggan.
- Sebagai pengguna, saya ingin logout, agar sesi saya aman setelah selesai bekerja.
- Sebagai pengguna, saya ingin pengaturan akun dibuat oleh admin (tanpa registrasi publik), agar data internal tidak bisa diakses orang luar.

### Modul 2: Pencarian Pelanggan
- Sebagai pengguna, saya ingin mencari pelanggan berdasarkan **idpel**, agar bisa menemukan pelanggan dari nomor lengkap.
- Sebagai pengguna, saya ingin mencari pelanggan berdasarkan **nama**, agar bisa menemukan pelanggan walau hanya tahu namanya.
- Sebagai pengguna, saya ingin mencari pelanggan berdasarkan **alamat**, agar bisa menemukan pelanggan di wilayah tertentu.
- Sebagai pengguna, saya ingin mencari pelanggan berdasarkan **trf**, **daya**, atau **kddk**, agar bisa menyaring pelanggan dengan spesifikasi tertentu.
- Sebagai pengguna, saya ingin hasil pencarian menampilkan kolom idpel, nama, alamat, trf, daya, dan kddk, agar saya bisa mengidentifikasi pelanggan sekilas tanpa buka detail.
- Sebagai pengguna, saya ingin hasil pencarian tampil cepat, agar tidak menunggu lama saat basis data besar.

### Modul 3: Detail Pelanggan & List kddk
- Sebagai pengguna, saya ingin mengklik hasil pencarian, agar saya diarahkan ke halaman detail pelanggan.
- Sebagai pengguna, saya ingin melihat list kddk yang diurutkan berdasarkan digit terakhir (contoh target `09200` → dimulai `00100`, `00200`, `00300`, dst.), agar urutan pelanggan dalam satu area konsisten.
- Sebagai pengguna, saya ingin URL list kddk menyertakan `#id` pelanggan, agar halaman langsung mengarah/menyorot ke pelanggan yang saya cari.
- Sebagai pengguna, saya ingin tetap bisa pindah ke pelanggan lain di list kddk yang sama, agar saya bisa menelusuri area tersebut.

*(Total: 13 user stories)*

## BAGIAN 4: Functional Requirements

### Modul 1: Autentikasi

**FR-01: Login Pengguna**
- **Input:** Email, password
- **Proses:** Verifikasi kredensial Laravel (Auth), mulai sesi
- **Output:** Redirect ke halaman pencarian (dashboard)
- **Aturan:** Semua halaman data pelanggan wajib login; tanpa login redirect ke /login

**FR-02: Logout**
- **Input:** Klik tombol logout
- **Proses:** Hapus sesi
- **Output:** Redirect ke halaman login
- **Aturan:** Setelah logout, akses halaman data ditolak

**FR-03: Manajemen Akun (Admin)**
- **Input:** Data user baru
- **Proses:** Admin membuat/menonaktifkan user via seeder/CLI
- **Output:** Akun tersimpan di tabel users
- **Aturan:** Tidak ada registrasi publik (halaman register dinonaktifkan)

### Modul 2: Pencarian Pelanggan

**FR-04: Pencarian Berdasarkan idpel**
- **Input:** Nomor idpel
- **Proses:** Query LIKE/hash match ke kolom idpel tabel `master`
- **Output:** Hasil yang cocok
- **Aturan:** Pencarian sebagian karakter diizinkan

**FR-05: Pencarian Berdasarkan Nama**
- **Input:** Nama
- **Proses:** Query LIKE ke kolom nama
- **Output:** Hasil yang cocok
- **Aturan:** Tidak case-sensitive

**FR-06: Pencarian Berdasarkan Alamat**
- **Input:** Alamat
- **Proses:** Query LIKE ke kolom alamat
- **Output:** Hasil yang cocok
- **Aturan:** Tidak case-sensitive

**FR-07: Pencarian Berdasarkan trf / daya / kddk**
- **Input:** trf, daya, atau kddk
- **Proses:** Query pencocokan kolom terkait
- **Output:** Hasil yang cocok
- **Aturan:** Pengguna dapat memilih satu kriteria (atau kombinasi yang tersedia)

**FR-08: Tampilan Hasil Pencarian**
- **Input:** Query pencarian
- **Proses:** Render tabel hasil
- **Output:** Tabel berisi idpel, nama, alamat, trf, daya, kddk
- **Aturan:** Hasil diberi pagination; kolom menampilkan ikon/aksi untuk klik

### Modul 3: Detail Pelanggan & List kddk

**FR-09: Klik Hasil → Halaman Detail**
- **Input:** Klik baris hasil pencarian
- **Proses:** Sistem mengarahkan ke halaman detail pelanggan
- **Output:** Halaman detail dengan info lengkap pelanggan + tombol ke list kddk
- **Aturan:** URL detail memakai id pelanggan

**FR-10: List Kode kddk Terurut**
- **Input:** kddk pelanggan target
- **Proses:** Sistem menampilkan daftar kode kddk yang diurutkan berdasarkan digit akhir (contoh target `BLBBBEA09200` → urut dari `00100`, `00200`, `00300`, ..., `09200`)
- **Output:** List kddk berurutan naik
- **Aturan:** Urutan mengikuti digit terakhir kode kddk secara numerik (per 100-an, dan loncatan seperti `00301` dipertahankan sesuai data)

**FR-11: Anchor `#id` ke Pelanggan Target**
- **Input:** Klik pelanggan pada list kddk
- **Proses:** URL list kddk menyertakan fragment `#<id-pelanggan>`
- **Output:** Halaman langsung scroll/scroll ke baris pelanggan target dan diberi highlight
- **Aturan:** Jika id tidak ditemukan, halaman tetap tampil tanpa highlight

**FR-12: Navigasi Antar Pelanggan dalam List kddk**
- **Input:** Klik pelanggan lain di list kddk
- **Proses:** Buka detail pelanggan tersebut
- **Output:** Halaman detail pelanggan baru
- **Aturan:** Tetap dalam konteks kddk yang sama

## BAGIAN 5: Non-Functional Requirements

### Performa
- Waktu muat halaman pencarian < 2 detik
- Hasil pencarian (query) tampil < 500ms untuk data 100.000+ baris
- Pagination hasil pencarian (misal 25–50 baris per halaman)

### Keamanan
- Autentikasi Laravel wajib untuk semua halaman data
- Password di-hash (bcrypt bawaan Laravel)
- Pencarian memakai prepared statement (Eloquent/Query Builder) agar aman dari SQL injection
- Data hanya bisa diakses via HTTPS saat production

### Skalabilitas
- Target data master sampai 100.000+ baris
- Pencarian didukung index database pada kolom yang sering dicari (idpel, nama, alamat, kddk)

### Usability
- **Mobile-first** — smartphone adalah perangkat target utama (petugas lapangan pakai HP), lalu tablet/desktop menyusul
- Antarmuka bahasa Indonesia
- Navigasi sederhana: halaman pencarian → hasil → detail → list kddk

### Maintainability
- Kode mengikuti struktur Laravel standar (Model, Controller, Route, Blade)
- Konfigurasi koneksi database memakai `.env` (`DB_DATABASE=komang`)
- Nama tabel dipakai langsung `master` tanpa migrasi duplikat (tabel sudah dibuat manual di database)

## BAGIAN 6: Out of Scope & Dependensi

### Out of Scope (Tidak Dikerjakan di V1)
- **CRUD master pelanggan (tambah/edit/hapus)** — tabel `master` sudah dibuat manual, aplikasi V1 hanya membaca (read-only) — ditunda ke v2
- **Registrasi publik & role lanjutan** (admin vs operator dengan hak berbeda) — hanya login sederhana — ditunda ke v2
- **Import/export Excel** — ditunda ke v2
- **Rekap/grafik analitik** — ditunda ke v2

### Dependensi
- **Laravel (PHP)** — framework untuk autentikasi & routing
- **MySQL (database `komang`)** — sumber data, tabel `master` sudah ada
- **Apache (XAMPP/Apache24)** — web server, project berada di `D:\Apache24\htdocs\komang`
- **XAMPP/PHP** — runtime PHP versi yang kompatibel dengan Laravel yang dipilih

### Asumsi
- Tabel `master` sudah berisi data pelanggan lengkap (kolom: idpel, nama, alamat, trf, daya, kddk, dan field lain yang dibutuhkan).
- Struktur kolom tabel `master` sudah sesuai kebutuhan pencarian (nama kolom dikonfirmasi saat implementasi).
- Pengguna (staff PLN) memakai perangkat dengan browser modern; petugas lapangan kadang pakai HP.
- Aplikasi berjalan di lingkungan lokal/Apache; belum ada deployment ke server publik.
