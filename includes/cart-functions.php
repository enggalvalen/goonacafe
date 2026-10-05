<?php
/**
 * Keranjang berbasis PHP session.
 * Struktur: $_SESSION['cart']["{id_produk}:{id_varian}"] = ['qty' => int, 'note' => string]
 * (id_varian = 0 untuk produk tanpa varian). Harga selalu dihitung ulang dari data produk.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/products-data.php';

function cart_get() {
    return isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function cart_count() {
    $n = 0;
    foreach (cart_get() as $row) $n += (int)$row['qty'];
    return $n;
}

function cart_key($id, $varian_id) {
    return (int)$id . ':' . (int)$varian_id;
}

/** Tambah ke keranjang. Mengembalikan false kalau produk/varian tidak valid. */
function cart_add($id, $varian_id, $qty, $note = '') {
    $p = find_product($id);
    if (!$p) return false;
    if ($p['varian']) {
        if (!find_varian($p, $varian_id)) $varian_id = $p['varian'][0]['id'];
    } else {
        $varian_id = 0;
    }
    $key  = cart_key($p['id'], $varian_id);
    $qty  = max(1, min(99, (int)$qty));
    $note = potong(trim($note), 120);
    $cart = cart_get();
    if (isset($cart[$key])) {
        $cart[$key]['qty'] = min(99, $cart[$key]['qty'] + $qty);
        if ($note !== '') $cart[$key]['note'] = $note;
    } else {
        $cart[$key] = ['qty' => $qty, 'note' => $note];
    }
    $_SESSION['cart'] = $cart;
    return true;
}

function cart_change($key, $delta) {
    $cart = cart_get();
    if (!isset($cart[$key])) return;
    $cart[$key]['qty'] = max(1, min(99, $cart[$key]['qty'] + (int)$delta));
    $_SESSION['cart'] = $cart;
}

function cart_remove($key) {
    $cart = cart_get();
    unset($cart[$key]);
    $_SESSION['cart'] = $cart;
}

function cart_clear() {
    $_SESSION['cart'] = [];
}

/** Daftar item lengkap: produk, varian, harga satuan, qty, subtotal. */
function cart_items() {
    $items = [];
    foreach (cart_get() as $key => $row) {
        [$id, $vid] = array_map('intval', explode(':', $key . ':0'));
        $p = find_product($id);
        if (!$p) continue;
        $v = null;
        if ($p['varian']) {
            $v = find_varian($p, $vid);
            if (!$v) continue;
        }
        $harga = $v ? $v['harga'] : $p['price'];
        $items[] = [
            'key' => $key, 'p' => $p, 'varian' => $v, 'harga' => $harga,
            'qty' => (int)$row['qty'], 'note' => $row['note'] ?? '', 'sub' => $harga * (int)$row['qty'],
        ];
    }
    return $items;
}

function cart_total() {
    $t = 0;
    foreach (cart_items() as $it) $t += $it['sub'];
    return $t;
}

/** Nama produk + varian, mis. "Americano (Ice)". */
function item_label($it) {
    return $it['p']['name'] . ($it['varian'] ? ' (' . $it['varian']['nama'] . ')' : '');
}

/** Hanya izinkan redirect ke halaman .php lokal. */
function safe_redirect($to) {
    if (is_string($to) && preg_match('/^[a-z0-9\-]+\.php(\?[a-zA-Z0-9=&_\-]*)?(#[a-zA-Z0-9_\-]*)?$/', $to)) {
        return $to;
    }
    return 'cart.php';
}

function with_param($url, $key, $value) {
    $hash = '';
    if (($pos = strpos($url, '#')) !== false) {
        $hash = substr($url, $pos);
        $url  = substr($url, 0, $pos);
    }
    return $url . (strpos($url, '?') === false ? '?' : '&') . $key . '=' . urlencode($value) . $hash;
}
