<?php
$page_title = 'FAQ';
require_once 'includes/faq-data.php';
$kolom = [[], []];
foreach ($faqs as $i => $f) $kolom[$i % 2][] = $f;
require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <h1 class="h-page left">Frequently Asked Questions</h1>
    <p class="muted" style="margin-bottom:28px;">Jawaban singkat untuk pertanyaan yang sering ditanyakan pelanggan Goona.</p>

    <div class="faq-cols">
      <?php foreach ($kolom as $col): ?>
      <div class="faq-col">
        <?php foreach ($col as $f): ?>
        <details class="faq-item">
          <summary>
            <span class="q"><?php echo e($f['q']); ?></span>
            <span class="plus" aria-hidden="true">+</span>
            <span class="hint">Klik untuk melihat jawaban...</span>
          </summary>
          <p class="faq-answer"><?php echo e($f['a']); ?></p>
        </details>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="panel">
      <h2>Masih punya pertanyaan?</h2>
      <p>Hubungi Goona melalui Chat atau Contact untuk bantuan lebih lanjut.</p>
      <div class="btn-row">
        <button class="btn" type="button" onclick="toggleChat(true)">Chat Goona</button>
        <a class="btn outline" href="contact.php">Contact Us</a>
      </div>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
