<?php
// export_laporan_excel.php
require 'vendor/autoload.php'; // phpoffice/phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1','Tanggal');
$sheet->setCellValue('B1','Nama Produk');
$sheet->setCellValue('C1','Harga Jual');
$sheet->setCellValue('D1','Qty');
$sheet->setCellValue('E1','Total Harga');

$rowNum = 2;
$totalAll = 0;
while($r = $res->fetch_assoc()){
  $sheet->setCellValue('A'.$rowNum, date('d-m-Y H:i',strtotime($r['tanggal_beli'])));
  $sheet->setCellValue('B'.$rowNum, $r['nama_produk']);
  $sheet->setCellValue('C'.$rowNum, $r['harga_jual']);
  $sheet->setCellValue('D'.$rowNum, $r['qty']);
  $sheet->setCellValue('E'.$rowNum, $r['total_harga']);
  $totalAll += $r['total_harga'];
  $rowNum++;
}

$sheet->setCellValue('D'.$rowNum,'Total');
$sheet->setCellValue('E'.$rowNum,$totalAll);
foreach(range('A','E') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

$writer = new Xlsx($spreadsheet);
$filename = "Laporan_{$tanggal_awal}_sd_{$tanggal_akhir}.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer->save('php://output');
exit;
