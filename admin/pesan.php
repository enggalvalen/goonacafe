<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
admin_login_dibutuhkan();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $id = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'baca')        { $pdo->prepare('UPDATE pesan_kontak SET dibaca=1 WHERE id=?')->execute([$id]); flash('Ditandai sudah dibaca.'); }
    elseif ($aksi === 'belum')   { $pdo->prepare('UPDATE pesan_kontak SET dibaca=0 WHERE id=?')->execute([$id]); flash('Ditandai belum dibaca.'); }
    elseif ($aksi === 'hapus')   { $pdo->prepare('DELETE FROM pesan_kontak WHERE id=?')->execute([$id]); flash('Pesan dihapus.'); }
    elseif ($aksi === 'baca-semua') { $pdo->exec('UPDATE pesan_kontak SET dibaca=1 WHERE dibaca=0'); flash('Semua pesan ditandai sudah dibaca.'); }
    header('Location: pesan.php' . (!empty($_GET['filter']) ? '?filter=' . urlencode($_GET['filter']) : ''));
    exit;
}

$filter = ($_GET['filter'] ?? '') === 'belum' ? 'belum' : '';
$rows = $pdo->query('SELECT id, nama, email, pesan, dibaca, dibuat FROM pesan_kontak'
    . ($filter ? ' WHERE dibaca=0' : '') . ' ORDER BY id DESC LIMIT 200')->fetchAll();
$belum = (int)$pdo->query('SELECT COUNT(*) FROM pesan_kontak WHERE dibaca=0')->fetchColumn();

admin_kepala('Pesan Contact', 'pesan.php');
?>
<div class="judul-baris">
  <div><h1>Pesan Contact</h1><p class="muted"><?php echo $belum; ?> belum dibaca</p></div>
  <?php if ($belum): ?>
  <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="aksi" value="baca-semua"><button class="tombol garis kecil" type="submit">Tandai semua sudah dibaca</button></form>
  <?php endif; ?>
</div>
<div class="filter">
  <a class="chip <?php echo $filter === '' ? 'on' : ''; ?>" href="pesan.php">Semua</a>
  <a class="chip <?php echo $filter === 'belum' ? 'on' : ''; ?>" href="pesan.php?filter=belum">Belum dibaca (<?php echo $belum; ?>)</a>
</div>

<?php if (!$rows): ?>
  <div class="kartu kosong"><?php echo $filter ? 'Semua pesan sudah dibaca.' : 'Belum ada pesan. Pesan dari formulir Contact akan muncul di sini.'; ?></div>
<?php endif; ?>
<?php foreach ($rows as $m): ?>
  <article class="kartu" style="<?php echo $m['dibaca'] ? '' : 'border-left:4px solid var(--maroon)'; ?>">
    <div class="judul-baris" style="margin-bottom:8px">
      <div>
        <strong><?php echo e($m['nama']); ?></strong>
        <?php if (!$m['dibaca']): ?><span class="status s-baru" style="margin-left:6px">Baru</span><?php endif; ?>
        <div class="kecil muted"><a href="mailto:<?php echo e($m['email']); ?>"><?php echo e($m['email']); ?></a> &middot; <?php echo e(waktu($m['dibuat'])); ?></div>
      </div>
      <div class="baris-aksi">
        <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo (int)$m['id']; ?>">
          <input type="hidden" name="aksi" value="<?php echo $m['dibaca'] ? 'belum' : 'baca'; ?>">
          <button class="tombol garis kecil" type="submit"><?php echo $m['dibaca'] ? 'Tandai belum dibaca' : 'Tandai sudah dibaca'; ?></button></form>
        <form method="post" onsubmit="return confirm('Hapus pesan ini selamanya?');"><?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo (int)$m['id']; ?>">
          <input type="hidden" name="aksi" value="hapus"><button class="tombol bahaya kecil" type="submit">Hapus</button></form>
      </div>
    </div>
    <div class="pesan-teks"><?php echo e($m['pesan']); ?></div>
  </article>
<?php endforeach; ?>
<?php admin_kaki();
