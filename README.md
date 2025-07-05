# Aplikasi Pemesanan Kendaraan

Aplikasi web untuk monitoring dan pemesanan kendaraan operasional perusahaan.

## Deskripsi

Aplikasi ini dibuat untuk memenuhi kebutuhan perusahaan dalam mengelola dan memonitoring penggunaan kendaraan, mulai dari konsumsi BBM, jadwal servis, hingga riwayat pemakaian. Aplikasi ini juga memfasilitasi proses pemesanan kendaraan oleh pegawai yang harus melalui alur persetujuan berjenjang.

## Tech Stack

- **Framework**: CodeIgniter 3.1.13
- **Bahasa**: PHP 7.4 / 8.x
- **Database**: MySQL / MariaDB
- **Frontend**: Bootstrap, jQuery, HTML, CSS

## Fitur Utama

-   **Manajemen Pengguna**: Mengelola pengguna dengan peran Admin dan Penyetuju (Approver).
-   **Manajemen Kendaraan**: Mengelola data kendaraan milik perusahaan dan sewaan.
-   **Pemesanan Kendaraan**: Admin dapat membuat pesanan kendaraan baru.
-   **Alur Persetujuan**: Proses persetujuan pemesanan minimal 2 level.
-   **Dashboard**: Menampilkan grafik visual untuk pemakaian kendaraan.
-   **Laporan**: Ekspor data pemesanan periodik ke format Excel.
-   **Logging**: Pencatatan aktivitas penting dalam aplikasi.

## Informasi Kredensial (Contoh)

-   **Admin**
    -   **Username**: admin
    -   **Password**: password
-   **Approver Level 1**
    -   **Username**: approver1
    -   **Password**: password
-   **Approver Level 2**
    -   **Username**: approver2
    -   **Password**: password

## Panduan Instalasi

1.  Clone repositori ini.
2.  Import file `.sql` yang tersedia ke dalam database MySQL Anda.
3.  Konfigurasi koneksi database di `application/config/database.php`.
4.  Sesuaikan `base_url` di `application/config/config.php`.
5.  Jalankan aplikasi melalui web server Anda (misal: XAMPP).

---
*Dokumentasi ini akan diperbarui seiring dengan perkembangan aplikasi.*
