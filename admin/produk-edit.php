<?php
require_once __DIR__ . '/../includes/admin-auth.php';
$pdo = admin_db();
admin_login_dibutuhkan();

const MAKS_FOTO = 3 * 1024 * 1024;
const SLOT_VARIAN = 3;

$id = (int)($_GET['id'] ?? 0);
$p = ['nama' => '', 'deskripsi' => '', 'harga' => 0, 'kategori' => 'coffee', 'foto' => '', 'favorit' => 0, 'aktif' => 1, 'urutan' => 0];
$vs = [];
if ($id) {
    $st = $pdo->prepare('SELECT * FROM produk WHERE id = ?');
    $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) { flash('Produk tidak ditemukan.', 'err'); header('Location: produk.php'); exit; }
    $st = $pdo->prepare('SELECT id, nama, harga FROM produk_varian WHERE produk_id = ? ORDER BY urutan, id');
    $st->execute([$id]);
    $vs = $st->fetchAll();
} else {
    $p['urutan'] = (int)$pdo->query('SELECT COALESCE(MAX(urutan),0)+1 FROM produk')->fetchColumn();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $in = [
        'nama'      => trim((string)($_POST['nama'] ?? '')),
        'deskripsi' => trim((string)($_POST['deskripsi'] ?? '')),
        'harga'     => trim((string)($_POST['harga'] ?? '')),
        'kategori'  => (string)($_POST['kategori'] ?? ''),
        'urutan'    => (int)($_POST['urutan'] ?? 0),
        'favorit'   => !empty($_POST['favorit']) ? 1 : 0,
        'aktif'     => !empty($_POST['aktif']) ? 1 : 0,
    ];
    if ($in['nama'] === '' || strlen($in['nama']) > 100) $errors['nama'] = 'Isi nama produk (maksimal 100 karakter).';
    if (!isset($KATEGORI_ADMIN[$in['kategori']]))        $errors['kategori'] = 'Pilih kategori.';

    // Varian: baris yang nama dan harganya kosong dilewati; kalau salah satu terisi, keduanya wajib.
    $vin = [];
    for ($i = 0; $i < SLOT_VARIAN; $i++) {
        $vid = (int)($_POST['v_id'][$i] ?? 0);
        $vn  = trim((string)($_POST['v_nama'][$i] ?? ''));
        $vh  = trim((string)($_POST['v_harga'][$i] ?? ''));
        if ($vn === '' && $vh === '') continue;
        if ($vn === '' || strlen($vn) > 40 || !ctype_digit($vh)) { $errors['varian'] = 'Varian harus punya nama (maks. 40 karakter) dan harga berupa angka.'; break; }
        $vin[] = ['id' => $vid, 'nama' => $vn, 'harga' => (int)$vh];
    }
    if ($vin) {
        $in['harga'] = (string)min(array_column($vin, 'harga'));
    } elseif (!ctype_digit($in['harga'])) {
        $errors['harga'] = 'Isi harga dengan angka, tanpa titik atau Rp.';
    }

    // Foto (opsional)
    $foto_baru = '';
    $f = $_FILES['foto'] ?? null;
    if ($f && $f['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > MAKS_FOTO) {
            $errors['foto'] = 'Foto gagal diunggah. Ukuran maksimal 3 MB.';
        } else {
            $info = @getimagesize($f['tmp_name']);
            $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'][$info[2] ?? 0] ?? '';
            if ($ext === '') $errors['foto'] = 'Format foto harus JPG, PNG, atau WEBP.';
            else $foto_baru = ['tmp' => $f['tmp_name'], 'ext' => $ext];
        }
    }

    if (!$errors) {
        $foto = $p['foto'];
        if ($foto_baru) {
            $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($in['nama'])), '-') ?: 'produk';
            $nama_file = $slug . '-' . bin2hex(random_bytes(3)) . '.' . $foto_baru['ext'];
            $tujuan = dirname(__DIR__) . '/assets/img/produk/' . $nama_file;
            if (move_uploaded_file($foto_baru['tmp'], $tujuan)) {
                $foto = 'assets/img/produk/' . $nama_file;
            } else {
                $errors['foto'] = 'Foto tidak bisa disimpan. Pastikan folder assets/img/produk bisa ditulis.';
            }
        }
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            if ($id) {
                $pdo->prepare('UPDATE produk SET nama=?, deskripsi=?, harga=?, kategori=?, foto=?, favorit=?, aktif=?, urutan=? WHERE id=?')
                    ->execute([$in['nama'], $in['deskripsi'], (int)$in['harga'], $in['kategori'], $foto, $in['favorit'], $in['aktif'], $in['urutan'], $id]);
            } else {
                $pdo->prepare('INSERT INTO produk (nama, deskripsi, harga, kategori, foto, favorit, aktif, urutan) VALUES (?,?,?,?,?,?,?,?)')
                    ->execute([$in['nama'], $in['deskripsi'], (int)$in['harga'], $in['kategori'], $foto, $in['favorit'], $in['aktif'], $in['urutan']]);
                $id = (int)$pdo->lastInsertId();
            }
            // Sinkronkan varian: id yang sama diperbarui (supaya keranjang pelanggan tetap valid), sisanya dihapus.
            $ada = [];
            $st = $pdo->prepare('SELECT id FROM produk_varian WHERE produk_id = ?'); $st->execute([$id]);
            foreach ($st->fetchAll() as $r) $ada[(int)$r['id']] = true;
            $dipakai = [];
            foreach ($vin as $urut => $v) {
                if ($v['id'] && isset($ada[$v['id']])) {
                    $pdo->prepare('UPDATE produk_varian SET nama=?, harga=?, urutan=? WHERE id=? AND produk_id=?')->execute([$v['nama'], $v['harga'], $urut + 1, $v['id'], $id]);
                    $dipakai[$v['id']] = true;
                } else {
                    $pdo->prepare('INSERT INTO produk_varian (produk_id, nama, harga, urutan) VALUES (?,?,?,?)')->execute([$id, $v['nama'], $v['harga'], $urut + 1]);
                }
            }
            foreach (array_keys($ada) as $vid) {
                if (!isset($dipakai[$vid])) $pdo->prepare('DELETE FROM produk_varian WHERE id=? AND produk_id=?')->execute([$vid, $id]);
            }
            $pdo->commit();
            if ($foto_baru && $p['foto'] !== '' && $p['foto'] !== $foto) hapus_foto_produk($pdo, $p['foto'], $id);
            flash($in['nama'] . ' disimpan.');
            header('Location: produk.php');
            exit;
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors['umum'] = 'Produk belum bisa disimpan. Coba lagi.';
        }
    }
    // tampilkan ulang isian yang sudah diketik
    $p = array_merge($p, $in);
    $vs = [];
    foreach ($vin as $v) $vs[] = $v;
}

$slot = [];
for ($i = 0; $i < SLOT_VARIAN; $i++) $slot[$i] = $vs[$i] ?? ['id' => 0, 'nama' => '', 'harga' => ''];

admin_kepala($id ? 'Edit produk' : 'Tambah produk', 'produk.php');
?>
<p class="kecil"><a href="produk.php">&larr; Semua produk</a></p>
<div class="judul-baris"><h1><?php echo $id ? 'Edit ' . e($p['nama']) : 'Tambah produk'; ?></h1></div>
<?php foreach ($errors as $m): ?><div class="notif err" role="alert"><?php echo e($m); ?></div><?php endforeach; ?>

<form method="post" enctype="multipart/form-data" class="kartu">
  <?php echo csrf_field(); ?>
  <div class="dua-kolom">
    <div>
      <label for="nama">Nama produk</label>
      <input type="text" id="nama" name="nama" value="<?php echo e($p['nama']); ?>" maxlength="100" required>

      <label for="kategori">Kategori</label>
      <select id="kategori" name="kategori">
        <?php foreach ($KATEGORI_ADMIN as $k => $l): ?>
          <option value="<?php echo $k; ?>"<?php echo $p['kategori'] === $k ? ' selected' : ''; ?>><?php echo e($l); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="deskripsi">Deskripsi</label>
      <textarea id="deskripsi" name="deskripsi"><?php echo e($p['deskripsi']); ?></textarea>

      <label for="harga">Harga (Rp)</label>
      <input type="number" id="harga" name="harga" min="0" step="500" value="<?php echo e($p['harga']); ?>">
      <p class="kecil muted">Kalau produk punya varian di bawah, harga dasar ini diabaikan dan website memakai harga varian termurah.</p>
    </div>
    <div>
      <label>Foto</label>
      <?php if ($p['foto'] !== ''): ?><img class="thumb" style="width:140px;height:140px" src="../<?php echo e($p['foto']); ?>" alt="Foto saat ini"><?php endif; ?>
      <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" aria-label="Unggah foto baru" style="margin-top:8px">
      <p class="kecil muted">JPG, PNG, atau WEBP, maksimal 3 MB. Foto persegi paling pas. Kosongkan kalau tidak ingin mengganti.</p>

      <label>Varian (opsional, mis. Hot dan Ice)</label>
      <?php foreach ($slot as $i => $v): ?>
        <div class="varian-baris">
          <input type="hidden" name="v_id[<?php echo $i; ?>]" value="<?php echo (int)$v['id']; ?>">
          <input type="text" name="v_nama[<?php echo $i; ?>]" value="<?php echo e($v['nama']); ?>" placeholder="Nama varian" maxlength="40" aria-label="Nama varian <?php echo $i + 1; ?>">
          <input type="number" name="v_harga[<?php echo $i; ?>]" value="<?php echo e($v['harga']); ?>" placeholder="Harga" min="0" step="500" aria-label="Harga varian <?php echo $i + 1; ?>">
        </div>
      <?php endforeach; ?>
      <p class="kecil muted">Kosongkan nama dan harga untuk menghapus varian.</p>

      <label for="urutan">Urutan tampil</label>
      <input type="number" id="urutan" name="urutan" value="<?php echo (int)$p['urutan']; ?>">

      <label class="cek"><input type="checkbox" name="aktif" value="1"<?php echo $p['aktif'] ? ' checked' : ''; ?>> Tersedia (tampil di website)</label>
      <label class="cek"><input type="checkbox" name="favorit" value="1"<?php echo $p['favorit'] ? ' checked' : ''; ?>> Tampilkan di Menu Favorit Home</label>
    </div>
  </div>
  <div class="baris-aksi" style="margin-top:18px">
    <button class="tombol" type="submit">Simpan produk</button>
    <a class="tombol garis" href="produk.php">Batal</a>
  </div>
</form>

<?php if ($id): ?>
<form method="post" action="produk.php" onsubmit="return confirm('Hapus produk ini selamanya? Pesanan lama tetap menyimpan namanya. Untuk menyembunyikan sementara, gunakan Nonaktifkan.');">
  <?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="aksi" value="hapus">
  <button class="tombol bahaya kecil" type="submit">Hapus produk</button>
</form>
<?php endif; ?>
<?php admin_kaki();
