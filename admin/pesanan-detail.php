<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
admin_login_dibutuhkan();

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM pesanan WHERE id = ?');
$st->execute([$id]);
$o = $st->fetch();
if (!$o) {
    http_response_code(404);
    flash('Pesanan tidak ditemukan.', 'err');
    header('Location: pesanan.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'status') {
        $baru = $_POST['status'] ?? '';
        if (isset($STATUS_PESANAN[$baru])) {
            $pdo->prepare('UPDATE pesanan SET status = ? WHERE id = ?')->execute([$baru, $id]);
            flash('Status pesanan ' . $o['kode'] . ' diubah menjadi ' . $STATUS_PESANAN[$baru] . '.');
        } else {
            flash('Status tidak dikenal.', 'err');
        }
    } elseif ($aksi === 'hapus') {
        $pdo->prepare('DELETE FROM pesanan WHERE id = ?')->execute([$id]);
        flash('Pesanan ' . $o['kode'] . ' dihapus.');
        header('Location: pesanan.php');
        exit;
    }
    header('Location: pesanan-detail.php?id=' . $id);
    exit;
}

$st = $pdo->prepare('SELECT nama_produk, varian, harga, qty, catatan FROM pesanan_item WHERE pesanan_id = ? ORDER BY id');
$st->execute([$id]);
$items = $st->fetchAll();
$wa = wa_link($o['hp']);

admin_kepala('Pesanan ' . $o['kode'], 'pesanan.php');
?>
<p class="kecil"><a href="pesanan.php">&larr; Semua pesanan</a></p>
<div class="judul-baris">
  <div><h1><?php echo e($o['kode']); ?></h1><p class="muted"><?php echo e(waktu($o['dibuat'])); ?></p></div>
  <span class="status s-<?php echo e($o['status']); ?>"><?php echo e($STATUS_PESANAN[$o['status']]); ?></span>
</div>

<div class="dua-kolom">
  <div class="kartu">
    <h2>Pemesan</h2>
    <dl class="dl">
      <dt>Nama</dt><dd><?php echo e($o['nama']); ?></dd>
      <dt>HP</dt><dd><?php echo e($o['hp']); ?><?php if ($wa): ?> &middot; <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener">Chat WhatsApp</a><?php endif; ?></dd>
      <dt>Outlet</dt><dd><?php echo e($o['outlet']); ?></dd>
      <dt>Pengambilan</dt><dd><?php echo e($o['pengambilan']); ?></dd>
      <dt>Pembayaran</dt><dd><?php echo e($pembayaran[$o['metode']]['label'] ?? $o['metode']); ?></dd>
      <dt>Catatan</dt><dd class="pesan-teks"><?php echo $o['catatan'] !== '' ? e($o['catatan']) : '<span class="muted">-</span>'; ?></dd>
    </dl>
  </div>
  <div class="kartu">
    <h2>Ubah status</h2>
    <form method="post">
      <?php echo csrf_field(); ?><input type="hidden" name="aksi" value="status">
      <label for="st">Status pesanan</label>
      <select id="st" name="status">
        <?php foreach ($STATUS_PESANAN as $k => $l): ?>
          <option value="<?php echo $k; ?>"<?php echo $o['status'] === $k ? ' selected' : ''; ?>><?php echo e($l); ?></option>
        <?php endforeach; ?>
      </select>
      <p style="margin-top:14px"><button class="tombol" type="submit">Simpan status</button></p>
    </form>
  </div>
</div>

<h2>Item pesanan</h2>
<div class="gulir"><table>
  <thead><tr><th>Item</th><th class="angka-kanan">Harga</th><th class="angka-kanan">Jumlah</th><th class="angka-kanan">Subtotal</th></tr></thead>
  <tbody>
  <?php foreach ($items as $it): ?>
    <tr>
      <td><?php echo e($it['nama_produk'] . ($it['varian'] !== '' ? ' (' . $it['varian'] . ')' : '')); ?>
        <?php if ($it['catatan'] !== ''): ?><br><span class="muted kecil">Catatan: <?php echo e($it['catatan']); ?></span><?php endif; ?></td>
      <td class="angka-kanan"><?php echo rp($it['harga']); ?></td>
      <td class="angka-kanan"><?php echo (int)$it['qty']; ?></td>
      <td class="angka-kanan"><?php echo rp($it['harga'] * $it['qty']); ?></td>
    </tr>
  <?php endforeach; ?>
    <tr><td colspan="3" class="angka-kanan"><strong>Total</strong></td><td class="angka-kanan"><strong><?php echo rp($o['total']); ?></strong></td></tr>
  </tbody>
</table></div>

<form method="post" style="margin-top:22px" onsubmit="return confirm('Hapus pesanan ini selamanya? Tindakan ini tidak bisa dibatalkan.');">
  <?php echo csrf_field(); ?><input type="hidden" name="aksi" value="hapus">
  <button class="tombol kecil bahaya" type="submit">Hapus pesanan</button>
</form>
<?php admin_kaki();
