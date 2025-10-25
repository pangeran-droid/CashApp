<?php
// pages/laporan.php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../auth/login.php"); exit; }
require_once __DIR__ . '/../config/koneksi.php';

$tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('-7 days'));
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');

$query = $koneksi->prepare("
  SELECT j.tanggal_beli, r.nama_produk, r.harga_jual, r.qty, r.total_harga
  FROM rinci_jual r JOIN jual j ON r.no_faktur = j.no_faktur
  WHERE DATE(j.tanggal_beli) BETWEEN ? AND ?
  ORDER BY j.tanggal_beli DESC
");
$query->bind_param("ss", $tanggal_awal, $tanggal_akhir);
$query->execute();
$result = $query->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$query->close();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Laporan - CashApp</title>
<link rel="icon" href="../assets/img/favicon.png" type="image/x-icon" title="Website Favicon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar bg-pos p-2"><div class="container"><a class="navbar-brand text-white" href="dashboard.php">CashApp</a><a class="btn btn-outline-light btn-sm" href="../auth/logout.php">Logout</a></div></nav>

<div class="container my-4">
  <h4>Laporan Penjualan</h4>
  <form method="get" class="row g-2 align-items-end">
    <div class="col-md-3">
      <label>Tanggal Awal</label>
      <input type="date" name="tanggal_awal" class="form-control" value="<?= htmlspecialchars($tanggal_awal) ?>">
    </div>
    <div class="col-md-3">
      <label>Tanggal Akhir</label>
      <input type="date" name="tanggal_akhir" class="form-control" value="<?= htmlspecialchars($tanggal_akhir) ?>">
    </div>
    <div class="col-md-2">
      <button class="btn btn-success">Tampilkan</button>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <a class="btn btn-danger" href="../export_laporan_pdf.php?tanggal_awal=<?= $tanggal_awal ?>&tanggal_akhir=<?= $tanggal_akhir ?>">Export PDF</a>
      <a class="btn btn-success" href="../export_laporan_excel.php?tanggal_awal=<?= $tanggal_awal ?>&tanggal_akhir=<?= $tanggal_akhir ?>">Export Excel</a>
    </div>
  </form>

  <div class="card mt-3">
    <div class="card-body table-responsive">
      <table class="table table-bordered">
        <thead><tr><th>Tanggal</th><th>Nama Produk</th><th>Harga Jual</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
          <?php if ($rows): foreach($rows as $r): ?>
            <tr>
              <td><?= date('d-m-Y H:i', strtotime($r['tanggal_beli'])) ?></td>
              <td><?= htmlspecialchars($r['nama_produk']) ?></td>
              <td>Rp <?= number_format($r['harga_jual'],0,',','.') ?></td>
              <td><?= $r['qty'] ?></td>
              <td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
