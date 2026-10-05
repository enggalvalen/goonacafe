<?php
/**
 * Installer database Goona Cafe (sekali jalan).
 * XAMPP : buka http://localhost/goonakafe/install.php
 * Hosting: buka https://DOMAINMU/install.php?kode=SETUP_KODE (kode dari includes/db-local.php)
 * Membuat semua tabel dan mengisi 29 menu. Setelah berhasil, HAPUS file ini.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/setup-guard.php';

function e_q($t) { return htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); }

function halaman($judul, $isi, $ok = true) {
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>Install Database - Goona Cafe</title><style>body{font-family:system-ui,sans-serif;background:#F7EFE0;color:#2B1416;margin:0;padding:40px 16px}'
       . '.k{max-width:560px;margin:0 auto;background:#fff;border:1px solid #e3d6bd;border-radius:8px;padding:28px}h1{font-size:22px;margin:0 0 12px;color:#8E1B1F}'
       . 'a.b{display:inline-block;margin:6px 8px 0 0;background:#8E1B1F;color:#fff;padding:10px 18px;border-radius:4px;text-decoration:none}code{background:#f3e9d4;padding:1px 6px;border-radius:3px}</style></head><body><div class="k">'
       . '<h1>' . htmlspecialchars($judul) . '</h1>' . $isi . '</div></body></html>';
    exit;
}

if (!setup_boleh()) {
    http_response_code(403);
    halaman('Tidak diizinkan', '<p>Installer hanya bisa dibuka dari komputer lokal, atau dari hosting dengan kode rahasia yang benar '
        . '(<code>install.php?kode=...</code>, kode diatur sebagai <code>SETUP_KODE</code> di <code>includes/db-local.php</code>, minimal 8 karakter).</p>', false);
}
if (!extension_loaded('pdo_mysql')) {
    halaman('Ekstensi PHP belum aktif', '<p>Ekstensi <code>pdo_mysql</code> belum aktif. Di XAMPP biasanya sudah aktif; cek <code>php.ini</code>.</p>', false);
}

$opsi = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => true, PDO::ATTR_TIMEOUT => 5];
try {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, $opsi);
    } catch (PDOException $ex) {
        if ((int)($ex->errorInfo[1] ?? 0) !== 1049) throw $ex;   // 1049 = database belum ada
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, $opsi);
        $pdo->exec('CREATE DATABASE `' . str_replace('`', '', DB_NAME) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo->exec('USE `' . str_replace('`', '', DB_NAME) . '`');
    }
} catch (Throwable $ex) {
    halaman('Tidak bisa terhubung ke database', '<p>Periksa MySQL sudah menyala (XAMPP) atau isi <code>includes/db-local.php</code> '
        . '(host, nama database, user, password) sesuai panel hosting. Di hosting, database harus dibuat dulu lewat panel.</p>', false);
}

$sudah = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = 'produk'")->fetchColumn();
if ($sudah) {
    halaman('Database sudah terpasang', '<p>Tabel sudah ada, jadi installer tidak mengimpor ulang (data tidak akan tertimpa).</p>'
        . '<p>Buat password admin di <a href="admin/setup.php' . e_q(setup_query()) . '">admin/setup.php</a> bila belum. Lalu hapus file <code>install.php</code> dari folder.</p>'
        . '<a class="b" href="admin/setup.php' . e_q(setup_query()) . '">Atur Admin</a><a class="b" href="index.php">Ke Website</a>');
}

try {
    // Buang baris CREATE DATABASE / USE: database sudah dipilih di atas (di hosting namanya ditentukan penyedia).
    $sql = preg_replace('/^\s*(CREATE DATABASE|USE)\b[^;]*;\s*$/mi', '', file_get_contents(__DIR__ . '/database.sql'));
    $pdo->exec($sql);
} catch (Throwable $ex) {
    halaman('Install gagal', '<p>Terjadi kesalahan saat membuat tabel. Pastikan MySQL berjalan dengan user <code>' . htmlspecialchars(DB_USER) . '</code>.</p>', false);
}
halaman('Database berhasil dibuat', '<p>Database <code>' . DB_NAME . '</code>, semua tabel, dan 29 menu sudah terpasang.</p>'
    . '<p><strong>Langkah berikutnya:</strong> buat password admin (username <code>admingoona</code>), lalu hapus file <code>install.php</code>.</p>'
    . '<a class="b" href="admin/setup.php' . e_q(setup_query()) . '">Buat Password Admin</a><a class="b" href="index.php">Ke Website</a>');
