<?php
require_once 'includes/blog-data.php';
$po = find_post($_GET['slug'] ?? '');

if (!$po) {
    http_response_code(404);
    $page_title = 'Artikel tidak ditemukan';
    require 'includes/header.php';
    ?>
    <section class="section" style="border-top:none;">
      <div class="wrap" style="text-align:center;">
        <h1 class="h-page">Artikel tidak ditemukan</h1>
        <p style="margin:10px auto 0;">Cerita yang kamu cari tidak ada atau sudah dihapus.</p>
        <div class="btn-row" style="justify-content:center;"><a class="btn" href="blog.php">Kembali ke Blog</a></div>
      </div>
    </section>
    <?php
    require 'includes/footer.php';
    exit;
}

$page_title = $po['judul'];
$related = array_slice(array_values(array_filter(get_posts(), fn($x) => $x['slug'] !== $po['slug'])), 0, 3);
require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <h1 class="h-page left"><?php echo e($po['judul']); ?></h1>
    <p class="muted small" style="margin-bottom:24px;"><?php echo e($po['tanggal']); ?> &bull; Goona Cafe</p>

    <div class="art hero-wide"><?php if ($f = blog_foto($po)): ?><img src="<?php echo e($f); ?>" alt="<?php echo e($po['judul']); ?>"><?php endif; ?></div>

    <div class="article-layout">
      <article class="panel prose">
        <h2 class="h-small">Tentang Konten</h2>
        <?php foreach ($po['isi'] as $para): ?><p><?php echo e($para); ?></p><?php endforeach; ?>
        <h2 style="margin-top:26px;font-size:22px;"><?php echo e($po['detail_judul']); ?></h2>
        <ul>
          <?php foreach ($po['poin'] as $poin): ?><li><?php echo e($poin); ?></li><?php endforeach; ?>
        </ul>
        <div class="btn-row"><a class="btn" href="produk.php">Lihat Produk</a></div>
      </article>

      <aside class="panel">
        <h2 class="h-small">Related Content</h2>
        <?php foreach ($related as $r): ?>
        <a class="related-item" href="blog-detail.php?slug=<?php echo e($r['slug']); ?>">
          <span class="mini-thumb"><?php if ($f = blog_foto($r)): ?><img src="<?php echo e($f); ?>" alt=""><?php endif; ?></span>
          <span><?php echo e($r['kategori']); ?>: <?php echo e($r['judul']); ?></span>
        </a>
        <?php endforeach; ?>
      </aside>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="band tint">
      <div>
        <h2>Temukan cerita Goona lainnya</h2>
        <p>Kembali ke Blog untuk melihat promo dan konten terbaru.</p>
      </div>
      <a class="btn" href="blog.php">Kembali ke Blog</a>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
