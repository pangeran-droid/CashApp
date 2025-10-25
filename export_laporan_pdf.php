<?php
// export_laporan_pdf.php
require 'vendor/autoload.php'; // make sure composer install dompdf/dompdf
use Dompdf\Dompdf;
require_once __DIR__ . '/config/koneksi.php';

$tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('-7 days'));
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');

$stmt = $koneksi->prepare("
  SELECT j.tanggal_beli, r.nama_produk, r.harga_jual, r.qty, r.total_harga
  FROM rinci_jual r JOIN jual j ON r.no_faktur = j.no_faktur
  WHERE DATE(j.tanggal_beli) BETWEEN ? AND ? ORDER BY j.tanggal_beli DESC
");
$stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
$stmt->execute();
$res = $stmt->get_result();

$html = '<h3 style="text-align:center;">Laporan Penjualan</h3>';
$html .= '<p style="text-align:center;">Periode: '.date('d-m-Y',strtotime($tanggal_awal)).' s/d '.date('d-m-Y',strtotime($tanggal_akhir)).'</p>';
$html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%"><thead><tr><th>Tanggal</th><th>Produk</th><th>Harga Jual</th><th>Qty</th><th>Total</th></tr></thead><tbody>';
$totalAll = 0;
while($r = $res->fetch_assoc()){
  $html .= '<tr><td>'.date('d-m-Y H:i',strtotime($r['tanggal_beli'])).'</td><td>'.htmlspecialchars($r['nama_produk']).'</td><td>Rp '.number_format($r['harga_jual'],0,',','.').'</td><td>'.$r['qty'].'</td><td>Rp '.number_format($r['total_harga'],0,',','.').'</td></tr>';
  $totalAll += $r['total_harga'];
}
$html .= '<tr><td colspan="4" style="text-align:right;font-weight:bold">Total</td><td>Rp '.number_format($totalAll,0,',','.').'</td></tr>';
$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("Laporan_{$tanggal_awal}_sd_{$tanggal_akhir}.pdf", ["Attachment" => true]);
exit;
