# Resonate 🎵

**Resonate** adalah aplikasi berbasis web yang memungkinkan pengguna untuk berbagi pesan (notes) yang terhubung dengan musik. Pengguna dapat mencari trek musik, memberikan balasan, dan berinteraksi dalam komunitas yang dimoderasi.

---

## ✨ Fitur Utama

- **Integrasi Musik**: Cari dan hubungkan lagu favorit dari layanan musik (Deezer) ke dalam pesan Anda.
- **Sistem Pesan (Notes)**: Buat pesan publik (Global) atau pribadi (My Notes).
- **Interaksi Real-time**: Sistem balasan (replies) pada setiap pesan dan notifikasi langsung.
- **Autentikasi Ganda**: Login konvensional atau menggunakan **Google OAuth**.
- **Panel Admin**: Moderasi konten, manajemen pengguna, dan sistem banned/blokir.
- **Kustomisasi Profil**: Unggah avatar dan pilih tema kartu (Card Theme).
- **Keamanan**: Dilengkapi dengan middleware untuk mengecek status akun (banned) secara real-time.

---

## 🚀 Teknologi yang Digunakan

### Back-end (Laravel 12)

- **Framework**: Laravel 12
- **Authentication**: Laravel Sanctum (API) & Socialite (Google Auth)
- **Database**: MySQL / SQLite
- **Mailing**: SMTP Gmail

### Front-end (Vue 3)

- **Framework**: Vue 3 dengan Vite
- **Styling**: Tailwind CSS 4
- **State Management**: VueUse
- **Lainnya**: SweetAlert2, Day.js, Vue Router

---

## 🛠️ Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan Resonate di lingkungan lokal Anda.

### 1. Persiapan Back-end

Masuk ke folder `back-end`, lalu jalankan:

```
# Install dependensi PHP
composer install

# Salin file konfigurasi .env
cp .env.example .env

# Generate application key
php artisan key:generate
```
### 2. Konfigurasi Database & Environment
Buka file .env dan sesuaikan pengaturan berikut:
- Database: Buat database kosong bernama music_note_app di MySQL Anda.
- Google Auth: Dapatkan ID dan Secret dari [Google Cloud Console](https://console.cloud.google.com/)
- Email: Gunakan "App Password" dari akun Google Anda untuk MAIL_PASSWORD.

```
DB_DATABASE=music_note_app
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi Google OAuth
GOOGLE_CLIENT_ID=isi_dengan_id_anda
GOOGLE_CLIENT_SECRET=isi_dengan_secret_anda
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Konfigurasi Email (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=kode_password_aplikasi_google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email_anda@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Migrasi & Storage Link (Penting!)
- Agar foto profil/avatar bisa tampil, Anda wajib menjalankan perintah link storage.
```
# Jalankan migrasi database dan seeder
php artisan migrate --seed

# Hubungkan folder storage ke publik (Wajib agar foto muncul)
php artisan storage:link
```
### 4. Persiapan Front-end
- Masuk ke folder front-end, lalu jalankan:
```
# Install dependensi Node.js
npm install

# Jalankan server development
npm run dev
```

---

## ⚙️ Konfigurasi Tambahan
### Foto Profil Tidak Muncul?
Pastikan Anda sudah menjalankan php artisan storage:link. Laravel akan membuat shortcut dari folder storage/app/public ke public/storage. Pastikan juga APP_URL di .env sudah benar (http://localhost).

### Notifikasi Email
 Untuk menerima email reset password atau notifikasi lainnya:
   - Aktifkan 2-Step Verification di akun Google Anda.
   - Cari menu App Passwords (Password Aplikasi).
   - Buat password baru untuk "Mail" dan pilih perangkat "Other".
   - Copy kode 16 digit tersebut ke MAIL_PASSWORD di file .env.

   ---

## 📂 Struktur Folder
- /back-end: Berisi kode sumber API Laravel, Model, Controller, dan Migrations.

- /front-end: Berisi kode sumber Vue.js, Components, Assets, dan logic tampilan.

---

## 🤝 Kontribusi
Kontribusi sangat diterima! Jika kamu menemukan bug atau ingin menambah fitur:
1. Fork repository ini.
2. Buat branch fitur baru (git checkout -b fitur-keren).
3. Commit perubahanmu (git commit -m 'Menambahkan fitur keren').
4. Push ke branch (git push origin fitur-keren).
5. Buat Pull Request.


## Dibuat dengan ❤️ oleh Abdian