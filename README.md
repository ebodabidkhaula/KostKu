# 🏠 KostKu - Sistem Manajemen Kost-Kosan

Platform pencarian dan manajemen kost berbasis **Laravel 10** + **MySQL**  
dengan fitur Admin & User, CRUD lengkap, Multimedia, SPA, dan animasi.

> ✅ **Versi ini sudah melalui audit menyeluruh**: seluruh 53 file PHP divalidasi dengan `php -l`, seluruh ekspresi Blade divalidasi sebagai PHP valid, dan struktur database (`kost_db.sql`) sudah diuji import langsung ke MariaDB sungguhan tanpa error.

---

## 📝 Changelog Perbaikan Terakhir

- ✅ Fitur **CRUD Kamar** (Tambah/Edit/Hapus kamar per kost) di halaman Detail Kost admin
- ✅ Fitur **Ulasan langsung** dari halaman detail kost (tanpa harus booking dulu)
- ✅ Lightbox foto kost kini bisa **digeser** (tombol panah, keyboard, swipe touch)
- ✅ Perbaikan bug: foto galeri tidak tersimpan saat **Edit Kost**
- ✅ Perbaikan bug: lightbox menampilkan foto yang salah saat thumbnail diklik
- ✅ Kotak "Demo Login" dihapus dari halaman login
- ✅ Kolom `booking_id` pada tabel `reviews` kini **nullable** (mendukung ulasan tanpa booking)

---

## 📋 Fitur Lengkap

| Fitur | Status |
|---|---|
| ✅ HTML, CSS, JavaScript, PHP, MySQL | Lengkap |
| ✅ Composer & Laravel Framework | Lengkap |
| ✅ CRUD Insert, Update, Delete, Query, Searching | Lengkap |
| ✅ Multimedia (Foto + Video Tour) | Lengkap |
| ✅ 7+ Jenis Input Form | Text, Password, Email, Radio, Checkbox, Dropdown/Select, Date, Tel, Textarea, Range, File |
| ✅ Layout Estetis & Responsif | Bootstrap 5 + Custom CSS |
| ✅ Efek Animasi Frontend | AOS, CSS Keyframes, Hover Transition |
| ✅ SPA (Single Page Application) | AJAX Filter + Dynamic Content |
| ✅ Framework Backend (Laravel) | Laravel 10 |
| ✅ Fitur Admin & User | Role-based Access |
| ✅ Login, Logout, Register | AuthController |

---

## 🚀 Instalasi

### Kebutuhan Sistem
- **PHP** >= 8.1
- **Composer** >= 2.x
- **MySQL** >= 8.0
- **Node.js** >= 18 (optional)

### Langkah 1: Clone & Install

```bash
# Masuk ke folder proyek
cd kost-app

# Install dependensi PHP via Composer
composer install

# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Langkah 2: Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kost_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Langkah 3: Setup Database

**Opsi A: Via Artisan (Recommended)**
```bash
# Buat database dulu di MySQL
mysql -u root -p -e "CREATE DATABASE kost_db;"

# Jalankan migrasi
php artisan migrate

# Isi data contoh
php artisan db:seed
```

**Opsi B: Via SQL langsung**
```bash
# Import file SQL
mysql -u root -p < database/kost_db.sql
```

### Langkah 4: Storage Link

```bash
php artisan storage:link
```

### Langkah 5: Jalankan Server

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🔑 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kostku.id | password |
| User | user@kostku.id | password |

---

## 🩹 Troubleshooting Umum

| Masalah | Solusi |
|---|---|
| Admin tidak bisa login | Pastikan sudah `php artisan migrate --seed` atau import `kost_db.sql`. Jika masih gagal, jalankan `php artisan tinker` lalu update password user via `Hash::make('password')` |
| Upload foto/video tidak muncul | Jalankan `php artisan storage:link`. Pastikan folder `public/storage` muncul sebagai symlink |
| Upload gagal tanpa pesan error | Cek `upload_max_filesize` dan `post_max_size` di `php.ini`, set ke `50M` dan `100M`. Restart server setelah ubah |
| Halaman blank / error 500 | Set `APP_DEBUG=true` di `.env`, refresh untuk lihat detail error. Jalankan `php artisan config:clear` |
| `419 Page Expired` | Hapus isi folder `storage/framework/sessions/*`, lalu refresh browser |
| Perubahan tidak terlihat setelah edit kode | Jalankan `php artisan view:clear` dan `php artisan route:clear` |

---

## 📁 Struktur Proyek

```
kost-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       ← Login, Register, Logout
│   │   │   ├── KostController.php       ← Publik listing kost
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── KostController.php   ← CRUD Kost
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── ReviewController.php
│   │   │   └── User/
│   │   │       ├── DashboardController.php
│   │   │       └── BookingController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Kost.php
│       ├── Room.php
│       ├── Booking.php
│       ├── KostPhoto.php
│       └── Review.php
├── database/
│   ├── migrations/                      ← Struktur tabel
│   ├── seeders/DatabaseSeeder.php       ← Data contoh
│   └── kost_db.sql                      ← SQL langsung
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                ← Layout publik
│   │   └── admin.blade.php              ← Layout admin
│   ├── auth/
│   │   ├── login.blade.php              ← Halaman login
│   │   └── register.blade.php           ← Registrasi multi-step
│   ├── admin/
│   │   ├── dashboard.blade.php          ← Dashboard + Chart
│   │   ├── kost/                        ← CRUD kost
│   │   └── booking/                     ← Manajemen booking
│   ├── user/
│   │   ├── dashboard.blade.php
│   │   └── booking/
│   ├── kost/
│   │   ├── index.blade.php              ← Listing + Filter SPA
│   │   └── show.blade.php               ← Detail + Lightbox
│   └── welcome.blade.php                ← Homepage
└── routes/web.php                       ← Semua routing
```

---

## 🎨 Jenis Input Form yang Digunakan

1. **Text** — Nama, NIK, Alamat
2. **Email** — Alamat email
3. **Password** — Password + konfirmasi
4. **Tel** — Nomor HP
5. **Date** — Tanggal lahir, check-in
6. **Radio Button** — Jenis kelamin, tipe kost
7. **Checkbox** — Fasilitas, persetujuan syarat
8. **Select/Dropdown** — Kota, provinsi, pekerjaan, durasi
9. **Textarea** — Deskripsi, catatan, alamat
10. **Range** — Budget kost, filter harga
11. **File** — Upload foto profil, foto kost, video tour

---

## 📸 Fitur Multimedia

- **Foto**: Upload thumbnail, galeri foto (multiple), foto kamar
- **Video**: Upload video tur virtual kost (MP4/AVI)
- **Lightbox**: Viewer foto fullscreen dengan navigasi
- **Preview**: Preview foto/video sebelum upload

---

## 🔄 Fitur SPA (Single Page Application)

- **Dynamic Search Filter**: Filter kost dengan AJAX tanpa reload halaman
- **Auto-submit**: Form filter otomatis apply saat pilihan berubah
- **Loading State**: Visual loading saat konten sedang dimuat
- **Tab Navigation**: Navigasi form tanpa reload (Create Kost Admin)
- **Multi-step Form**: Registrasi 3 langkah dengan animasi transisi

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 10 (PHP 8.1) |
| Database | MySQL 8 |
| Dependency | Composer |
| Frontend | Bootstrap 5, Vanilla JS |
| Animation | AOS.js, CSS Keyframes |
| Charts | Chart.js |
| Alerts | SweetAlert2 |
| HTTP Client | Axios |
| Icons | Font Awesome 6 |
| Fonts | Plus Jakarta Sans, Playfair Display |

---

## 🌐 Halaman & Route

### Publik
- `/` — Homepage dengan featured kost
- `/kost` — Listing + filter/search
- `/kost/{slug}` — Detail kost + galeri + video + booking

### Auth
- `/login` — Login
- `/register` — Register multi-step
- `/logout` — Logout

### User
- `/user/dashboard` — Dashboard user
- `/user/booking` — Riwayat booking
- `/user/booking/create/{kost}` — Form booking
- `/user/profile` — Edit profil

### Admin
- `/admin/dashboard` — Dashboard + statistik + chart
- `/admin/kost` — CRUD kost
- `/admin/booking` — Manajemen booking
- `/admin/users` — Manajemen pengguna
- `/admin/reviews` — Moderasi ulasan
- `/admin/reports` — Laporan

---

*KostKu — Platform Kost Terpercaya © 2025*
