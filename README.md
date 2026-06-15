# Lost & Found Kampus — FATEK UNSRAT

Aplikasi web untuk melaporkan dan mencari barang hilang maupun barang temuan di lingkungan Fakultas Teknik Universitas Sam Ratulangi (UNSRAT) Manado.

Website: https://lost-found-kampus-production.up.railway.app
 
=== AKUN ADMIN ===

Email    : adminlostfound@gmail.com
Password : qawsedrf
 
Akses: Dashboard admin, kelola postingan, kelola user, kelola klaim
URL Admin Panel: https://lost-found-kampus-production.up.railway.app/admin
 
 
=== AKUN USER ===

Email    : budisihombing026@gmail.com
Password : plokijuh
 
Akses: Lapor barang, klaim barang, komentar, edit profil

## Fitur Utama

- **Autentikasi** — Register, login (email/password), dan login via Google OAuth
- **Lapor Barang** — Posting barang hilang atau ditemukan lengkap dengan foto, lokasi, dan tanggal
- **Pencarian & Filter** — Cari berdasarkan nama barang, lokasi, tipe, dan status
- **Komentar** — Diskusi pada setiap postingan, termasuk balasan (reply)
- **Kontak via WhatsApp** — Tombol chat langsung ke pemilik barang dengan pesan template otomatis
- **Sistem Klaim** — Ajukan klaim kepemilikan barang dengan deskripsi bukti dan foto pendukung
- **Verifikasi Admin** — Admin meninjau dan menyetujui/menolak klaim yang masuk
- **Dokumentasi Serah Terima** — Foto bukti serah terima otomatis diberi watermark timestamp dari server (anti-pemalsuan)
- **Notifikasi Real-time** — Pemberitahuan untuk komentar baru dan status klaim (polling)
- **Profil Wajib Lengkap** — User baru (termasuk dari Google login) wajib mengisi NIM dan nomor WhatsApp
- **Admin Panel** — Dashboard statistik, kelola postingan, kelola user, dan kelola klaim

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 10 (PHP) |
| Frontend | Vue 3 + Inertia.js |
| Build Tool | Vite |
| Database | MySQL |
| Styling | Bootstrap 5 + Custom CSS |
| Storage Foto | Cloudinary |
| Hosting | Railway |

## Instalasi Lokal

```bash
# Clone repository
git clone https://github.com/USERNAME/lost-found-kampus.git
cd lost-found-kampus

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Jalankan storage symlink
php artisan storage:link

# Jalankan development server (2 terminal)
npm run dev
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

## Konfigurasi Tambahan

Tambahkan ke `.env`:

```
# Google OAuth (opsional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

# Cloudinary (untuk upload foto)
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

## Struktur Proyek

```
app/Http/Controllers/   → Logic aplikasi (Item, Claim, Admin, dll)
app/Models/              → Model database (Item, User, ClaimRequest, dll)
resources/js/Pages/      → Halaman Vue (Items, Auth, Admin, Profile)
resources/js/Layouts/    → Layout utama (AppLayout, AdminLayout, AuthLayout)
routes/web.php           → Definisi route aplikasi
database/migrations/     → Struktur tabel database
```

## Tim Pengembang — Kelompok B1

| Nama | NIM | Tugas |
|---|---|---|
| Jennifer Gloria Manoppo | 230211060081 | Frontend & Database |
| Kezia Floresita Ngama | 230211060084 | Frontend |
| Natanael Parulian Sitompul | 230211060087 | Backend |
| Vancel Bernard Fredrik Rengkung | 230211060105 | Backend |

---

Dibuat untuk tugas mata kuliah Pengembangan Aplikasi Web Berbasis Framework — Teknik Informatika UNSRAT.
