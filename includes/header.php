<?php
require_once __DIR__ . '/cart-functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
// Halaman turunan menyalakan menu induknya
$nav_map    = ['produk-detail.php' => 'produk.php', 'blog-detail.php' => 'blog.php'];
$nav_active = $nav_map[$current_page] ?? $current_page;
$nav_items  = [
    'index.php'   => 'Home',
    'produk.php'  => 'Produk',
    'about.php'   => 'About Us',
    'blog.php'    => 'Blog',
    'contact.php' => 'Contact',
    'faq.php'     => 'FAQ',
];
$cart_count = cart_count();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo isset($page_title) ? e($page_title) . ' - Goona Cafe' : 'Goona Cafe'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/img/logo.png">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site">
  <nav class="bar wrap" aria-label="Navigasi utama">
    <a href="index.php" class="logo" aria-label="Goona Cafe - Beranda"><img src="assets/img/logo.png" alt="Goona Cafe" width="700" height="229"></a>
    <ul class="nav-links" id="navLinks">
      <?php foreach ($nav_items as $file => $label): ?>
        <li><a href="<?php echo $file; ?>" class="<?php echo $nav_active === $file ? 'active' : ''; ?>"<?php echo $nav_active === $file ? ' aria-current="page"' : ''; ?>><?php echo e($label); ?></a></li>
      <?php endforeach; ?>
    </ul>
    <div class="nav-right">
      <button class="btn small" type="button" onclick="toggleChat(true)">Chat Kami +</button>
      <a class="btn outline small cart-btn" href="cart.php" aria-label="Keranjang">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h3l2.6 12.2a1 1 0 0 0 1 .8h9.2a1 1 0 0 0 1-.8L21 7H6"/></svg>
        Cart
        <span class="cart-badge" id="cartBadge"<?php echo $cart_count ? '' : ' hidden'; ?>><?php echo $cart_count; ?></span>
      </a>
      <button class="hamburger" id="navToggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="navLinks">&#9776;</button>
    </div>
  </nav>
</header>

<main>
