<?php
$page_title = 'Home';
require_once 'includes/products-data.php';
$favorit = array_filter($products, fn($p) => $p['favorit']);
require 'includes/header.php';
?>

<section>
  <div class="wrap hero">
    <div>
      <h1>Satu Tempat untuk Menikmati Kopi, Makanan, dan Momen Berharga</h1>
      <p>Goona Cafe menghadirkan berbagai pilihan menu mulai dari Coffee Series, Non Coffee Series, Tea Series, hingga makanan dan snack favorit yang cocok dinikmati saat menunggu perjalanan.</p>
      <div class="btn-row">
        <a class="btn" href="produk.php">Lihat Menu</a>
        <button class="btn outline" type="button" onclick="toggleChat(true)">Pesan Sekarang</button>
      </div>
    </div>
    <div class="art hero-art"><img src="assets/img/lokasi/hero.jpg" alt="Goona Cafe tampak depan"></div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center">
      <h2>Menu Favorit Goona Cafe</h2>
      <p style="margin:8px auto 0;">Beragam pilihan menu untuk menemani setiap momenmu di Goona Cafe.</p>
    </div>
    <div class="grid-3">
      <?php foreach ($favorit as $p): ?>
      <div class="card">
        <?php echo produk_thumb($p, 'produk-detail.php?id=' . $p['id']); ?>
        <h3><a href="produk-detail.php?id=<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></a></h3>
        <p class="desc"><?php echo e($p['desc']); ?></p>
        <div class="card-foot">
          <span class="price"><?php echo harga_tampil($p); ?></span>
          <a class="btn outline small" href="produk-detail.php?id=<?php echo $p['id']; ?>">Pesan Sekarang</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap split center-y">
    <div class="art" style="aspect-ratio:4/3;"><img src="assets/img/lokasi/outlet-malam.jpg" alt="Suasana Goona Cafe di malam hari"></div>
    <div>
      <h2>Rasa yang Menemani Perjalanan</h2>
      <p>Goona Cafe adalah ruang nyaman untuk menciptakan cerita, berbagi momen, dan menikmati cita rasa terbaik.</p>
      <div class="btn-row"><a class="btn outline" href="about.php">Kenali Kami</a></div>
    </div>
  </div>
</section>

<section class="section dark">
  <div class="wrap cta-band">
    <h2>Siap Menikmati Pengalaman Berbeda?</h2>
    <p style="margin:10px auto 0;">Datang, nikmati suasananya, dan temukan menu favoritmu.</p>
    <div class="btn-row">
      <a class="btn light" href="contact.php">Kunjungi Goona Cafe</a>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
