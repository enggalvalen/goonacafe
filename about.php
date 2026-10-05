<?php
$page_title = 'About Us';

// 'foto' = file di assets/img/tim/, 'pos' = bagian foto yang ditampilkan (atur agar wajah terlihat).
$tim = [
    ['nama' => 'Ringga Galvin Fullviano',    'peran' => 'Ketua',             'foto' => 'assets/img/tim/ringga.jpg', 'pos' => '50% 30%'],
    ['nama' => 'May Seila Ayu Setya Nanda',  'peran' => 'Designer Website',  'foto' => 'assets/img/tim/may-seila.jpg', 'pos' => '50% 30%'],
    ['nama' => 'Enggal Vaby Valentino',      'peran' => 'Website Developer', 'foto' => 'assets/img/tim/enggal.jpg', 'pos' => '55% 40%'],
    ['nama' => 'Putri Mualimah Pramudya',    'peran' => 'Marketing Analyst', 'foto' => 'assets/img/tim/putri.jpg', 'pos' => '50% 35%'],
    ['nama' => 'Muchammad Irfat Argyansyah', 'peran' => 'Content Writer',    'foto' => 'assets/img/tim/irfat.jpg', 'pos' => '50% 30%'],
];
require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap">
    <h1 class="h-page left" style="margin-bottom:28px;">About Us</h1>
    <div class="panel split center-y">
      <div class="art" style="aspect-ratio:4/3;"><img src="assets/img/lokasi/outlet-malam.jpg" alt="Suasana Goona Cafe di malam hari"></div>
      <div>
        <h2>Cerita di Balik Goona Cafe</h2>
        <p>Goona Cafe hadir untuk menjadi ruang hangat bagi siapa saja yang ingin menikmati berbagai menu, berbagi cerita, dan menciptakan momen.</p>
        <div class="btn-row"><button class="btn" type="button" onclick="toggleChat(true)">Chat Sekarang</button></div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head center"><h2>Team Pengembangan</h2></div>
    <div class="team">
      <?php foreach ($tim as $m): ?>
      <div class="member">
        <div class="photo" style="--pos:<?php echo e($m['pos']); ?>;">
          <?php if ($m['foto']): ?><img src="<?php echo e($m['foto']); ?>" alt="Foto <?php echo e($m['nama']); ?>"><?php endif; ?>
        </div>
        <h3><?php echo e($m['nama']); ?></h3>
        <p class="muted small"><?php echo e($m['peran']); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="band">
      <div>
        <h2>Temukan Goona Cafe di Dekatmu</h2>
        <p>Ingin menikmati suasana dan menu Goona Cafe? Temukan outlet terdekatmu.</p>
      </div>
      <a class="btn" href="contact.php#lokasi">Lihat Lokasi</a>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
