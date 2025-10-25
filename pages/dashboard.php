<?php
// pages/dashboard.php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../auth/login.php"); exit; }
require_once __DIR__ . '/../config/koneksi.php';
$username = htmlspecialchars($_SESSION['username']);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard - CashApp</title>
<link rel="icon" href="../assets/img/favicon.png" type="image/x-icon" title="Website Favicon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>.bg-positif{background:#009688;color:#fff}</style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-positif p-2">
  <div class="container">
    <a class="navbar-brand text-white" href="#">CashApp</a>
    <div class="d-flex align-items-center">
      <span class="me-3">Halo, <?= $username; ?></span>
      <a class="btn btn-outline-light btn-sm me-2" href="produk.php">Produk</a>
      <a class="btn btn-outline-light btn-sm me-2" href="kasir.php">Kasir</a>
      <a class="btn btn-outline-light btn-sm me-2" href="laporan.php">Laporan</a>
      <a class="btn btn-danger btn-sm" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Total Produk</h5>
          <?php
          $res = $koneksi->query("SELECT COUNT(*) AS cnt FROM produk");
          $cnt = $res->fetch_assoc()['cnt'] ?? 0;
          ?>
          <h2 class="text-success"><?= $cnt ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Transaksi Hari Ini</h5>
          <?php
          $today = date('Y-m-d');
          $res = $koneksi->query("SELECT COUNT(*) AS cnt FROM jual WHERE DATE(tanggal_beli) = '$today'");
          $cnt = $res->fetch_assoc()['cnt'] ?? 0;
          ?>
          <h2 class="text-success"><?= $cnt ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Omzet Hari Ini</h5>
          <?php
          $res = $koneksi->query("SELECT IFNULL(SUM(total_belanja),0) AS tot FROM jual WHERE DATE(tanggal_beli) = '$today'");
          $tot = $res->fetch_assoc()['tot'] ?? 0;
          ?>
          <h2 class="text-success">Rp <?= number_format($tot,0,',','.') ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
