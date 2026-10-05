<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
admin_login_dibutuhkan();

$q = fn($sql) => $pdo->query($sql)->fetchColumn();
$baru     = (int)$q("SELECT COUNT(*) FROM pesanan WHERE status='baru'");
$diproses = (int)$q("SELECT COUNT(*) FROM pesanan WHERE status='diproses'");
$hari_ini = (int)$q("SELECT COUNT(*) FROM pesanan WHERE DATE(dibuat)=CURDATE() AND status<>'dibatalkan'");
$omzet    = (int)$q("SELECT COALESCE(SUM(total),0) FROM pesanan WHERE DATE(dibuat)=CURDATE() AND status<>'dibatalkan'");
$belum    = (int)$q('SELECT COUNT(*) FROM pesan_kontak WHERE dibaca=0');
$produk   = (int)$q('SELECT COUNT(*) FROM produk WHERE aktif=1');
$habis    = (int)$q('SELECT COUNT(*) FROM produk WHERE aktif=0');
$terbaru  = $pdo->query('SELECT id, kode, nama, outlet, total, status, dibuat FROM pesanan ORDER BY id DESC LIMIT 8')->fetchAll();

admin_kepala('Ringkasan', 'index.php');
?>
<div class="judul-baris"><div><h1>Ringkasan</h1><p class="muted">Halo, <?php echo e(ADMIN_USERNAME); ?>.</p></div></div>

<div class="grid-ringkas">
  <a class="angka" href="pesanan.php?status=baru"><b><?php echo $baru; ?></b>Pesanan baru</a>
  <a class="angka" href="pesanan.php?status=diproses"><b><?php echo $diproses; ?></b>Sedang diproses</a>
  <a class="angka" href="pesanan.php"><b><?php echo rp($omzet); ?></b>Pesanan hari ini (<?php echo $hari_ini; ?>)</a>
  <a class="angka" href="pesan.php"><b><?php echo $belum; ?></b>Pesan Contact belum dibaca</a>
  <a class="angka" href="produk.php"><b><?php echo $produk; ?></b>Produk aktif<?php echo $habis ? ' (' . $habis . ' nonaktif)' : ''; ?></a>
</div>

<h2>Pesanan terbaru</h2>
<?php if (!$terbaru): ?>
  <div class="kartu kosong">Belum ada pesanan. Pesanan dari halaman Checkout akan muncul di sini.</div>
<?php else: ?>
<div class="gulir"><table>
  <thead><tr><th>Kode</th><th>Pemesan</th><th>Outlet</th><th class="angka-kanan">Total</th><th>Status</th><th>Waktu</th></tr></thead>
  <tbody>
  <?php foreach ($terbaru as $o): ?>
    <tr>
      <td><a href="pesanan-detail.php?id=<?php echo (int)$o['id']; ?>"><?php echo e($o['kode']); ?></a></td>
      <td><?php echo e($o['nama']); ?></td>
      <td><?php echo e($o['outlet']); ?></td>
      <td class="angka-kanan"><?php echo rp($o['total']); ?></td>
      <td><span class="status s-<?php echo e($o['status']); ?>"><?php echo e($STATUS_PESANAN[$o['status']] ?? $o['status']); ?></span></td>
      <td><?php echo e(waktu($o['dibuat'])); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php admin_kaki();
