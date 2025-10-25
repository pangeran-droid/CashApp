<?php
// pages/kasir.php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../auth/login.php"); exit; }
require_once __DIR__ . '/../config/koneksi.php';

// init cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// add to cart via POST
if (isset($_POST['add_to_cart'])) {
    $kode = $_POST['kode_produk'];
    // fetch product
    $stmt = $koneksi->prepare("SELECT kode_produk,nama_produk,harga_beli,harga_jual,stok FROM produk WHERE kode_produk = ?");
    $stmt->bind_param("s",$kode);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$res) {
        $_SESSION['notif'] = ['type'=>'error','msg'=>'Produk tidak ditemukan'];
        header("Location: kasir.php"); exit;
    }
    // check stock
    if ($res['stok'] <= 0) {
        $_SESSION['notif'] = ['type'=>'error','msg'=>'Stok kosong'];
        header("Location: kasir.php"); exit;
    }

    if (isset($_SESSION['cart'][$kode])) {
        $_SESSION['cart'][$kode]['qty'] += 1;
        $_SESSION['cart'][$kode]['total_harga'] = $_SESSION['cart'][$kode]['qty'] * $res['harga_jual'];
    } else {
        $_SESSION['cart'][$kode] = [
            'kode_produk'=>$res['kode_produk'],
            'nama_produk'=>$res['nama_produk'],
            'harga_modal'=>$res['harga_beli'],
            'harga_jual'=>$res['harga_jual'],
            'qty'=>1,
            'total_harga'=>$res['harga_jual']
        ];
    }
    header("Location: kasir.php"); exit;
}

// remove item
if (isset($_POST['remove_item'])) {
    $kode = $_POST['remove_item'];
    unset($_SESSION['cart'][$kode]);
    header("Location: kasir.php"); exit;
}

// clear cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: kasir.php"); exit;
}

// pay
if (isset($_POST['bayar'])) {
    if (empty($_SESSION['cart'])) {
        $_SESSION['notif'] = ['type'=>'error','msg'=>'Keranjang kosong'];
        header("Location: kasir.php"); exit;
    }
    $total_belanja = 0;
    foreach($_SESSION['cart'] as $it) $total_belanja += $it['total_harga'];
    $total_bayar = (float)$_POST['total_bayar'];
    if ($total_bayar < $total_belanja) {
        $_SESSION['notif'] = ['type'=>'error','msg'=>'Uang bayar kurang'];
        header("Location: kasir.php"); exit;
    }
    $kembalian = $total_bayar - $total_belanja;
    $no_faktur = 'FKT-'.date('YmdHis');

    // insert jual
    $stmt = $koneksi->prepare("INSERT INTO jual (no_faktur,total_belanja,total_bayar,kembalian) VALUES (?,?,?,?)");
    $stmt->bind_param("sddd", $no_faktur, $total_belanja, $total_bayar, $kembalian);
    $stmt->execute();
    $stmt->close();

    // insert rinci and update stok
    foreach($_SESSION['cart'] as $it) {
        $kodep = $it['kode_produk'];
        $nama = $it['nama_produk'];
        $hm = $it['harga_modal'];
        $hj = $it['harga_jual'];
        $qty = (int)$it['qty'];
        $total_h = $it['total_harga'];
        $untung = ($hj - $hm) * $qty;

        $stmt = $koneksi->prepare("INSERT INTO rinci_jual (no_faktur,kode_produk,nama_produk,harga_modal,harga_jual,qty,total_harga,untung) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssddidd", $no_faktur, $kodep, $nama, $hm, $hj, $qty, $total_h, $untung);
        $stmt->execute();
        $stmt->close();

        // update stok
        $koneksi->query("UPDATE produk SET stok = stok - $qty WHERE kode_produk = '".$koneksi->real_escape_string($kodep)."'");
    }

    $_SESSION['cart'] = [];
    $_SESSION['notif'] = ['type'=>'success','msg'=>"Transaksi sukses (No: $no_faktur). Kembalian Rp ".number_format($kembalian,0,',','.')];
    header("Location: kasir.php"); exit;
}

// fetch product list paginated
$limit = 10;
$page = isset($_GET['page'])?max(1,(int)$_GET['page']):1;
$start = ($page-1)*$limit;
$totalRes = $koneksi->query("SELECT COUNT(*) AS cnt FROM produk");
$total = $totalRes->fetch_assoc()['cnt'] ?? 0;
$pages = max(1, ceil($total/$limit));
$res = $koneksi->query("SELECT * FROM produk ORDER BY id_produk DESC LIMIT $start,$limit");
$products = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kasir - CashApp</title>
<link rel="icon" href="../assets/img/favicon.png" type="image/x-icon" title="Website Favicon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>.bg-pos{background:#009688;color:#fff}</style>
</head>
<body>
<nav class="navbar bg-pos p-2">
  <div class="container">
    <a class="navbar-brand text-white" href="dashboard.php">CashApp</a>
    <div>
      <a class="btn btn-outline-light btn-sm" href="produk.php">Produk</a>
      <a class="btn btn-danger btn-sm" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="row g-3">
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white">Daftar Produk</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead><tr><th>#</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php if ($products): $no=$start+1; foreach($products as $p): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                    <td>Rp <?= number_format($p['harga_jual'],0,',','.') ?></td>
                    <td><?= (int)$p['stok'] ?></td>
                    <td>
                      <form method="post" style="display:inline">
                        <input type="hidden" name="kode_produk" value="<?= htmlspecialchars($p['kode_produk']) ?>">
                        <button class="btn btn-sm btn-success" name="add_to_cart">Add</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="5" class="text-center">Tidak ada produk</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- pagination -->
          <nav><ul class="pagination">
            <?php for($i=1;$i<=$pages;$i++): ?>
              <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
          </ul></nav>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white">Keranjang</div>
        <div class="card-body">
          <form method="post">
            <table class="table">
              <thead><tr><th>Nama</th><th>Harga</th><th>Qty</th><th>Total</th><th></th></tr></thead>
              <tbody>
                <?php $total=0; if(!empty($_SESSION['cart'])): foreach($_SESSION['cart'] as $key=>$it): $total += $it['total_harga']; ?>
                  <tr>
                    <td><?= htmlspecialchars($it['nama_produk']) ?></td>
                    <td>Rp <?= number_format($it['harga_jual'],0,',','.') ?></td>
                    <td><?= $it['qty'] ?></td>
                    <td>Rp <?= number_format($it['total_harga'],0,',','.') ?></td>
                    <td>
                      <button name="remove_item" value="<?= htmlspecialchars($key) ?>" class="btn btn-sm btn-danger">x</button>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="5" class="text-center">Keranjang kosong</td></tr>
                <?php endif; ?>
              </tbody>
            </table>

            <div class="mb-2">
              <label>Total Belanja</label>
              <input id="total_belanja" name="total_belanja" class="form-control" value="<?= $total ?>" readonly>
            </div>
            <div class="mb-2">
              <label>Total Bayar</label>
              <input id="total_bayar" type="number" step="0.01" name="total_bayar" class="form-control" required>
            </div>
            <div class="mb-2">
              <label>Kembalian</label>
              <input id="kembalian" name="kembalian" class="form-control" readonly>
            </div>

            <div class="d-grid gap-2">
              <button class="btn btn-primary" name="bayar">Bayar</button>
              <button class="btn btn-outline-danger" name="clear_cart" type="submit">Batalkan Semua</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('total_bayar')?.addEventListener('input', function(){
    let total = parseFloat(document.getElementById('total_belanja').value) || 0;
    let bayar = parseFloat(this.value) || 0;
    let kembali = bayar - total;
    document.getElementById('kembalian').value = kembali > 0 ? kembali : 0;
  });
</script>

<?php if (!empty($_SESSION['notif'])): $n = $_SESSION['notif']; unset($_SESSION['notif']); ?>
<script>
  Swal.fire({ icon: <?= json_encode($n['type']=='success' ? 'success' : 'error') ?>, title: <?= json_encode(ucfirst($n['type'])) ?>, text: <?= json_encode($n['msg']) ?>, timer: 2000, showConfirmButton:false });
</script>
<?php endif; ?>
</body>
</html>
