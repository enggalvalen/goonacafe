<?php
$page_title = 'Blog';
require_once 'includes/blog-data.php';
$posts    = get_posts();
$featured = null;
$latest   = [];
foreach ($posts as $po) {
    if ($po['featured'] && !$featured) $featured = $po; else $latest[] = $po;
}
require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <h1 class="h-page left">Goona Stories</h1>
    <p class="muted" style="margin-bottom:28px;">Promo, menu baru, event, dan cerita kecil dari Goona Cafe.</p>

    <?php if ($featured): ?>
    <article class="panel feature">
      <div class="art" style="aspect-ratio:16/10;"><?php if ($f = blog_foto($featured)): ?><img src="<?php echo e($f); ?>" alt="<?php echo e($featured['judul']); ?>"><?php endif; ?></div>
      <div>
        <span class="tag"><?php echo e($featured['kategori']); ?></span>
        <h2 style="margin-top:14px;"><?php echo e($featured['judul']); ?></h2>
        <p><?php echo e($featured['ringkas']); ?></p>
        <div class="btn-row"><a class="btn" href="blog-detail.php?slug=<?php echo e($featured['slug']); ?>">Lihat Selengkapnya</a></div>
      </div>
    </article>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <h2 class="h-section">Latest Content</h2>
    <div class="grid-3">
      <?php foreach ($latest as $po): ?>
      <article class="card">
        <a href="blog-detail.php?slug=<?php echo e($po['slug']); ?>" class="thumb wide" aria-label="Baca <?php echo e($po['judul']); ?>"><?php if ($f = blog_foto($po)): ?><img src="<?php echo e($f); ?>" alt="" loading="lazy"><?php endif; ?></a>
        <span class="tag small-tag"><?php echo e($po['kategori']); ?></span>
        <h3 style="margin-top:8px;"><a href="blog-detail.php?slug=<?php echo e($po['slug']); ?>"><?php echo e($po['judul']); ?></a></h3>
        <p class="desc"><?php echo e($po['ringkas']); ?></p>
        <div class="card-foot"><a class="post-link" href="blog-detail.php?slug=<?php echo e($po['slug']); ?>">Baca selengkapnya &rarr;</a></div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="band tint">
      <div>
        <h2>Mau tahu lebih banyak tentang Goona?</h2>
        <p>Lihat semua menu andalan kami dan pilih favoritmu.</p>
      </div>
      <a class="btn" href="produk.php">Lihat Menu</a>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
