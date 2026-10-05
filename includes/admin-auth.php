<?php
/**
 * Fungsi bersama panel admin: sesi, login, CSRF, pesan flash, layout.
 * Sesi admin memakai nama cookie sendiri, terpisah dari keranjang pelanggan.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('goona_admin');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}

const ADMIN_USERNAME = 'admingoona';

$STATUS_PESANAN = [
    'baru'       => 'Baru',
    'diproses'   => 'Diproses',
    'selesai'    => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
];
$KATEGORI_ADMIN = [
    'coffee'    => 'Coffee Series',
    'tea'       => 'Tea Series',
    'noncoffee' => 'Non Coffee Series',
    'ayam'      => 'Paket Ayam',
    'snacks'    => 'Snacks',
    'alacarte'  => 'Ala Carte',
];

function rp($n) { return 'Rp' . number_format((int)$n, 0, ',', '.'); }

function waktu($ts) {
    $t = strtotime($ts);
    return $t ? date('d M Y, H.i', $t) : '-';
}

function admin_login_dibutuhkan() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));
    return $_SESSION['csrf'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
/** Hentikan permintaan POST yang tokennya tidak cocok. */
function csrf_wajib() {
    $kirim = $_POST['csrf'] ?? '';
    if (!is_string($kirim) || !hash_equals($_SESSION['csrf'] ?? '', $kirim)) {
        http_response_code(400);
        exit('Sesi formulir kedaluwarsa. Kembali ke halaman sebelumnya, muat ulang, lalu coba lagi.');
    }
}

function flash($pesan = null, $jenis = 'ok') {
    if ($pesan !== null) { $_SESSION['flash'] = [$jenis, $pesan]; return null; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** Koneksi database, atau halaman penjelasan kalau MySQL/tabel belum siap. */
function admin_db() {
    $pdo = db();
    $siap = false;
    if ($pdo) {
        try { $pdo->query('SELECT 1 FROM admin LIMIT 1'); $siap = true; } catch (Throwable $ex) {}
    }
    if (!$siap) {
        admin_kepala('Database belum siap', '');
        echo '<div class="kartu"><h1>Database belum siap</h1><p>Nyalakan MySQL di XAMPP Control Panel, lalu buka '
           . '<a href="../install.php">install.php</a> sekali untuk membuat tabel.</p></div>';
        admin_kaki();
        exit;
    }
    return $pdo;
}

function wa_link($hp) {
    $d = preg_replace('/\D+/', '', (string)$hp);
    if ($d === '') return '';
    if ($d[0] === '0') $d = '62' . substr($d, 1);
    return 'https://wa.me/' . $d;
}

function admin_kepala($judul, $aktif = '') {
    global $pdo_badge;
    $menu = ['index.php' => 'Ringkasan', 'pesanan.php' => 'Pesanan', 'pesan.php' => 'Pesan Contact', 'produk.php' => 'Produk'];
    $baru = 0; $belum = 0;
    if ($aktif !== '' && ($p = db())) {
        try {
            $baru  = (int)$p->query("SELECT COUNT(*) FROM pesanan WHERE status='baru'")->fetchColumn();
            $belum = (int)$p->query('SELECT COUNT(*) FROM pesan_kontak WHERE dibaca=0')->fetchColumn();
        } catch (Throwable $ex) {}
    }
    ?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?php echo e($judul); ?> - Admin Goona Cafe</title>
<link rel="icon" type="image/png" href="../assets/img/logo.png">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php if ($aktif !== ''): ?>
<header class="atas">
  <div class="isi atas-baris">
    <a href="index.php" class="merek"><img src="../assets/img/logo.png" alt="Goona Cafe" height="34"><span>Admin</span></a>
    <nav aria-label="Menu admin">
      <?php foreach ($menu as $f => $l): ?>
        <a href="<?php echo $f; ?>" class="<?php echo $aktif === $f ? 'on' : ''; ?>"<?php echo $aktif === $f ? ' aria-current="page"' : ''; ?>>
          <?php echo e($l); ?>
          <?php if ($f === 'pesanan.php' && $baru): ?><span class="lencana"><?php echo $baru; ?></span><?php endif; ?>
          <?php if ($f === 'pesan.php' && $belum): ?><span class="lencana"><?php echo $belum; ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="kanan">
      <a href="../index.php" target="_blank" rel="noopener">Lihat website</a>
      <form method="post" action="logout.php"><?php echo csrf_field(); ?><button type="submit" class="tombol kecil garis">Keluar</button></form>
    </div>
  </div>
</header>
<?php endif; ?>
<main class="isi">
<?php
    if ($f = flash()) {
        echo '<div class="notif ' . ($f[0] === 'err' ? 'err' : 'ok') . '" role="status">' . e($f[1]) . '</div>';
    }
}

function admin_kaki() {
    echo "\n</main>\n</body>\n</html>";
}

/** Hapus file foto produk kalau berada di assets/img/produk/ dan tidak dipakai produk lain. */
function hapus_foto_produk($pdo, $foto, $kecuali_id = 0) {
    if (strpos($foto, 'assets/img/produk/') !== 0 || strpos($foto, '..') !== false) return;
    $st = $pdo->prepare('SELECT COUNT(*) FROM produk WHERE foto = ? AND id <> ?');
    $st->execute([$foto, (int)$kecuali_id]);
    if ((int)$st->fetchColumn() > 0) return;
    $path = dirname(__DIR__) . '/' . $foto;
    if (is_file($path)) @unlink($path);
}
