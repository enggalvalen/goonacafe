<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
admin_login_dibutuhkan();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';
    $st = $pdo->prepare('SELECT nama, foto FROM produk WHERE id = ?');
    $st->execute([$id]);
    $p = $st->fetch();
    if ($p) {
        if ($aksi === 'aktif') {
            $pdo->prepare('UPDATE produk SET aktif = 1 - aktif WHERE id = ?')->execute([$id]);
            flash($p['nama'] . ' diubah ketersediaannya.');
        } elseif ($aksi === 'favorit') {
            $pdo->prepare('UPDATE produk SET favorit = 1 - favorit WHERE id = ?')->execute([$id]);
            flash('Menu favorit di Home diperbarui untuk ' . $p['nama'] . '.');
        } elseif ($aksi === 'hapus') {
            $pdo->prepare('DELETE FROM produk WHERE id = ?')->execute([$id]);
            hapus_foto_produk($pdo, $p['foto']);
            flash($p['nama'] . ' dihapus. Pesanan lama tetap menyimpan namanya.');
        }
    } else {
        flash('Produk tidak ditemukan.', 'err');
    }
    header('Location: produk.php' . (!empty($_GET['kat']) ? '?kat=' . urlencode($_GET['kat']) : ''));
    exit;
}

$kat = $_GET['kat'] ?? '';
if (!isset($KATEGORI_ADMIN[$kat])) $kat = '';
$st = $pdo->prepare('SELECT id, nama, harga, kategori, foto, favorit, aktif FROM produk' . ($kat ? ' WHERE kategori = ?' : '') . ' ORDER BY urutan, id');
$st->execute($kat ? [$kat] : []);
$rows = $st->fetchAll();
$varian = [];
foreach ($pdo->query('SELECT produk_id, nama, harga FROM produk_varian ORDER BY urutan, id') as $v) $varian[(int)$v['produk_id']][] = $v;
$jumlah = (int)$pdo->query('SELECT COUNT(*) FROM produk')->fetchColumn();

admin_kepala('Produk', 'produk.php');
?>
<div class="judul-baris">
  <div><h1>Produk</h1><p class="muted"><?php echo $jumlah; ?> produk &middot; urutan tampil mengikuti kolom Urutan di halaman edit</p></div>
  <a class="tombol" href="produk-edit.php">Tambah produk</a>
</div>
<div class="filter">
  <a class="chip <?php echo $kat === '' ? 'on' : ''; ?>" href="produk.php">Semua</a>
  <?php foreach ($KATEGORI_ADMIN as $k => $l): ?>
    <a class="chip <?php echo $kat === $k ? 'on' : ''; ?>" href="produk.php?kat=<?php echo $k; ?>"><?php echo e($l); ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$rows): ?>
  <div class="kartu kosong">Belum ada produk di kategori ini.</div>
<?php else: ?>
<div class="gulir"><table>
  <thead><tr><th></th><th>Nama</th><th>Kategori</th><th class="angka-kanan">Harga</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $p): $vs = $varian[(int)$p['id']] ?? []; ?>
    <tr>
      <td><?php if ($p['foto'] !== ''): ?><img class="thumb" src="../<?php echo e($p['foto']); ?>" alt="" loading="lazy"><?php else: ?><span class="thumb"></span><?php endif; ?></td>
      <td><a href="produk-edit.php?id=<?php echo (int)$p['id']; ?>"><?php echo e($p['nama']); ?></a>
        <?php if ($p['favorit']): ?><br><span class="muted kecil">Favorit di Home</span><?php endif; ?></td>
      <td><?php echo e($KATEGORI_ADMIN[$p['kategori']] ?? $p['kategori']); ?></td>
      <td class="angka-kanan"><?php if ($vs) { foreach ($vs as $v) { echo e($v['nama']) . ' ' . rp($v['harga']) . '<br>'; } } else { echo rp($p['harga']); } ?></td>
      <td><span class="status <?php echo $p['aktif'] ? 's-aktif' : 's-habis'; ?>"><?php echo $p['aktif'] ? 'Tersedia' : 'Nonaktif'; ?></span></td>
      <td><div class="baris-aksi">
        <a class="tombol garis kecil" href="produk-edit.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
        <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>"><input type="hidden" name="aksi" value="aktif">
          <button class="tombol garis kecil" type="submit"><?php echo $p['aktif'] ? 'Nonaktifkan' : 'Aktifkan'; ?></button></form>
      </div></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php admin_kaki();
