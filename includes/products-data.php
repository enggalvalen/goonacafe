<?php
/**
 * Data produk Goona Cafe.
 * Sumber utama: tabel `produk` + `produk_varian` di MySQL (diubah lewat panel admin).
 * Kalau database belum aktif, dipakai data cadangan di products-fallback.php.
 * Bentuk satu produk: id, name, desc, price, category, favorit, foto, varian[] (id, nama, harga)
 */
require_once __DIR__ . '/db.php';

function get_products($semua = false) {
    static $cache = [];
    $k = $semua ? 'semua' : 'aktif';
    if (isset($cache[$k])) return $cache[$k];

    $pdo = db();
    if ($pdo) {
        try {
            $sql = 'SELECT id, nama, deskripsi, harga, kategori, foto, favorit, aktif FROM produk'
                 . ($semua ? '' : ' WHERE aktif = 1') . ' ORDER BY urutan, id';
            $rows = $pdo->query($sql)->fetchAll();
            $varian = [];
            foreach ($pdo->query('SELECT id, produk_id, nama, harga FROM produk_varian ORDER BY urutan, id') as $v) {
                $varian[(int)$v['produk_id']][] = ['id' => (int)$v['id'], 'nama' => $v['nama'], 'harga' => (int)$v['harga']];
            }
            $list = [];
            foreach ($rows as $r) {
                $vs = $varian[(int)$r['id']] ?? [];
                $list[] = [
                    'id' => (int)$r['id'], 'name' => $r['nama'], 'desc' => $r['deskripsi'],
                    'price' => $vs ? min(array_column($vs, 'harga')) : (int)$r['harga'],
                    'category' => $r['kategori'], 'favorit' => (bool)$r['favorit'], 'aktif' => (bool)$r['aktif'],
                    'foto' => $r['foto'], 'varian' => $vs,
                ];
            }
            return $cache[$k] = $list;
        } catch (Throwable $ex) {
            // tabel belum ada -> pakai data cadangan
        }
    }
    $list = require __DIR__ . '/products-fallback.php';
    foreach ($list as &$p) $p['aktif'] = true;
    return $cache[$k] = $list;
}

$products = get_products();

$category_labels = [
    'all'       => 'Semua',
    'coffee'    => 'Coffee Series',
    'tea'       => 'Tea Series',
    'noncoffee' => 'Non Coffee Series',
    'ayam'      => 'Paket Ayam',
    'snacks'    => 'Snacks',
    'alacarte'  => 'Ala Carte',
];

function find_product($id) {
    foreach (get_products() as $p) {
        if ($p['id'] === (int)$id) return $p;
    }
    return null;
}

/** Varian milik produk ($varian_id = 0 untuk produk tanpa varian). Null kalau tidak valid. */
function find_varian($p, $varian_id) {
    foreach ($p['varian'] as $v) {
        if ($v['id'] === (int)$varian_id) return $v;
    }
    return null;
}

/** Alamat foto produk (kosong kalau filenya tidak ada). */
function foto_produk($p) {
    $f = $p['foto'] ?? '';
    if ($f !== '' && preg_match('#^assets/img/produk/[A-Za-z0-9_.\-]+$#', $f) && file_exists(dirname(__DIR__) . '/' . $f)) {
        return $f;
    }
    return '';
}

/** Gambar produk berbentuk persegi. Kalau fotonya belum ada, tampil kotak polos. */
function produk_thumb($p, $href = '', $class = 'thumb') {
    $foto = foto_produk($p);
    $kosong = $foto === '' ? ' kosong' : '';
    if ($href !== '') {
        $img = $foto !== '' ? '<img src="' . e($foto) . '" alt="" loading="lazy">' : '';
        return '<a href="' . e($href) . '" class="' . $class . $kosong . '" aria-label="Lihat ' . e($p['name']) . '">' . $img . '</a>';
    }
    $img = $foto !== '' ? '<img src="' . e($foto) . '" alt="' . e($p['name']) . '">' : '';
    return '<div class="' . $class . $kosong . '"' . ($foto === '' ? ' role="img" aria-label="Foto ' . e($p['name']) . '"' : '') . '>' . $img . '</div>';
}

/** Teks harga untuk kartu: "Mulai Rp..." kalau varian berbeda harga. */
function harga_tampil($p) {
    $h = array_unique(array_column($p['varian'], 'harga'));
    return (count($h) > 1 ? 'Mulai ' : '') . format_rupiah($p['price']);
}

function format_rupiah($angka) {
    return 'Rp' . number_format((int)$angka, 0, ',', '.');
}
