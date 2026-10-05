<?php
// Memproses aksi keranjang (add / inc / dec / remove), lalu redirect (atau JSON bila lewat fetch).
require_once __DIR__ . '/includes/cart-functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$ajax   = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
$action = $_POST['action'] ?? '';
$id     = (int)($_POST['id'] ?? 0);
$key    = (string)($_POST['key'] ?? '');
$ok     = true;

switch ($action) {
    case 'add':
        $ok = cart_add($id, (int)($_POST['varian'] ?? 0), (int)($_POST['qty'] ?? 1), (string)($_POST['note'] ?? ''));
        break;
    case 'inc':    cart_change($key, 1);  break;
    case 'dec':    cart_change($key, -1); break;
    case 'remove': cart_remove($key);     break;
}

if ($ajax) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok, 'count' => cart_count()]);
    exit;
}

$to = safe_redirect($_POST['redirect'] ?? 'cart.php');
if ($action === 'add' && $ok && strpos($to, 'produk.php') === 0) {
    $to = with_param($to, 'added', (string)$id);
}
header('Location: ' . $to);
exit;
