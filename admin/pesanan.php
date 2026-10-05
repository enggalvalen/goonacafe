<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
admin_login_dibutuhkan();

$status = $_GET['status'] ?? '';
if (!isset($STATUS_PESANAN[$status])) $status = '';
$cari  = trim((string)($_GET['q'] ?? ''));
$hal   = max(1, (int)($_GET['hal'] ?? 1));
$per   = 25;

$where = []; $par = [];
if ($status !== '') { $where[] = 'status = ?'; $par[] = $status; }
if ($cari !== '')   { $where[] = '(kode LIKE ? OR nama LIKE ? OR hp LIKE ?)'; $like = '%' . $cari . '%'; array_push($par, $like, $like, $like); }
$w = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$st = $pdo->prepare('SELECT COUNT(*) FROM pesanan' . $w); $st->execute($par);
$total = (int)$st->fetchColumn();
$hal_maks = max(1, (int)ceil($total / $per));
$hal = min($hal, $hal_maks);

$st = $pdo->prepare('SELECT id, kode, nama, hp, outlet, pengambilan, metode, total, status, dibuat FROM pesanan' . $w
    . ' ORDER BY id DESC LIMIT ' . $per . ' OFFSET ' . (($hal - 1) * $per));
$st->execute($par);
$rows = $st->fetchAll();

$hitung = [];
foreach ($pdo->query('SELECT status, COUNT(*) n FROM pesanan GROUP BY status') as $r) $hitung[$r['status']] = (int)$r['n'];

function url_pesanan($ubah) {
    $q = array_merge(['status' => $_GET['status'] ?? '', 'q' => $_GET['q'] ?? '', 'hal' => 1], $ubah);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== null && $v !== 1);
    return 'pesanan.php' . ($q ? '?' . http_build_query($q) : '');
}

admin_kepala('Pesanan', 'pesanan.php');
?>
<div class="judul-baris"><div><h1>Pesanan</h1><p class="muted"><?php echo $total; ?> pesanan<?php echo ($status || $cari) ? ' sesuai filter' : ''; ?></p></div></div>

<div class="filter">
  <a class="chip <?php echo $status === '' ? 'on' : ''; ?>" href="<?php echo e(url_pesanan(['status' => ''])); ?>">Semua</a>
  <?php foreach ($STATUS_PESANAN as $k => $l): ?>
    <a class="chip <?php echo $status === $k ? 'on' : ''; ?>" href="<?php echo e(url_pesanan(['status' => $k])); ?>"><?php echo e($l); ?> (<?php echo $hitung[$k] ?? 0; ?>)</a>
  <?php endforeach; ?>
  <form method="get" action="pesanan.php">
    <?php if ($status): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
    <input type="search" name="q" value="<?php echo e($cari); ?>" placeholder="Cari kode, nama, atau HP" aria-label="Cari pesanan">
    <button class="tombol kecil" type="submit">Cari</button>
  </form>
</div>

<?php if (!$rows): ?>
  <div class="kartu kosong"><?php echo ($status || $cari) ? 'Tidak ada pesanan yang cocok. Ubah filter atau kata kunci.' : 'Belum ada pesanan.'; ?></div>
<?php else: ?>
<div class="gulir"><table>
  <thead><tr><th>Kode</th><th>Pemesan</th><th>Outlet</th><th>Bayar</th><th class="angka-kanan">Total</th><th>Status</th><th>Waktu</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $o): ?>
    <tr class="<?php echo $o['status'] === 'baru' ? 'belum' : ''; ?>">
      <td><a href="pesanan-detail.php?id=<?php echo (int)$o['id']; ?>"><?php echo e($o['kode']); ?></a></td>
      <td><?php echo e($o['nama']); ?><br><span class="muted kecil"><?php echo e($o['hp']); ?></span></td>
      <td><?php echo e($o['outlet']); ?><br><span class="muted kecil"><?php echo e($o['pengambilan']); ?></span></td>
      <td><?php echo e($pembayaran[$o['metode']]['label'] ?? $o['metode']); ?></td>
      <td class="angka-kanan"><?php echo rp($o['total']); ?></td>
      <td><span class="status s-<?php echo e($o['status']); ?>"><?php echo e($STATUS_PESANAN[$o['status']]); ?></span></td>
      <td><?php echo e(waktu($o['dibuat'])); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php if ($hal_maks > 1): ?>
<nav class="halaman" aria-label="Halaman">
  <?php for ($i = 1; $i <= $hal_maks; $i++): ?>
    <?php if ($i === $hal): ?><span aria-current="page"><?php echo $i; ?></span>
    <?php else: ?><a href="<?php echo e(url_pesanan(['hal' => $i])); ?>"><?php echo $i; ?></a><?php endif; ?>
  <?php endfor; ?>
</nav>
<?php endif; endif; ?>
<?php admin_kaki();
