<?php
require_once 'includes/cart-functions.php';

$selesai = ($_GET['status'] ?? '') === 'selesai' && !empty($_SESSION['last_order']);
$items   = cart_items();
$metode  = metode_pembayaran_tersedia();

// Keranjang kosong dan bukan halaman sukses -> kembali ke keranjang
if (!$selesai && !$items) {
    header('Location: cart.php');
    exit;
}

$errors = [];
$v = ['nama' => '', 'hp' => '', 'outlet' => '', 'pengambilan' => 'Dine In', 'metode' => (string)array_key_first($metode), 'catatan' => ''];

if (!$selesai && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nama']        = trim($_POST['nama'] ?? '');
    $v['hp']          = trim($_POST['hp'] ?? '');
    $v['outlet']      = $_POST['outlet'] ?? '';
    $v['pengambilan'] = $_POST['pengambilan'] ?? '';
    $v['metode']      = $_POST['metode'] ?? '';
    $v['catatan']     = potong(trim($_POST['catatan'] ?? ''), 200);

    if ($v['nama'] === '')                                   $errors['nama'] = 'Isi nama lengkap.';
    if (!preg_match('/^[0-9+\-\s]{8,16}$/', $v['hp']))       $errors['hp'] = 'Isi nomor HP yang valid (8-16 angka).';
    if (!in_array($v['outlet'], $lokasi, true))              $errors['outlet'] = 'Pilih outlet.';
    if (!in_array($v['pengambilan'], ['Dine In', 'Take Away'], true)) $errors['pengambilan'] = 'Pilih Dine In atau Take Away.';

    if (!isset($metode[$v['metode']]))                       $errors['metode'] = 'Pilih metode pembayaran.';

    if (!$errors) {
        $pdo = db();
        $kode = '';
        $total = cart_total();
        if ($pdo) {
            try {
                $pdo->beginTransaction();
                for ($i = 0; $i < 6 && $kode === ''; $i++) {
                    $coba = 'GC' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
                    $cek = $pdo->prepare('SELECT 1 FROM pesanan WHERE kode = ?');
                    $cek->execute([$coba]);
                    if (!$cek->fetchColumn()) $kode = $coba;
                }
                if ($kode === '') throw new RuntimeException('kode');
                $pdo->prepare('INSERT INTO pesanan (kode, nama, hp, outlet, pengambilan, metode, catatan, total) VALUES (?,?,?,?,?,?,?,?)')
                    ->execute([$kode, potong($v['nama'], 100), $v['hp'], $v['outlet'], $v['pengambilan'], $v['metode'], $v['catatan'], $total]);
                $pid = (int)$pdo->lastInsertId();
                $ins = $pdo->prepare('INSERT INTO pesanan_item (pesanan_id, produk_id, nama_produk, varian, harga, qty, catatan) VALUES (?,?,?,?,?,?,?)');
                foreach ($items as $it) {
                    $ins->execute([$pid, $it['p']['id'], $it['p']['name'], $it['varian']['nama'] ?? '', $it['harga'], $it['qty'], $it['note']]);
                }
                $pdo->commit();
            } catch (Throwable $ex) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $kode = '';
            }
        }
        if ($kode === '') {
            // Pesanan TIDAK tersimpan: keranjang dibiarkan utuh supaya pelanggan tidak kehilangan pilihannya.
            $errors['db'] = 'Pesanan belum bisa disimpan karena sistem sedang bermasalah. Keranjangmu masih aman, coba lagi sebentar lagi atau pesan lewat WhatsApp kami.';
        } else {
            $_SESSION['last_order'] = [
                'kode'        => $kode,
                'nama'        => $v['nama'],
                'hp'          => $v['hp'],
                'outlet'      => $v['outlet'],
                'pengambilan' => $v['pengambilan'],
                'catatan'     => $v['catatan'],
                'metode'      => $v['metode'],
                'items'       => array_map(fn($it) => ['name' => item_label($it), 'qty' => $it['qty'], 'sub' => $it['sub'], 'note' => $it['note']], $items),
                'total'       => $total,
            ];
            cart_clear();
            header('Location: checkout.php?status=selesai');
            exit;
        }
    }
}

$page_title = $selesai ? 'Pesanan Diterima' : 'Checkout';
require 'includes/header.php';

if ($selesai):
    $o = $_SESSION['last_order'];
?>
<section class="section" style="border-top:none;">
  <div class="wrap">
    <div class="panel success">
      <h1 class="h-page left">Pesanan diterima</h1>
      <p>Terima kasih, <?php echo e($o['nama']); ?>. Selesaikan pembayaran di bawah, lalu kirim bukti lewat WhatsApp agar pesananmu segera diproses.</p>
      <div class="sum-row"><span>Kode pesanan</span><strong><?php echo e($o['kode']); ?></strong></div>
      <div class="sum-row"><span>Outlet</span><strong><?php echo e($o['outlet']); ?></strong></div>
      <div class="sum-row"><span>Pengambilan</span><strong><?php echo e($o['pengambilan']); ?></strong></div>
      <?php if ($o['catatan'] !== ''): ?><div class="sum-row"><span>Catatan</span><strong><?php echo e($o['catatan']); ?></strong></div><?php endif; ?>
      <h2 class="h-small" style="margin-top:22px;">Pesananmu</h2>
      <?php foreach ($o['items'] as $it): ?>
        <div class="sum-row"><span><?php echo e($it['name']); ?> x<?php echo $it['qty']; ?><?php echo $it['note'] !== '' ? ' (' . e($it['note']) . ')' : ''; ?></span><strong><?php echo format_rupiah($it['sub']); ?></strong></div>
      <?php endforeach; ?>
      <div class="sum-row total"><span>Total</span><strong><?php echo format_rupiah($o['total']); ?></strong></div>

      <?php $m = $pembayaran[$o['metode']] ?? null; if ($m): ?>
      <div class="pay-box">
        <h2 class="h-small" style="margin-top:0;">Pembayaran: <?php echo e($m['label']); ?></h2>
        <?php if (isset($m['gambar'])): ?>
          <img class="qris" src="<?php echo e($m['gambar']); ?>" alt="QRIS Goona Cafe">
          <p>Scan QRIS ini dan bayar sebesar <strong><?php echo format_rupiah($o['total']); ?></strong>.</p>
        <?php elseif (isset($m['rekening'])): ?>
          <p>Transfer <strong><?php echo format_rupiah($o['total']); ?></strong> ke rekening berikut:</p>
          <div class="pay-num"><?php echo e($m['rekening']); ?></div>
          <p>a.n. <?php echo e($m['atas_nama']); ?></p>
        <?php else: ?>
          <p>Kirim <strong><?php echo format_rupiah($o['total']); ?></strong> ke nomor DANA berikut:</p>
          <div class="pay-num"><?php echo e($m['nomor']); ?></div>
        <?php endif; ?>
      </div>
      <?php
        $wa_teks = 'Halo Goona Cafe, saya sudah memesan dengan kode ' . $o['kode'] . ' a.n. ' . $o['nama']
                 . ', total ' . format_rupiah($o['total']) . ' via ' . $m['label'] . '. Berikut bukti pembayarannya.';
      ?>
      <div class="btn-row">
        <a class="btn" href="<?php echo e($kontak['whatsapp'] . '?text=' . rawurlencode($wa_teks)); ?>" target="_blank" rel="noopener">Kirim Bukti via WhatsApp</a>
        <a class="btn outline" href="produk.php">Lihat Menu</a>
        <a class="btn outline" href="index.php">Kembali ke Home</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php else: $total = cart_total(); ?>
<section class="section" style="border-top:none;">
  <div class="wrap">
    <h1 class="h-page left" style="margin-bottom:28px;">Checkout</h1>

    <?php if ($errors): ?>
      <p class="form-note error" role="alert"><?php echo isset($errors['db']) ? e($errors['db']) . ' <a href="' . e($kontak['whatsapp']) . '" target="_blank" rel="noopener"><u>Chat WhatsApp</u></a>' : 'Periksa lagi: ' . e(implode(' ', $errors)); ?></p>
    <?php endif; ?>

    <div class="shop-layout">
      <form class="panel" id="checkoutForm" method="post" action="checkout.php" novalidate>
        <h2 class="h-small">Data Pemesan</h2>
        <div class="row-2">
          <div class="field">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?php echo e($v['nama']); ?>" required>
          </div>
          <div class="field">
            <label for="hp">No. HP</label>
            <input type="tel" id="hp" name="hp" value="<?php echo e($v['hp']); ?>" inputmode="tel" required>
          </div>
        </div>
        <div class="field">
          <label for="outlet">Outlet</label>
          <select id="outlet" name="outlet" required>
            <option value="">Pilih outlet</option>
            <?php foreach ($lokasi as $lok): ?>
              <option value="<?php echo e($lok); ?>"<?php echo $v['outlet'] === $lok ? ' selected' : ''; ?>><?php echo e($lok); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <fieldset class="field fieldset">
          <legend class="h-small">Pilihan Pengambilan</legend>
          <div class="choice">
            <?php foreach (['Dine In', 'Take Away'] as $opt): ?>
            <label>
              <input type="radio" name="pengambilan" value="<?php echo $opt; ?>"<?php echo $v['pengambilan'] === $opt ? ' checked' : ''; ?>>
              <span><?php echo $opt; ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <fieldset class="field fieldset">
          <legend class="h-small">Metode Pembayaran</legend>
          <div class="choice">
            <?php foreach ($metode as $kunci => $m): ?>
            <label>
              <input type="radio" name="metode" value="<?php echo e($kunci); ?>"<?php echo $v['metode'] === $kunci ? ' checked' : ''; ?>>
              <span><?php echo e($m['label']); ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <div class="field">
          <label for="catatan">Catatan Pesanan</label>
          <textarea id="catatan" name="catatan" maxlength="200"><?php echo e($v['catatan']); ?></textarea>
        </div>
        <p class="muted small">* Pastikan data pemesanan sudah benar.</p>
      </form>

      <aside class="panel">
        <h2 class="h-small">Ringkasan Pesanan</h2>
        <?php foreach ($items as $it): ?>
          <div class="sum-row"><span><?php echo e(item_label($it)); ?> x<?php echo $it['qty']; ?></span><strong><?php echo format_rupiah($it['sub']); ?></strong></div>
        <?php endforeach; ?>
        <div class="sum-row total"><span>Total</span><strong><?php echo format_rupiah($total); ?></strong></div>
        <button class="btn block" type="submit" form="checkoutForm">Lanjut Pembayaran</button>
        <a class="btn outline block" style="margin-top:12px;" href="cart.php">Kembali ke Keranjang</a>
      </aside>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="band">
      <div>
        <h2>Hampir selesai!</h2>
        <p>Periksa data pemesan lalu lanjutkan ke metode pembayaran.</p>
      </div>
      <button class="btn" type="submit" form="checkoutForm">Lanjut Bayar</button>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
