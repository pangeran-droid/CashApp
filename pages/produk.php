<?php
// pages/produk.php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../auth/login.php"); exit; }
require_once __DIR__ . '/../config/koneksi.php';

// NOTIF helper via session
function set_notif($type, $msg) {
    $_SESSION['notif'] = ['type'=>$type,'msg'=>$msg];
}

// CREATE
if (isset($_POST['create_produk'])) {
    $kode = $koneksi->real_escape_string($_POST['kode_produk']);
    $nama = $koneksi->real_escape_string($_POST['nama_produk']);
    $kategori = $koneksi->real_escape_string($_POST['kategori']);
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];
    $satuan = $koneksi->real_escape_string($_POST['satuan']);

    $stmt = $koneksi->prepare("INSERT INTO produk (kode_produk,nama_produk,kategori,harga_beli,harga_jual,stok,satuan) VALUES(?,?,?,?,?,?,?)");
    $stmt->bind_param("sssddis", $kode, $nama, $kategori, $harga_beli, $harga_jual, $stok, $satuan);

    if ($stmt->execute()) set_notif('success','Produk ditambahkan');
    else set_notif('error','Gagal menambahkan produk');

    $stmt->close();
    header("Location: produk.php");
    exit;
}

// UPDATE
if (isset($_POST['update_produk'])) {
    $id = (int)$_POST['id_produk'];
    $kode = $koneksi->real_escape_string($_POST['kode_produk']);
    $nama = $koneksi->real_escape_string($_POST['nama_produk']);
    $kategori = $koneksi->real_escape_string($_POST['kategori']);
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];
    $satuan = $koneksi->real_escape_string($_POST['satuan']);

    $stmt = $koneksi->prepare("UPDATE produk SET kode_produk=?, nama_produk=?, kategori=?, harga_beli=?, harga_jual=?, stok=?, satuan=? WHERE id_produk=?");
    $stmt->bind_param("sssddisi", $kode, $nama, $kategori, $harga_beli, $harga_jual, $stok, $satuan, $id);

    if ($stmt->execute()) set_notif('success','Produk diperbarui');
    else set_notif('error','Gagal memperbarui produk');

    $stmt->close();
    header("Location: produk.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
    $stmt->bind_param("i",$id);
    if ($stmt->execute()) set_notif('success','Produk dihapus');
    else set_notif('error','Gagal menghapus produk');
    $stmt->close();
    header("Location: produk.php");
    exit;
}

// PAGINATION + SEARCH
$limit = 10;
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$start = ($page-1)*$limit;
$search = isset($_GET['q']) ? $koneksi->real_escape_string($_GET['q']) : '';

$where = "";
if ($search != '') {
    $where = "WHERE kode_produk LIKE '%$search%' OR nama_produk LIKE '%$search%'";
}
$totalRes = $koneksi->query("SELECT COUNT(*) AS cnt FROM produk $where");
$total = $totalRes->fetch_assoc()['cnt'] ?? 0;
$pages = max(1, ceil($total / $limit));
$res = $koneksi->query("SELECT * FROM produk $where ORDER BY id_produk DESC LIMIT $start,$limit");
$rows = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Produk - CashApp</title>
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
      <a class="btn btn-outline-light btn-sm" href="kasir.php">Kasir</a>
      <a class="btn btn-danger btn-sm" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="d-flex justify-content-between mb-3">
    <h4>Manajemen Produk</h4>
    <div>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">Tambah Produk</button>
    </div>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Cari kode atau nama...">
      <button class="btn btn-outline-secondary" type="submit">Cari</button>
    </div>
  </form>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-success">
          <tr><th>#</th><th>Kode</th><th>Nama</th><th>Kategori</th><th>Harga Beli</th><th>Harga Jual</th><th>Stok</th><th>Satuan</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (count($rows)): $no=$start+1; foreach($rows as $r): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['kode_produk']) ?></td>
              <td><?= htmlspecialchars($r['nama_produk']) ?></td>
              <td><?= $r['kategori'] ?></td>
              <td>Rp <?= number_format($r['harga_beli'],0,',','.') ?></td>
              <td>Rp <?= number_format($r['harga_jual'],0,',','.') ?></td>
              <td><?= (int)$r['stok'] ?></td>
              <td><?= $r['satuan'] ?></td>
              <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $r['id_produk'] ?>">Edit</button>
                <a href="?delete=<?= $r['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="9" class="text-center text-muted">Belum ada produk</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- pagination -->
  <nav class="mt-3">
    <ul class="pagination justify-content-center">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a></li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<!-- Modal Create -->
<div class="modal fade" id="modalCreate" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header bg-success text-white"><h5 class="modal-title">Tambah Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label>Kode</label><input name="kode_produk" class="form-control" required></div>
        <div class="mb-2"><label>Nama</label><input name="nama_produk" class="form-control" required></div>
        <div class="mb-2">
          <label>Kategori</label>
          <select name="kategori" class="form-select" required>
            <option value="Makanan">Makanan</option>
            <option value="Minuman">Minuman</option>
            <option value="Cemilan">Cemilan</option>
          </select>
        </div>
        <div class="mb-2 row">
          <div class="col"><label>Harga Beli</label><input type="number" step="0.01" name="harga_beli" class="form-control" required></div>
          <div class="col"><label>Harga Jual</label><input type="number" step="0.01" name="harga_jual" class="form-control" required></div>
        </div>
        <div class="mb-2 row">
          <div class="col"><label>Stok</label><input type="number" name="stok" class="form-control" required></div>
          <div class="col"><label>Satuan</label>
            <select name="satuan" class="form-select" required><option value="pcs">pcs</option><option value="paket">paket</option></select>
          </div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-success" name="create_produk">Simpan</button></div>
    </form>
  </div>
</div>

<!-- Modal Edit per produk -->
<?php foreach($rows as $r): ?>
<div class="modal fade" id="modalEdit<?= $r['id_produk'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header bg-warning"><h5 class="modal-title">Edit Produk</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id_produk" value="<?= $r['id_produk'] ?>">
        <div class="mb-2"><label>Kode</label><input name="kode_produk" class="form-control" value="<?= htmlspecialchars($r['kode_produk']) ?>" required></div>
        <div class="mb-2"><label>Nama</label><input name="nama_produk" class="form-control" value="<?= htmlspecialchars($r['nama_produk']) ?>" required></div>
        <div class="mb-2"><label>Kategori</label>
          <select name="kategori" class="form-select" required>
            <option value="Makanan" <?= $r['kategori']=='Makanan'?'selected':'' ?>>Makanan</option>
            <option value="Minuman" <?= $r['kategori']=='Minuman'?'selected':'' ?>>Minuman</option>
            <option value="Cemilan" <?= $r['kategori']=='Cemilan'?'selected':'' ?>>Cemilan</option>
          </select>
        </div>
        <div class="mb-2 row">
          <div class="col"><label>Harga Beli</label><input type="number" step="0.01" name="harga_beli" class="form-control" value="<?= $r['harga_beli'] ?>" required></div>
          <div class="col"><label>Harga Jual</label><input type="number" step="0.01" name="harga_jual" class="form-control" value="<?= $r['harga_jual'] ?>" required></div>
        </div>
        <div class="mb-2 row">
          <div class="col"><label>Stok</label><input type="number" name="stok" class="form-control" value="<?= $r['stok'] ?>" required></div>
          <div class="col"><label>Satuan</label>
            <select name="satuan" class="form-select" required>
              <option value="pcs" <?= $r['satuan']=='pcs'?'selected':'' ?>>pcs</option>
              <option value="paket" <?= $r['satuan']=='paket'?'selected':'' ?>>paket</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-warning" name="update_produk">Update</button></div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // show notif SweetAlert if any
  <?php if (!empty($_SESSION['notif'])): $n = $_SESSION['notif']; unset($_SESSION['notif']); ?>
    (function(){
      const t = <?= json_encode($n['type']) ?>;
      const m = <?= json_encode($n['msg']) ?>;
      if (t === 'success') Swal.fire({icon:'success', title:'Sukses', text:m, timer:1600, showConfirmButton:false});
      else Swal.fire({icon:'error', title:'Error', text:m});
    })();
  <?php endif; ?>
</script>
</body>
</html>
