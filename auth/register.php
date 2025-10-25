<?php
// auth/register.php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if ($username == '' || $email == '' || $pass == '') {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: register.php");
        exit;
    }

    $stmt = $koneksi->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Username atau email sudah terdaftar.";
        $stmt->close();
        header("Location: register.php");
        exit;
    }
    $stmt->close();

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $koneksi->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hash);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
    } else {
        $_SESSION['error'] = "Gagal registrasi.";
    }
    $stmt->close();
    header("Location: register.php");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register - CashApp</title>
<link rel="icon" href="../assets/img/favicon.png" type="image/x-icon" title="Website Favicon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:480px;">
  <div class="card shadow">
    <div class="card-body">
      <h4 class="mb-3 text-success">CashApp - Register</h4>

      <?php if(!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); endif; ?>
      <?php if(!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); endif; ?>

      <form method="post">
        <div class="mb-2"><input class="form-control" name="username" placeholder="Username" required></div>
        <div class="mb-2"><input class="form-control" type="email" name="email" placeholder="Email" required></div>
        <div class="mb-2"><input class="form-control" type="password" name="password" placeholder="Password" required></div>
        <div class="d-grid gap-2">
          <button class="btn btn-success" name="register">Daftar</button>
          <a class="btn btn-outline-secondary" href="login.php">Sudah punya akun? Login</a>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
