<?php
/**
 * CONTOH pengaturan hosting. Salin file ini menjadi includes/db-local.php di server hosting,
 * lalu isi sesuai data dari panel hosting. Jangan unggah db-local.php ke GitHub.
 */
define('DB_HOST', 'sqlXXX.infinityfree.com');   // "MySQL Hostname" di panel hosting
define('DB_NAME', 'if0_12345678_goonakafe');    // nama database lengkap dengan awalannya
define('DB_USER', 'if0_12345678');              // "MySQL Username"
define('DB_PASS', 'password-hosting-kamu');     // password akun hosting / database

// Kode rahasia (minimal 8 karakter) untuk membuka install.php dan admin/setup.php dari hosting.
// Pakai kode panjang dan acak, hapus baris ini atau ubah kodenya setelah admin berhasil dibuat.
define('SETUP_KODE', 'ganti-dengan-kode-rahasia-kamu');
