<?php
/**
 * Penjaga halaman install.php dan admin/setup.php.
 * Boleh dibuka kalau (1) dari komputer lokal, atau (2) membawa kode rahasia yang cocok dengan
 * SETUP_KODE di includes/db-local.php, mis. install.php?kode=KODE_KAMU
 */
function setup_lokal() {
    return in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
}

function setup_kode_dikirim() {
    $k = $_GET['kode'] ?? '';
    return is_string($k) ? $k : '';
}

function setup_boleh() {
    if (setup_lokal()) return true;
    if (!defined('SETUP_KODE') || strlen((string)SETUP_KODE) < 8) return false;
    return hash_equals((string)SETUP_KODE, setup_kode_dikirim());
}

/** Tambahan query string untuk tautan ke halaman setup berikutnya (kosong kalau akses lokal). */
function setup_query() {
    return setup_lokal() ? '' : '?kode=' . rawurlencode(setup_kode_dikirim());
}
