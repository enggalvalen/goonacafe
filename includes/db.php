<?php
/**
 * Koneksi database MySQL.
 * Di XAMPP: tanpa file tambahan, dipakai pengaturan bawaan (root, tanpa password, database goonakafe).
 * Di hosting: buat file includes/db-local.php (lihat db-local.example.php) berisi host, nama database,
 * user, dan password dari hosting. File itu tidak ikut ke GitHub (ada di .gitignore).
 */
if (is_file(__DIR__ . '/db-local.php')) require_once __DIR__ . '/db-local.php';
defined('DB_HOST') || define('DB_HOST', '127.0.0.1');
defined('DB_NAME') || define('DB_NAME', 'goonakafe');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

/** Mengembalikan objek PDO, atau null kalau database belum aktif / belum diinstall. */
function db() {
    static $pdo = null, $dicoba = false;
    if ($dicoba) return $pdo;
    $dicoba = true;
    if (!extension_loaded('pdo_mysql')) return null;
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 3,
        ]);
    } catch (Throwable $ex) {
        $pdo = null;
    }
    return $pdo;
}
