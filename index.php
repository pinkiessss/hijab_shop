<?php
require_once 'config.php';

// Ambil semua produk
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();

$cart_count = array_sum($_SESSION['cart']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hijab Shop — Katalog</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="hero">
  <div class="container nav">
    <div class="brand">Hijab Shop</div>
    <div>
      <a href="index.php">Home</a>
      <a href="cart.php" class="cart-pill">🧺 Keranjang (<?php echo $cart_count; ?>)</a>
    </div>
  </div>
  <div class="container">
    <h1>Koleksi Hijab Terbaru</h1>
    <p>Material premium, nyaman dipakai, dan gaya kekinian.</p>
  </div>
</header>

<main class="container" style="margin-top:18px;">
  <div class="grid">
    <?php foreach ($products as $p): ?>
    <div class="card">
      <img src="<?php echo htmlspecialchars($p['image'] ?: 'assets/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
      <h3><?php echo htmlspecialchars($p['name']); ?></h3>
      <div class="price"><?php echo rupiah($p['price']); ?></div>
      <div style="margin-top:auto;">
        <a class="btn" href="product.php?slug=<?php echo urlencode($p['slug']); ?>">Lihat Detail</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<footer class="footer">
  © <?php echo date('Y'); ?> <span class="footer-brand">Hijab Shop</span>
</footer>
</body>
</html>
