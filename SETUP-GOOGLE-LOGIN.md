# Setup — Login Google + Fix Tombol Mata

File-file di ZIP ini tinggal di-**extract di root project** `lost-found-kampus/`
(timpa file lama). Setelah itu lakukan 4 langkah ini:

## 1. Install Laravel Socialite
```bash
composer require laravel/socialite
```

## 2. Isi kredensial Google di file .env
Tambahkan (atau isi) baris berikut di `.env` kamu:
```env
GOOGLE_CLIENT_ID=isi-client-id-dari-google
GOOGLE_CLIENT_SECRET=isi-client-secret-dari-google
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```
> Catatan: `.env` TIDAK disertakan di ZIP ini supaya secret kamu tidak ketimpa.
> Acuannya ada di `.env.example`.

## 3. Buat kredensial OAuth di Google Cloud Console
1. Buka https://console.cloud.google.com/ → buat / pilih project.
2. **APIs & Services → OAuth consent screen** → pilih *External* → isi nama app & email.
3. **APIs & Services → Credentials → Create Credentials → OAuth client ID**.
4. Application type: **Web application**.
5. **Authorized redirect URIs** → tambahkan PERSIS:
   ```
   http://127.0.0.1:8000/auth/google/callback
   ```
6. Copy **Client ID** & **Client Secret** ke `.env` (langkah 2).

## 4. Jalankan migration (menambah kolom google_id & avatar)
```bash
php artisan migrate
php artisan config:clear
```

Selesai. Tombol "Login/Daftar dengan Google" sudah aktif di halaman /login dan /register.

---

## Yang berubah di patch ini
**Fix tombol mata password**
- `resources/views/layouts/auth.blade.php` — tambah `@stack('scripts')` + `@stack('styles')`
  (sebelumnya script toggle di login tidak pernah jalan karena stack-nya tidak ada).
- `public/css/app.css` — sembunyikan tombol reveal bawaan browser
  (`::-ms-reveal`) yang bikin ikon mata kelihatan dobel + style tombol Google & divider.
- `resources/views/auth/login.blade.php` — toggle dibuat reusable (`.toggle-password`).
- `resources/views/auth/register.blade.php` — tambah toggle mata yang berfungsi
  di field Password & Konfirmasi Password.

**Login dengan Google**
- `config/services.php` — blok config `google`.
- `routes/web.php` — route `google.redirect` & `google.callback`.
- `app/Http/Controllers/Auth/GoogleController.php` — controller OAuth (baru).
- `app/Models/User.php` — `google_id` & `avatar` masuk `$fillable`.
- `database/migrations/2024_01_04_000001_add_google_id_to_users_table.php` — kolom baru.
- `resources/views/auth/login.blade.php` & `register.blade.php` — tombol Google.
