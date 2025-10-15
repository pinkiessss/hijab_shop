<?php
require_once '../config.php';

// sederhana: hardcoded. Untuk produksi, gunakan tabel users + hash password.
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'admin123';

check_csrf();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  if ($u === $ADMIN_USER && $p === $ADMIN_PASS) {
    $_SESSION['admin'] = true;
    header('Location: products.php');
    exit;
  } else $error = 'Username atau password salah';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin — Hijab Shop</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container" style="max-width:420px; margin-top:60px;">
  <div class="card">
    <h2>Login Admin</h2>
    <?php if ($error): ?>
      <div style="color:#b91c1c; margin-bottom:8px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" class="form">
      <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
      <label>Username</label>
      <input class="input" name="username">
      <label>Password</label>
      <input class="input" type="password" name="password">
      <button class="btn" style="margin-top:6px;">Masuk</button>
    </form>
  </div>
</div>
</body>
</html>
