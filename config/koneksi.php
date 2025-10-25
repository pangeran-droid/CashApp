<?php
// config/koneksi.php
$DB_HOST = "";
$DB_USER = "";
$DB_PASS = "";
$DB_NAME = "";

$koneksi = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
$koneksi->set_charset("utf8mb4");
