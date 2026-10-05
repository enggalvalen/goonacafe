<?php
/**
 * Pengaturan umum Goona Cafe.
 * Kontak dan sosmed Goona Cafe (dipakai di footer dan halaman Contact).
 */
$kontak = [
    'whatsapp'  => 'https://wa.me/6285850326586',
    'instagram' => 'https://www.instagram.com/goona_cafe',
    'tiktok'    => 'https://www.tiktok.com/@goona.cafe?_r=1&_t=ZS-9AFZkLzfzoQ',
    'email'     => 'mailto:enggalvalen636@gmail.com',
];

$jam_buka = '05.00 - 00.00';   // setiap hari

// Outlet => link Google Maps ('' = belum ada link). Tambahkan link saat sudah tersedia.
$lokasi_maps = [
    'Stasiun Gubeng Baru'       => 'https://share.google/t1RgXQO5fZiJvoehC',
    'Stasiun Gubeng Lama'       => '',
    'Stasiun Wonokromo'         => 'https://share.google/h1S3ZKAjPWV5VYDDs',
    'Stasiun Semut'             => '',
    'Stasiun Bangil (Pasuruan)' => '',
];
$lokasi = array_keys($lokasi_maps);

// Metode pembayaran. QRIS baru tampil di Checkout kalau file gambarnya ada di assets/img/.
$pembayaran = [
    'qris' => ['label' => 'QRIS', 'gambar' => 'assets/img/qris.png'],
    'bri'  => ['label' => 'Transfer Bank BRI', 'rekening' => '007001038092531', 'atas_nama' => 'ENGGAL VABY VALENTINO'],
    'dana' => ['label' => 'DANA', 'nomor' => '085850326586'],
];

/** Metode pembayaran yang siap dipakai (QRIS disembunyikan sampai gambarnya diupload). */
function metode_pembayaran_tersedia() {
    global $pembayaran;
    $tersedia = [];
    foreach ($pembayaran as $kunci => $m) {
        if (isset($m['gambar']) && !file_exists(dirname(__DIR__) . '/' . $m['gambar'])) continue;
        $tersedia[$kunci] = $m;
    }
    return $tersedia;
}

function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

/** Potong teks maksimal $maks karakter (aman walau ekstensi mbstring belum aktif). */
function potong($teks, $maks) {
    $teks = (string)$teks;
    return function_exists('mb_substr') ? mb_substr($teks, 0, $maks) : substr($teks, 0, $maks);
}
