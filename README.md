# RUP Intelligence Dashboard

Portal web untuk pengelolaan dan monitoring data **RUP (Rencana Umum Pengadaan)**. Aplikasi ini dibangun dengan Laravel 12 dan menampilkan data pengadaan secara terstruktur, lengkap dengan integrasi API, chat customer service, dan preview modul OpenClaw.

## Fitur

- **Dashboard interaktif** — statistik, tabel data RUP, grafik distribusi tahunan, dan trend bulanan
- **Pencarian & filter** — cari berdasarkan pekerjaan, instansi, atau ID RUP; filter berdasarkan tahun anggaran
- **Detail record** — halaman detail lengkap untuk setiap entri RUP
- **OpenClaw Preview** — antarmuka mock untuk integrasi scraping data
- **Customer Service Chat** — widget chat yang terhubung ke API Laravel (siap diintegrasikan dengan n8n)
- **History & Notifications** — riwayat webhook dan daftar pemberitahuan
- **Tema gelap & terang** — toggle tema di setiap halaman, preferensi disimpan di browser

## Tema Gelap / Terang

Website mendukung dua mode tampilan:

| Mode | Keterangan |
|------|------------|
| **Gelap** | Tampilan default dengan latar gelap (cocok untuk monitoring malam hari) |
| **Terang** | Tampilan cerah dengan kontras tinggi untuk kondisi pencahayaan terang |

Cara mengganti tema:
1. Klik tombol **☀️ / 🌙** di pojok kanan atas halaman
2. Pilihan tema otomatis tersimpan di `localStorage` browser
3. Saat pertama kali dibuka, tema mengikuti preferensi sistem operasi jika belum pernah dipilih

## Persyaratan

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL (atau SQLite untuk development)

## Instalasi

```bash
# Clone repository
git clone <url-repo> website-sim-kokek
cd website-sim-kokek

# Install dependensi PHP & Node
composer install
npm install

# Salin konfigurasi environment
cp .env.example .env
php artisan key:generate

# Atur koneksi database di .env
# DB_CONNECTION=mysql
# DB_DATABASE=magang_db
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi & seeder
php artisan migrate
php artisan db:seed

# Build asset frontend
npm run build
```

Atau gunakan script setup bawaan:

```bash
composer run setup
```

## Menjalankan Aplikasi

**Development (server + queue + Vite):**

```bash
composer run dev
```

**Manual:**

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Vite dev server
npm run dev
```

Buka browser di [http://localhost:8000](http://localhost:8000).

## Struktur Halaman

| Route | Halaman |
|-------|---------|
| `/` | Dashboard utama |
| `/records/{id}` | Detail record RUP |
| `/openclaw` | Preview integrasi OpenClaw |
| `/history` | Riwayat webhook & pesan |
| `/notifications` | Daftar notifikasi |

## API Endpoints

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| `GET` | `/api/dashboard` | Data statistik dashboard |
| `POST` | `/api/chat` | Kirim pesan chat CS |
| `POST` | `/api/n8n/webhook` | Webhook dari n8n |
| `GET` | `/api/history` | Data history (JSON) |
| `GET` | `/api/download` | Download data |
| `GET` | `/api/notifications` | Data notifikasi (JSON) |

## Struktur Frontend

```
resources/
├── css/
│   └── app.css          # Variabel tema + styles global
├── js/
│   ├── app.js           # Entry point Vite
│   └── theme.js         # Logika toggle tema gelap/terang
└── views/
    ├── components/
    │   ├── theme-init.blade.php    # Script anti-flash saat load
    │   └── theme-toggle.blade.php  # Tombol toggle tema
    └── layouts/
        ├── app.blade.php           # Layout dasar
        └── dashboard.blade.php     # Layout dengan sidebar
```

## Testing

```bash
composer run test
# atau
php artisan test
```

## Lisensi

Proyek ini menggunakan [MIT License](https://opensource.org/licenses/MIT).
