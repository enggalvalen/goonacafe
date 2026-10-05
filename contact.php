<?php
$page_title = 'Contact';
require_once 'includes/cart-functions.php'; // memuat config ($kontak, $lokasi)

$channels = [
    ['title' => 'WhatsApp',        'desc' => 'Pesan menu atau tanyakan informasi melalui WhatsApp.',  'action' => 'Chat WhatsApp',      'href' => $kontak['whatsapp']],
    ['title' => 'Instagram',       'desc' => 'Dapatkan informasi terbaru dan promo Goona Cafe.',      'action' => 'Kunjungi Instagram', 'href' => $kontak['instagram']],
    ['title' => 'Jam Operasional', 'desc' => 'Buka setiap hari pukul ' . $jam_buka . ' di seluruh lokasi stasiun.', 'action' => 'Lihat Jam Buka',     'href' => ''],
    ['title' => 'Email',           'desc' => 'Kirim pertanyaan atau kerja sama melalui email.',        'action' => 'Kirim Email',        'href' => $kontak['email']],
];

// ==== Proses form pesan (server-side, dengan PHP) ====
$form_note = '';
$form_ok   = false;
$nama = $email = $pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_pesan'])) {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if ($nama !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $pesan !== '') {
        $tersimpan = false;
        if ($pdo = db()) {
            try {
                $pdo->prepare('INSERT INTO pesan_kontak (nama, email, pesan) VALUES (?,?,?)')
                    ->execute([potong($nama, 100), potong($email, 150), potong($pesan, 2000)]);
                $tersimpan = true;
            } catch (Throwable $ex) {
                $tersimpan = false;
            }
        }
        if ($tersimpan) {
            $form_note = 'Terima kasih, ' . e($nama) . '! Pesanmu sudah kami terima dan akan segera kami balas.';
            $form_ok   = true;
            $nama = $email = $pesan = '';
        } else {
            $form_note = 'Maaf, pesanmu belum bisa terkirim karena sistem sedang bermasalah. Isi formulir tetap kami simpan di halaman ini; coba lagi sebentar lagi atau hubungi kami lewat WhatsApp.';
        }
    } else {
        $form_note = 'Mohon isi Nama, Email (format valid), dan Pesan dengan benar.';
    }
}

require 'includes/header.php';
?>

<section class="section" style="border-top:none;">
  <div class="wrap" style="text-align:center;">
    <h1 class="h-page">Temukan Goona Cafe Terdekat</h1>
    <p style="margin:10px auto 0;">Nikmati menu favorit Goona Cafe di berbagai lokasi stasiun dengan suasana nyaman yang siap menemani perjalananmu.</p>
    <div class="btn-row" style="justify-content:center;">
      <a class="btn" href="#lokasi">Lihat Lokasi Kami</a>
      <a class="btn outline" href="#msgForm">Hubungi Kami</a>
    </div>
  </div>
</section>

<section class="section" id="lokasi">
  <div class="wrap split">
    <div>
      <h2>Temukan Lokasi Kami</h2>
      <p>Goona Cafe hadir di beberapa lokasi stasiun strategis untuk menemani perjalanan dan waktu santaimu.</p>
      <ul class="loc-list">
        <?php foreach ($lokasi_maps as $lok => $maps): ?>
          <li>
            <span class="dot"></span><span class="loc-name"><?php echo e($lok); ?></span>
            <?php if ($maps): ?>
              <a class="loc-map" href="<?php echo e($maps); ?>" target="_blank" rel="noopener">Buka di Maps</a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="panel map-panel">
      <h3>Petunjuk arah</h3>
      <p>Buka lokasi outlet di Google Maps untuk rute terdekat dari posisimu.</p>
      <div class="btn-row">
        <?php foreach ($lokasi_maps as $lok => $maps): if (!$maps) continue; ?>
          <a class="btn outline small" href="<?php echo e($maps); ?>" target="_blank" rel="noopener"><?php echo e($lok); ?></a>
        <?php endforeach; ?>
      </div>
      <p class="muted small">Link peta outlet lainnya menyusul.</p>
    </div>
  </div>
</section>

<section class="section" id="hubungi">
  <div class="wrap">
    <div class="section-head center">
      <h2>Kami Selalu Siap Membantu</h2>
      <p style="margin:8px auto 0;">Pilih cara yang paling nyaman untuk menghubungi kami.</p>
    </div>
    <div class="grid-4">
      <?php foreach ($channels as $c): ?>
      <div class="chan">
        <div class="ring"></div>
        <h3><?php echo e($c['title']); ?></h3>
        <p><?php echo e($c['desc']); ?></p>
        <?php if ($c['href']): ?>
          <a class="btn outline small" style="margin-top:12px;" href="<?php echo e($c['href']); ?>" target="_blank" rel="noopener"><?php echo e($c['action']); ?></a>
        <?php else: ?>
          <button class="btn outline small" style="margin-top:12px;" type="button" onclick="toggleChat(true)"><?php echo e($c['action']); ?></button>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="msgForm">
  <div class="wrap split">
    <div>
      <h2>Ada Pertanyaan? Hubungi Kami</h2>
      <p>Isi formulir di bawah ini dan kami akan segera merespons.</p>

      <form method="post" action="contact.php#msgForm" style="margin-top:24px;">
        <div class="row-2">
          <div class="field">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" value="<?php echo e($nama); ?>" required>
          </div>
          <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" value="<?php echo e($email); ?>" required>
          </div>
        </div>
        <div class="field">
          <label for="pesan">Pesan</label>
          <textarea id="pesan" name="pesan" placeholder="Tulis pesan kamu di sini..." required><?php echo e($pesan); ?></textarea>
        </div>
        <button class="btn" type="submit" name="kirim_pesan" value="1">Kirim Pesan</button>
        <?php if ($form_note): ?>
          <p class="form-note<?php echo $form_ok ? '' : ' error'; ?>" role="status"><?php echo $form_note; ?></p>
        <?php endif; ?>
      </form>
    </div>
    <div class="art" style="aspect-ratio:4/3.6;"><img src="assets/img/lokasi/konter.jpg" alt="Konter Goona Cafe di stasiun"></div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
