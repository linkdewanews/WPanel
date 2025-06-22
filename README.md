<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


====================================================================================================================
# Panel Posting WordPress dengan Laravel & Filament

Ini adalah panel custom yang dibangun dengan Laravel 11 dan Filament 3 untuk mengelola beberapa situs WordPress dan melakukan posting artikel secara terpusat melalui WordPress REST API. Proyek ini tidak memerlukan instalasi plugin tambahan di sisi WordPress.

## Fitur Utama

* **Manajemen Situs (CRUD):** Tambah, lihat, edit, dan hapus data situs WordPress target.
* **Posting Individual:** Memposting artikel ke satu situs spesifik dari daftar.
* **Posting Massal (Bulk Posting):** Menulis satu artikel dan mempublikasikannya ke banyak situs sekaligus.
* **Opsi Publikasi Fleksibel:** Pilihan untuk langsung publikasi, simpan sebagai draft, atau menjadwalkan postingan.
* **Riwayat Postingan (History):** Semua aktivitas posting (berhasil/gagal) tercatat rapi dalam sebuah tabel.
* **Edit Postingan:** Kemampuan untuk mengedit kembali artikel yang sudah diposting langsung dari halaman history.
* **Dashboard Informatif:** Dilengkapi statistik, daftar aktivitas terbaru, dan grafik postingan.

## Prasyarat

Sebelum memulai, pastikan lingkungan Anda memenuhi syarat berikut:
* PHP 8.2+
* Composer
* Node.js & NPM
* Database (MySQL, MariaDB, atau PostgreSQL)

## Instalasi & Setup di Lokal (dari GitHub)

Berikut adalah langkah-langkah untuk menjalankan proyek ini dari repositori GitHub di lingkungan pengembangan lokal.

1.  **Clone Repositori**
    Ganti `NAMA_USER_ANDA/NAMA_REPO_ANDA` dengan URL repositori Anda.
    ```bash
    git clone [https://github.com/linkdewanews/WPanel.git]
    ```

2.  **Masuk ke Direktori Proyek**
    ```bash
    cd WPanel
    ```

3.  **Install Dependencies PHP**
    Perintah ini akan mengunduh semua library dari `composer.json` ke dalam folder `vendor`.
    ```bash
    composer install
    ```

4.  **Buat dan Konfigurasi File `.env`**
    Salin file contoh dan kemudian edit isinya.
    ```bash
    # Untuk Mac/Linux
    cp .env.example .env

    # Untuk Windows
    copy .env.example .env
    ```
    *PENTING: Buka file `.env` dan isi bagian `DB_*` dengan konfigurasi database lokal Anda. Atur juga `APP_URL` dan pastikan `APP_DEBUG=true`.*

5.  **Generate Kunci Aplikasi**
    Ini akan mengisi variabel `APP_KEY` di file `.env`.
    ```bash
    php artisan key:generate
    ```

6.  **Jalankan Migrasi Database**
    Perintah ini akan membuat semua tabel (`users`, `sites`, `post_histories`, dll.) di database Anda.
    ```bash
    php artisan migrate
    ```

7.  **Buat User Admin Pertama**
    Anda akan diminta untuk memasukkan nama, email, dan password untuk login ke panel.
    ```bash
    php artisan make:filament-user
    ```

8.  **Buat Symbolic Link untuk Storage**
    Ini penting agar file yang di-upload bisa diakses.
    ```bash
    php artisan storage:link
    ```

9.  **(Opsional) Install & Compile Aset Frontend**
    ```bash
    npm install
    npm run dev
    ```
    *(Tekan `Ctrl + C` untuk menghentikan proses `npm run dev` setelah selesai atau biarkan berjalan).*

10.  **import database**
     ```bash
    pada file panel.sql import ke phpmyadmin
    ```

## Menjalankan Server Development

Setelah semua langkah setup selesai, jalankan server lokal Laravel.
```bash
php artisan serve
```
Aplikasi Anda sekarang berjalan! Buka browser dan kunjungi alamat yang ditampilkan (biasanya `http://127.0.0.1:8000`).

Untuk masuk ke panel admin, kunjungi: **`http://127.0.0.1:8000/admin`**

---