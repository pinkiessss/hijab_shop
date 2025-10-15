<?php
require_once 'config.php';
check_csrf();

// Actions: add/remove/update via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['remove'])) {
    $pid = (int)$_POST['pid'];
    unset($_SESSION['cart'][$pid]);
  }
  if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
      $qty = max(0, (int)$qty);
      if ($qty == 0) unset($_SESSION['cart'][$pid]);
      else $_SESSION['cart'][$pid] = $qty;
    }
  }
  header('Location: cart.php');
  exit;
}

// Fetch items detail
$items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
  $ids = array_keys($_SESSION['cart']);
  $in  = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
  $stmt->execute($ids);
  $rows = $stmt->fetchAll();
  foreach ($rows as $r) {
    $qty = $_SESSION['cart'][$r['id']] ?? 0;
    $sub = $qty * $r['price'];
    $total += $sub;
    $items[] = ['p' => $r, 'qty' => $qty, 'sub' => $sub];
  }
}

$cart_count = array_sum($_SESSION['cart']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Keranjang — Hijab Shop</title>
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
  <h1>Keranjang Belanja</h1>
  <?php if (empty($items)): ?>
    <p>Keranjang Anda kosong. <a href="index.php">Belanja sekarang →</a></p>
  <?php else: ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <table class="table">
      <thead>
        <tr>
          <th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): ?>
        <tr>
          <td><?php echo htmlspecialchars($it['p']['name']); ?></td>
          <td><?php echo rupiah($it['p']['price']); ?></td>
          <td style="max-width:90px;">
            <input class="input" type="number" name="qty[<?php echo $it['p']['id']; ?>]" value="<?php echo $it['qty']; ?>" min="0">
          </td>
          <td><?php echo rupiah($it['sub']); ?></td>
          <td>
            <button class="btn btn-danger" name="remove" value="1" onclick="return confirm('Hapus item ini?');">Hapus</button>
            <input type="hidden" name="pid" value="<?php echo $it['p']['id']; ?>">
          </td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="3" style="text-align:right; font-weight:700;">Total</td>
          <td colspan="2" style="font-weight:700;"><?php echo rupiah($total); ?></td>
        </tr>
      </tbody>
    </table>
    <div style="margin-top:12px; display:flex; gap:10px; justify-content:flex-end;">
      <button class="btn btn-outline" name="update" value="1">Update Keranjang</button>
      <a class="btn" href="checkout.php">Lanjut ke Checkout</a>
    </div>
  </form>
  <?php endif; ?>
</main>

<div class="container footer">© <?php echo date('Y'); ?> Hijab Shop</div>
</body>
</html>
