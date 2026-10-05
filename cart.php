<?php
$page_title = 'Keranjang';
require_once 'includes/cart-functions.php';
$items = cart_items();
$total = cart_total();
require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <h1 class="h-page left">Keranjang Belanja</h1>
    <p class="muted" style="margin-bottom:28px;">Periksa kembali pesanan sebelum melanjutkan.</p>

    <?php if (!$items): ?>
      <div class="panel empty">
        <h2>Keranjangmu masih kosong</h2>
        <p>Pilih menu favoritmu dulu, nanti muncul di sini.</p>
        <div class="btn-row"><a class="btn" href="produk.php">Lihat Menu</a></div>
      </div>
    <?php else: ?>
    <div class="shop-layout">
      <div class="panel">
        <h2 class="h-small">Produk</h2>
        <?php foreach ($items as $it): $p = $it['p']; ?>
        <div class="cart-row">
          <?php echo produk_thumb($p, 'produk-detail.php?id=' . $p['id']); ?>
          <div>
            <h3><a href="produk-detail.php?id=<?php echo $p['id']; ?>"><?php echo e(item_label($it)); ?></a></h3>
            <div class="muted small"><?php echo format_rupiah($it['harga']); ?></div>
            <?php if ($it['note'] !== ''): ?><div class="muted small">Catatan: <?php echo e($it['note']); ?></div><?php endif; ?>
            <form method="post" action="cart-action.php" class="qty" style="margin-top:10px;">
              <input type="hidden" name="key" value="<?php echo e($it['key']); ?>">
              <input type="hidden" name="redirect" value="cart.php">
              <button type="submit" name="action" value="dec" aria-label="Kurangi jumlah"<?php echo $it['qty'] <= 1 ? ' disabled' : ''; ?>>&minus;</button>
              <span class="qty-num" aria-label="Jumlah"><?php echo $it['qty']; ?></span>
              <button type="submit" name="action" value="inc" aria-label="Tambah jumlah">+</button>
            </form>
          </div>
          <div class="price"><?php echo format_rupiah($it['sub']); ?></div>
          <form method="post" action="cart-action.php">
            <input type="hidden" name="key" value="<?php echo e($it['key']); ?>">
            <input type="hidden" name="redirect" value="cart.php">
            <button class="linkbtn" type="submit" name="action" value="remove">Hapus</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>

      <aside class="panel">
        <h2 class="h-small">Ringkasan Pesanan</h2>
        <div class="sum-row"><span>Subtotal</span><strong><?php echo format_rupiah($total); ?></strong></div>
        <div class="sum-row"><span>Biaya lain</span><strong>Rp0</strong></div>
        <div class="sum-row total"><span>Total</span><strong><?php echo format_rupiah($total); ?></strong></div>
        <a class="btn block" href="checkout.php">Checkout</a>
        <a class="btn outline block" style="margin-top:12px;" href="produk.php">Lanjut Belanja</a>
      </aside>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($items): ?>
<section class="section">
  <div class="wrap">
    <div class="band">
      <div>
        <h2>Sudah sesuai?</h2>
        <p>Lanjutkan ke checkout untuk mengisi data pemesanan.</p>
      </div>
      <a class="btn" href="checkout.php">Checkout</a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
