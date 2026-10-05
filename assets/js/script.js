(function () {
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  // ==== Navigasi mobile ====
  const navToggle = $('#navToggle');
  const navLinks  = $('#navLinks');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
      const open = navLinks.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open);
    });
  }

  // ==== Filter kategori produk (hanya aktif di produk.php) ====
  // PHP selalu merender semua produk; filter hanya menyembunyikan kartu,
  // jadi berpindah kategori tidak pernah menghasilkan halaman kosong.
  $$('.filter-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      $$('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.cat;
      $$('.product-card').forEach(card => {
        card.hidden = !(cat === 'all' || card.dataset.cat === cat);
      });
      $$('.add-form input[name="redirect"]').forEach(i => {
        i.value = 'produk.php?kategori=' + cat;
      });
      if (window.history.replaceState) window.history.replaceState(null, '', btn.getAttribute('href'));
    });
  });

  // ==== Badge keranjang ====
  function setBadge(n) {
    const b = $('#cartBadge');
    if (!b) return;
    b.textContent = n;
    b.hidden = !n;
  }

  // ==== Tambah Pesanan tanpa reload (fallback: kirim form biasa) ====
  $$('.add-form').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'fetch' }
      })
        .then(r => { if (!r.ok) throw new Error('gagal'); return r.json(); })
        .then(d => {
          setBadge(d.count);
          const note = form.closest('.card').querySelector('.added');
          if (note) {
            note.classList.add('show');
            setTimeout(() => note.classList.remove('show'), 1600);
          }
        })
        .catch(() => form.submit());
    });
  });

  // ==== Tombol jumlah +/- di detail produk ====
  $$('[data-stepper]').forEach(box => {
    const input = $('input', box);
    $$('button[data-step]', box).forEach(btn => {
      btn.addEventListener('click', () => {
        const next = (parseInt(input.value, 10) || 1) + parseInt(btn.dataset.step, 10);
        input.value = Math.max(1, Math.min(99, next));
      });
    });
  });

  // ==== Harga mengikuti pilihan varian (Hot / Ice) di detail produk ====
  const hargaEl = $('#hargaTampil');
  $$('[data-varian] input[type="radio"]').forEach(r => {
    r.addEventListener('change', () => { if (hargaEl) hargaEl.textContent = r.dataset.harga; });
  });

  // ==== Widget Chat ====
  function nowTime() {
    const d = new Date();
    return String(d.getHours()).padStart(2, '0') + '.' + String(d.getMinutes()).padStart(2, '0');
  }

  window.toggleChat = function (force) {
    const panel = $('#chatPanel');
    const open = force === undefined ? !panel.classList.contains('open') : force;
    panel.classList.toggle('open', open);
    if (open) panel.classList.remove('min');
  };

  window.minimizeChat = function () {
    $('#chatPanel').classList.toggle('min');
  };

  function pushBubble(text, me, link) {
    const body = $('#chatBody');
    const wrap = document.createElement('div');
    wrap.className = 'msg' + (me ? ' me' : '');
    const b = document.createElement('div');
    b.className = 'bubble';
    b.textContent = text;
    if (link) {
      const a = document.createElement('a');
      a.href = link.href;
      a.textContent = link.label;
      b.append(' ', a);
    }
    const t = document.createElement('span');
    t.className = 'time';
    t.textContent = nowTime();
    wrap.append(b, t);
    body.appendChild(wrap);
    body.scrollTop = body.scrollHeight;
  }

  window.quickReply = function (kind) {
    const map = {
      menu:   ['Pesan Menu', 'Silakan lihat menu lengkap kami di halaman', { href: 'produk.php', label: 'Produk' }],
      lokasi: ['Lokasi Kami', 'Kami ada di Stasiun Gubeng Baru, Gubeng Lama, Wonokromo, Semut, dan Bangil (Pasuruan).'],
      jam:    ['Jam Operasional', 'Goona Cafe buka setiap hari pukul 05.00 sampai 00.00 di seluruh lokasi stasiun.']
    };
    const [q, a, link] = map[kind];
    pushBubble(q, true);
    setTimeout(() => pushBubble(a, false, link), 350);
  };

  window.sendChat = function () {
    const input = $('#chatInput');
    if (!input || !input.value.trim()) return;
    pushBubble(input.value.trim(), true);
    input.value = '';
    setTimeout(() => pushBubble('Terima kasih, tim kami akan segera membalas pesanmu.'), 400);
  };

  document.addEventListener('DOMContentLoaded', () => {
    $$('[data-now]').forEach(el => { el.textContent = nowTime(); });
    const chatInput = $('#chatInput');
    if (chatInput) {
      chatInput.addEventListener('keydown', e => { if (e.key === 'Enter') window.sendChat(); });
    }
  });
})();
