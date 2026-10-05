</main>

<footer class="site-footer">
  <div class="wrap">
    <div class="foot-grid">
      <div>
        <a href="index.php" class="foot-brand" aria-label="Goona Cafe"><img src="assets/img/logo.png" alt="Goona Cafe" width="700" height="229"></a>
        <p class="foot-tag">Your Caffeine Partner</p>
        <p class="foot-tag">&ldquo;Temani setiap momenmu.&rdquo;</p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul class="foot-links">
          <li><a href="index.php">Home</a></li>
          <li><a href="produk.php">Product</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="blog.php">Blog</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="faq.php">FAQ</a></li>
        </ul>
      </div>
      <div>
        <h4>Contact Us</h4>
        <ul class="foot-links">
          <li><a href="contact.php#lokasi">Locations</a></li>
          <li><a href="<?php echo e($kontak['whatsapp']); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
          <li><a href="<?php echo e($kontak['email']); ?>">Email</a></li>
          <li><a href="contact.php#hubungi">Open Hours</a></li>
        </ul>
      </div>
      <div>
        <h4>Follow Us</h4>
        <ul class="foot-links">
          <li><a href="<?php echo e($kontak['instagram']); ?>" target="_blank" rel="noopener">Instagram</a></li>
          <li><a href="<?php echo e($kontak['tiktok']); ?>" target="_blank" rel="noopener">TikTok</a></li>
          <li><a href="<?php echo e($kontak['whatsapp']); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
        </ul>
      </div>
    </div>
    <div class="foot-bottom">
      <span>&copy; 2026 Goona Cafe</span>
      <span>Privacy | Terms</span>
    </div>
  </div>
</footer>

<!-- CHAT WIDGET (tampil di semua halaman) -->
<button id="chatToggle" type="button" onclick="toggleChat()" aria-label="Buka chat">&#128172;</button>
<div id="chatPanel" role="dialog" aria-label="Chat Goona Cafe">
  <div class="chat-head">
    <span class="chat-title">Chat Goona Cafe</span>
    <span class="chat-actions">
      <button type="button" onclick="minimizeChat()" aria-label="Kecilkan chat">&minus;</button>
      <button type="button" onclick="toggleChat(false)" aria-label="Tutup chat">&#10005;</button>
    </span>
  </div>
  <div class="chat-profile">
    <span class="avatar">GOONA</span>
    <span>
      <strong>Goona Cafe</strong>
      <span class="online"><i></i> Online</span>
    </span>
  </div>
  <div class="chat-body" id="chatBody" aria-live="polite">
    <div class="msg">
      <div class="bubble">Halo &#128075;<br>Selamat datang di Goona Cafe.<br>Ada yang bisa kami bantu?</div>
      <span class="time" data-now></span>
    </div>
    <button class="quick" type="button" onclick="quickReply('menu')">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="5" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
      Pesan Menu
    </button>
    <button class="quick" type="button" onclick="quickReply('lokasi')">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="7"/></svg>
      Lokasi Kami
    </button>
    <button class="quick" type="button" onclick="quickReply('jam')">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>
      Jam Operasional
    </button>
    <div class="msg">
      <div class="bubble">Silakan pilih salah satu menu di atas atau ketik pertanyaan kamu.</div>
      <span class="time" data-now></span>
    </div>
  </div>
  <div class="chat-input">
    <input type="text" id="chatInput" placeholder="Ketik pesan di sini..." aria-label="Pesan">
    <button type="button" onclick="sendChat()" aria-label="Kirim pesan">&#10148;</button>
  </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
