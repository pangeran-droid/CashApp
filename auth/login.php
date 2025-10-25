<?php
// auth/login.php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hash);
        $stmt->fetch();
        if (password_verify($pass, $hash)) {
            $_SESSION['login'] = true;
            $_SESSION['userid'] = $id;
            $_SESSION['username'] = $username;
            header("Location: ../pages/dashboard.php");
            exit;
        }
    }
    $_SESSION['error'] = "Email atau password salah.";
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - CashApp</title>
<link rel="icon" href="../assets/img/favicon.png" type="image/x-icon" title="Website Favicon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:420px;">
  <div class="card shadow">
    <div class="card-body">
      <h4 class="mb-3 text-success">CashApp - Login</h4>
      <?php if(!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); endif; ?>
      <form method="post">
        <div class="mb-2"><input class="form-control" name="email" placeholder="Email" required></div>
        <div class="mb-2"><input class="form-control" type="password" name="password" placeholder="Password" required></div>
        <div class="d-grid gap-2">
          <button class="btn btn-success" name="login">Masuk</button>
          <a class="btn btn-outline-secondary" href="register.php">Belum punya akun?</a>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
