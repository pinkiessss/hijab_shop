<?php
require_once 'config.php';

// hitung total & siapkan items
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

// submit order
check_csrf();
$success = false;
$order_id = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($items)) {
  $name = trim($_POST['name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $address = trim($_POST['address'] ?? '');

  if ($name === '') $errors[] = 'Nama wajib diisi';
  if ($phone === '') $errors[] = 'Nomor HP wajib diisi';
  if ($address === '') $errors[] = 'Alamat wajib diisi';

  if (!$errors) {
    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total) VALUES (?,?,?,?)");
      $stmt->execute([$name, $phone, $address, $total]);
      $order_id = $pdo->lastInsertId();

      $oi = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
      $upd = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
      foreach ($items as $it) {
        $oi->execute([$order_id, $it['p']['id'], $it['qty'], $it['p']['price']]);
        $upd->execute([$it['qty'], $it['p']['id'], $it['qty']]);
      }

      $pdo->commit();
      $_SESSION['cart'] = [];
      $success = true;
    } catch (Exception $e) {
      $pdo->rollBack();
      $errors[] = 'Gagal menyimpan pesanan.';
    }
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout — Hijab Shop</title>
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
  <h1>Checkout</h1>

  <?php if ($success): ?>
    <div class="card">
      <h3>Terima kasih! Pesanan Anda berhasil dibuat.</h3>
      <p>Nomor Pesanan: <strong>#<?php echo (int)$order_id; ?></strong></p>
      <a class="btn" href="index.php">Kembali ke Katalog</a>
    </div>
  <?php else: ?>

    <?php if ($errors): ?>
      <div class="card" style="border:2px solid #fde68a; background:#fef9c3;">
        <strong>Periksa kembali:</strong>
        <ul>
          <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
      <p>Keranjang kosong. <a href="index.php">Belanja dulu →</a></p>
    <?php else: ?>
      <div class="card">
        <h3>Ringkasan Pesanan</h3>
        <table class="table">
          <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?php echo htmlspecialchars($it['p']['name']); ?></td>
                <td><?php echo rupiah($it['p']['price']); ?></td>
                <td><?php echo (int)$it['qty']; ?></td>
                <td><?php echo rupiah($it['sub']); ?></td>
              </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="3" style="text-align:right; font-weight:700;">Total</td>
              <td style="font-weight:700;"><?php echo rupiah($total); ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <form method="post" class="form" style="max-width:520px; margin-top:16px;">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <div>
          <label>Nama Lengkap</label>
          <input class="input" name="name" placeholder="Nama penerima">
        </div>
        <div>
          <label>Nomor HP</label>
          <input class="input" name="phone" placeholder="08xxxxxxxxxx">
        </div>
        <div>
          <label>Alamat Lengkap</label>
          <textarea class="input" name="address" rows="4" placeholder="Jalan, RT/RW, Kel/Desa, Kec, Kota/Kab, Provinsi, Kode Pos"></textarea>
        </div>
        <button class="btn">Buat Pesanan</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</main>

<div class="container footer">© <?php echo date('Y'); ?> Hijab Shop</div>
</body>
</html>
