<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

$error = '';
$terkunci = max(0, ($_SESSION['kunci_sampai'] ?? 0) - time());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    if ($terkunci > 0) {
        $error = 'Terlalu banyak percobaan. Tunggu ' . $terkunci . ' detik lalu coba lagi.';
    } else {
        $u = trim((string)($_POST['username'] ?? ''));
        $p = (string)($_POST['password'] ?? '');
        $st = $pdo->prepare('SELECT id, password_hash FROM admin WHERE username = ?');
        $st->execute([$u]);
        $row = $st->fetch();
        // Selalu jalankan password_verify supaya waktu respons sama, ada atau tidaknya username.
        $hash = $row['password_hash'] ?? '$2y$10$xIZ4wk2lcvR90mywhD9H7OHxuxBwFXycILbN86YimiHgDyQXxwvjy';
        if (password_verify($p, $hash) && $row) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$row['id'];
            unset($_SESSION['gagal'], $_SESSION['kunci_sampai']);
            header('Location: index.php');
            exit;
        }
        $_SESSION['gagal'] = ($_SESSION['gagal'] ?? 0) + 1;
        if ($_SESSION['gagal'] >= 5) {
            $_SESSION['kunci_sampai'] = time() + 60;
            $_SESSION['gagal'] = 0;
            $error = 'Terlalu banyak percobaan. Tunggu 60 detik lalu coba lagi.';
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
admin_kepala('Masuk', '');
?>
<div class="kartu sempit">
  <p style="text-align:center"><img src="../assets/img/logo.png" alt="Goona Cafe" height="48" style="display:inline-block"></p>
  <h1 style="text-align:center">Masuk admin</h1>
  <?php if (!empty($_GET['setup'])): ?><div class="notif ok">Akun admin sudah dibuat. Silakan masuk.</div><?php endif; ?>
  <?php if ($error): ?><div class="notif err" role="alert"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post" autocomplete="on">
    <?php echo csrf_field(); ?>
    <label for="u">Username</label>
    <input type="text" id="u" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus autocomplete="username">
    <label for="p">Password</label>
    <input type="password" id="p" name="password" required autocomplete="current-password">
    <p style="margin-top:18px"><button class="tombol" type="submit" style="width:100%">Masuk</button></p>
  </form>
</div>
<?php admin_kaki();
