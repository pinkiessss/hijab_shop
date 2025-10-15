<?php
require_once 'config.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();
if (!$product) {
  http_response_code(404);
  echo "Produk tidak ditemukan.";
  exit;
}

// Tambah ke cart
check_csrf();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  $qty = max(1, intval($_POST['qty'] ?? 1));
  $pid = $product['id'];
  if (!isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
  $_SESSION['cart'][$pid] += $qty;
  header('Location: cart.php');
  exit;
}

$cart_count = array_sum($_SESSION['cart']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($product['name']); ?> — Hijab Shop</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container nav">
  <div class="brand">Hijab Shop</div>
  <div>
    <a href="index.php">Home</a>
    <a href="cart.php" class="cart-pill">🧺 Keranjang (<?php echo $cart_count; ?>)</a>
  </div>
</div>

<main class="container">
  <div class="card" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
    <img src="<?php echo htmlspecialchars($product['image'] ?: 'assets/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <div>
      <h1><?php echo htmlspecialchars($product['name']); ?></h1>
      <div class="badge">Stok: <?php echo (int)$product['stock']; ?></div>
      <p style="margin:10px 0 14px;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      <div class="price" style="font-size:22px;"><?php echo rupiah($product['price']); ?></div>

      <form method="post" class="form" style="max-width:260px;">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Jumlah</label>
        <input class="input" type="number" min="1" value="1" name="qty">
        <button class="btn" name="add_to_cart">Tambah ke Keranjang</button>
      </form>
    </div>
  </div>
</main>

<div class="container footer">© <?php echo date('Y'); ?> Hijab Shop</div>
</body>
</html>
