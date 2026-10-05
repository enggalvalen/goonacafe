<?php
/**
 * Data blog "Goona Stories". Tanggal dan isi artikel adalah draf; sesuaikan dengan kondisi asli.
 */
function get_posts() {
    return [
        [
            'slug' => 'temani-momenmu', 'kategori' => 'Promo', 'featured' => true,
            'judul' => 'Temani Momenmu dengan Goona', 'tanggal' => '29 September 2026',
            'ringkas' => 'Nikmati pilihan favorit, promo terbaru, dan cerita yang membuat setiap kunjungan terasa lebih dekat.',
            'isi' => [
                'Menunggu kereta, baru turun dari perjalanan jauh, atau sekadar mencari tempat duduk yang nyaman. Goona Cafe hadir di beberapa stasiun supaya momen-momen itu ditemani secangkir minuman yang enak.',
                'Mulai dari Coffee Series, Non Coffee Series, Tea Series, sampai Paket Ayam dan Snacks, ada pilihan untuk pagi buta maupun larut malam. Kami buka setiap hari pukul 05.00 sampai 00.00.',
            ],
            'detail_judul' => 'Cara Menikmati',
            'poin' => [
                'Pilih menu favoritmu di halaman Produk',
                'Pesan lewat keranjang, pilih Dine In atau Take Away',
                'Ambil pesananmu di outlet Goona Cafe terdekat',
            ],
        ],
        [
            'slug' => 'paket-hemat', 'kategori' => 'Promo', 'featured' => false,
            'judul' => 'Paket Hemat untuk Teman Perjalanan', 'tanggal' => '25 September 2026',
            'ringkas' => 'Info promo dan pilihan paket yang praktis.',
            'isi' => [
                'Perjalanan panjang butuh bekal yang mengenyangkan. Paket Ayam Goona dibuat praktis: satu paket sudah lengkap, tinggal ambil dan jalan.',
                'Pilih Paket Gandini (nasi dengan sayap atau paha bawah) atau Paket Niken 2 (snack dengan dada atau paha atas), lalu padukan dengan minuman favoritmu.',
            ],
            'detail_judul' => 'Pilihan Paket',
            'poin' => [
                'Paket Gandini: nasi + sayap/paha bawah, Rp25.000',
                'Paket Niken 2: snack + dada/paha atas, Rp40.000',
                'Tersedia di semua outlet selama jam operasional (05.00 - 00.00)',
            ],
        ],
        [
            'slug' => 'menu-baru', 'kategori' => 'New Menu', 'featured' => false,
            'judul' => 'Kenalan dengan Menu Baru', 'tanggal' => '20 September 2026',
            'ringkas' => 'Highlight menu baru yang hadir di Goona.',
            'isi' => [
                'Kenalan dengan Creamy Reog Latte, kopi susu dengan gula aren yang pas menemani aktivitas sehari-hari. Manisnya lembut, cocok untuk kamu yang suka kopi tapi tidak mau terlalu pahit.',
                'Masih ada Butterschotch Coffee, Matchana, dan Strawberry Lemonade sebagai menu favorit yang selalu dicari.',
            ],
            'detail_judul' => 'Tentang Menu',
            'poin' => [
                'Creamy Reog Latte: kopi susu gula aren, Rp25.000',
                'Termasuk dalam Coffee Series',
                'Bisa ditambah catatan pesanan, misalnya less sugar',
            ],
        ],
        [
            'slug' => 'momen-kecil', 'kategori' => 'Goona Moments', 'featured' => false,
            'judul' => 'Momen Kecil di Goona', 'tanggal' => '15 September 2026',
            'ringkas' => 'Cerita singkat tentang suasana dan pengalaman di outlet.',
            'isi' => [
                'Di stasiun, setiap orang punya cerita: yang berangkat pagi-pagi, yang pulang larut malam, yang menunggu seseorang datang. Goona Cafe ingin jadi ruang kecil yang hangat di tengah semua itu.',
                'Bagikan momenmu di Goona Cafe lewat Instagram atau TikTok kami, dan temukan cerita pengunjung lainnya.',
            ],
            'detail_judul' => 'Temukan Kami',
            'poin' => [
                'Stasiun Gubeng Baru, Gubeng Lama, Wonokromo, Semut, dan Bangil (Pasuruan)',
                'Buka setiap hari pukul 05.00 sampai 00.00',
                'Instagram @goona_cafe dan TikTok @goona.cafe',
            ],
        ],
    ];
}

/** Foto artikel (assets/img/blog/{slug}.jpg), kosong kalau belum ada. */
function blog_foto($p) {
    $f = 'assets/img/blog/' . $p['slug'] . '.jpg';
    return file_exists(dirname(__DIR__) . '/' . $f) ? $f : '';
}

function find_post($slug) {
    foreach (get_posts() as $p) {
        if ($p['slug'] === $slug) return $p;
    }
    return null;
}
