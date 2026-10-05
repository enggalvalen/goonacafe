<?php
require_once 'includes/products-data.php';

$id = (int)($_GET['id'] ?? 0);
$p  = find_product($id);

if (!$p) {
    http_response_code(404);
    $page_title = 'Produk tidak ditemukan';
    require 'includes/header.php';
    ?>
    <section class="section" style="border-top:none;">
      <div class="wrap" style="text-align:center;">
        <h1 class="h-page">Produk tidak ditemukan</h1>
        <p style="margin:10px auto 0;">Menu yang kamu cari tidak ada atau sudah tidak tersedia.</p>
        <div class="btn-row" style="justify-content:center;"><a class="btn" href="produk.php">Lihat Menu</a></div>
      </div>
    </section>
    <?php
    require 'includes/footer.php';
    exit;
}

$page_title = $p['name'];

// Rekomendasi: kategori yang sama dulu, lalu produk lain
$lain = array_values(array_filter(get_products(), fn($x) => $x['id'] !== $p['id']));
usort($lain, fn($a, $b) => ($b['category'] === $p['category']) <=> ($a['category'] === $p['category']));
$related = array_slice($lain, 0, 3);

require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <nav class="crumb" aria-label="Breadcrumb">
      <a href="produk.php">Produk</a> / <span><?php echo e($p['name']); ?></span>
    </nav>

    <div class="detail">
      <!-- Ganti dengan <img src="assets/img/..." alt="..."> -->
      <?php echo produk_thumb($p, '', 'detail-photo'); ?>

      <div class="panel detail-info">
        <h1 class="h-detail"><?php echo e($p['name']); ?></h1>
        <div class="price big" id="hargaTampil"><?php echo format_rupiah($p['price']); ?></div>
        <p><?php echo e($p['desc']); ?></p>

        <h2 class="h-small">Deskripsi Produk</h2>
        <p class="muted">Informasi detail bahan, rasa, dan penyajian akan segera kami lengkapi.</p>

        <form method="post" action="cart-action.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
          <input type="hidden" name="redirect" value="cart.php">

          <?php if ($p['varian']): ?>
          <fieldset class="fieldset">
            <legend class="h-small">Pilihan Penyajian</legend>
            <div class="choice" data-varian>
              <?php foreach ($p['varian'] as $i => $vr): ?>
              <label>
                <input type="radio" name="varian" value="<?php echo $vr['id']; ?>" data-harga="<?php echo format_rupiah($vr['harga']); ?>"<?php echo $i === 0 ? ' checked' : ''; ?>>
                <span><?php echo e($vr['nama']); ?> &middot; <?php echo format_rupiah($vr['harga']); ?></span>
              </label>
              <?php endforeach; ?>
            </div>
          </fieldset>
          <?php endif; ?>

          <h2 class="h-small">Jumlah</h2>
          <div class="qty" data-stepper>
            <button type="button" data-step="-1" aria-label="Kurangi jumlah">&minus;</button>
            <input type="number" name="qty" value="1" min="1" max="99" aria-label="Jumlah">
            <button type="button" data-step="1" aria-label="Tambah jumlah">+</button>
          </div>

          <div class="detail-note">
            <div class="field grow">
              <label for="note" class="h-small" style="opacity:1;">Catatan Pesanan</label>
              <input type="text" id="note" name="note" maxlength="120" placeholder="Contoh: less sugar">
            </div>
            <button class="btn" type="submit">Tambah ke Keranjang</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <h2 class="h-section">Yang Ini Juga Bikin Nagih.</h2>
    <div class="related">
      <?php foreach ($related as $r): ?>
      <div class="mini">
        <?php echo produk_thumb($r, 'produk-detail.php?id=' . $r['id']); ?>
        <div>
          <h3><?php echo e($r['name']); ?></h3>
          <div class="price"><?php echo harga_tampil($r); ?></div>
          <a class="btn outline small" style="margin-top:10px;" href="produk-detail.php?id=<?php echo $r['id']; ?>">Lihat Detail</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="band">
      <div>
        <h2>Mau coba menu lain?</h2>
        <p>Kembali ke daftar menu dan pilih kategori favoritmu.</p>
      </div>
      <a class="btn" href="produk.php">Lihat Menu</a>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
