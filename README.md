# Aplikasi Chat Real-Time

Aplikasi chat real-time yang dibangun menggunakan Laravel 12 dan Laravel Reverb WebSocket. Aplikasi ini memungkinkan pengguna berkomunikasi secara langsung melalui chat pribadi maupun grup.

## Fitur

* Autentikasi Pengguna (Login, Registrasi, dan Logout)
* Chat Pribadi (Private Chat)
* Chat Grup (Group Chat)
* Real-Time Messaging
* Status Pengguna (Online/Offline)

## Teknologi

* Laravel 12
* Laravel Reverb
* Laravel Breeze
* MySQL
* Blade & JavaScript

## Menjalankan Aplikasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Jalankan aplikasi:

```bash
php artisan serve
php artisan reverb:start
npm run dev
```

Akses aplikasi melalui:

```text
http://127.0.0.1:8000
```


Proyek ini dibuat untuk keperluan pembelajaran dan tugas perkuliahan.
