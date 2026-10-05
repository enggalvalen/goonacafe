<?php
require_once __DIR__ . '/../includes/admin-auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $c = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $c['path'], $c['domain'] ?? '', $c['secure'] ?? false, true);
    }
    session_destroy();
}
header('Location: login.php');
exit;
