<?php
$page_title = 'Produk';
require_once 'includes/products-data.php';

$kategori_aktif = isset($_GET['kategori']) && array_key_exists($_GET['kategori'], $category_labels)
    ? $_GET['kategori'] : 'all';
$baru_ditambah = (int)($_GET['added'] ?? 0);

require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <div class="section-head center">
      <h1 class="h-page">Temukan Menu Favoritmu</h1>
      <p style="margin:8px auto 0;">Berbagai pilihan minuman, makanan, dan snack favorit yang siap menemani setiap momenmu.</p>
    </div>

    <div class="filters">
      <?php foreach ($category_labels as $key => $label): ?>
        <a href="produk.php?kategori=<?php echo $key; ?>"
           class="filter-btn <?php echo $kategori_aktif === $key ? 'active' : ''; ?>"
           data-cat="<?php echo $key; ?>">
          <?php echo e($label); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Semua produk selalu dirender; produk di luar kategori aktif hanya disembunyikan (hidden). -->
    <div class="grid-3 products" id="productGrid">
      <?php foreach ($products as $p):
        $sembunyi = $kategori_aktif !== 'all' && $p['category'] !== $kategori_aktif; ?>
      <div class="card product-card" data-cat="<?php echo $p['category']; ?>"<?php echo $sembunyi ? ' hidden' : ''; ?>>
        <?php echo produk_thumb($p, 'produk-detail.php?id=' . $p['id']); ?>
        <h3><a href="produk-detail.php?id=<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></a></h3>
        <p class="desc"><?php echo e($p['desc']); ?></p>
        <div class="card-foot">
          <span class="price"><?php echo harga_tampil($p); ?></span>
          <?php if ($p['varian']): ?>
          <a class="btn outline small" href="produk-detail.php?id=<?php echo $p['id']; ?>">Pilih Varian</a>
          <?php else: ?>
          <form method="post" action="cart-action.php" class="add-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
            <input type="hidden" name="qty" value="1">
            <input type="hidden" name="redirect" value="produk.php?kategori=<?php echo e($kategori_aktif); ?>">
            <button class="btn outline small" type="submit">Tambah Pesanan</button>
          </form>
          <?php endif; ?>
        </div>
        <span class="added<?php echo $baru_ditambah === $p['id'] ? ' show' : ''; ?>" role="status">Ditambahkan &#10003;</span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
