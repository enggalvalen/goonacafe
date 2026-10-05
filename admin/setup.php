<?php
/**
 * Pembuatan akun admin pertama (sekali pakai).
 * Username tetap `admingoona`; password diisi sendiri di sini dan disimpan sebagai hash.
 * XAMPP: langsung buka. Hosting: tambahkan ?kode=SETUP_KODE di alamatnya.
 * Setelah akun ada, halaman ini menolak semua permintaan.
 */
require_once __DIR__ . '/../includes/admin-auth.php';
require_once __DIR__ . '/../includes/setup-guard.php';
$pdo = admin_db();

if (!setup_boleh()) {
    http_response_code(403);
    admin_kepala('Tidak diizinkan', '');
    echo '<div class="kartu sempit"><h1>Tidak diizinkan</h1><p>Akun admin hanya bisa dibuat dari komputer lokal, atau dari hosting dengan kode rahasia (<code>setup.php?kode=...</code>, sesuai <code>SETUP_KODE</code> di <code>includes/db-local.php</code>).</p></div>';
    admin_kaki(); exit;
}
if ((int)$pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn() > 0) {
    admin_kepala('Admin sudah ada', '');
    echo '<div class="kartu sempit"><h1>Akun admin sudah dibuat</h1><p>Halaman ini tidak bisa dipakai lagi. '
       . 'Kalau lupa password, ikuti petunjuk di README.</p><p><a class="tombol" href="login.php">Ke halaman masuk</a></p></div>';
    admin_kaki(); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $p1 = (string)($_POST['password'] ?? '');
    $p2 = (string)($_POST['ulang'] ?? '');
    if (strlen($p1) < 8)          $error = 'Password minimal 8 karakter.';
    elseif ($p1 !== $p2)          $error = 'Kedua password tidak sama.';
    else {
        try {
            $pdo->prepare('INSERT INTO admin (username, password_hash) VALUES (?, ?)')
                ->execute([ADMIN_USERNAME, password_hash($p1, PASSWORD_DEFAULT)]);
            header('Location: login.php?setup=1');
            exit;
        } catch (Throwable $ex) {
            $error = 'Akun belum bisa dibuat. Coba muat ulang halaman ini.';
        }
    }
}
admin_kepala('Buat akun admin', '');
?>
<div class="kartu sempit">
  <h1>Buat akun admin</h1>
  <p class="muted">Username: <strong><?php echo e(ADMIN_USERNAME); ?></strong></p>
  <?php if ($error): ?><div class="notif err" role="alert"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <?php echo csrf_field(); ?>
    <label for="p1">Password (minimal 8 karakter)</label>
    <input type="password" id="p1" name="password" required minlength="8" autocomplete="new-password" autofocus>
    <label for="p2">Ulangi password</label>
    <input type="password" id="p2" name="ulang" required minlength="8" autocomplete="new-password">
    <p style="margin-top:18px"><button class="tombol" type="submit" style="width:100%">Buat akun</button></p>
  </form>
</div>
<?php admin_kaki();
