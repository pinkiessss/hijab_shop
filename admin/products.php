<?php
require_once '../config.php';
if (empty($_SESSION['admin'])) {
  header('Location: login.php');
  exit;
}

check_csrf();

// Create / Update
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'create' || $action === 'update') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $price = max(0, (int)($_POST['price'] ?? 0));
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    $desc  = trim($_POST['description'] ?? '');
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
      $f = $_FILES['image'];
      if ($f['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
          $new = '../assets/' . uniqid('img_') . '.' . $ext;
          if (move_uploaded_file($f['tmp_name'], $new)) {
            $imagePath = substr($new, 3); // remove ../
          }
        }
      }
    }

    if ($action === 'create') {
      $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, stock, image) VALUES (?,?,?,?,?,?)");
      $stmt->execute([$name, $slug, $desc, $price, $stock, $imagePath]);
      $msg = 'Produk ditambahkan';
    } else {
      $id = (int)$_POST['id'];
      if ($imagePath) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, slug=?, description=?, price=?, stock=?, image=? WHERE id=?");
        $stmt->execute([$name, $slug, $desc, $price, $stock, $imagePath, $id]);
      } else {
        $stmt = $pdo->prepare("UPDATE products SET name=?, slug=?, description=?, price=?, stock=? WHERE id=?");
        $stmt->execute([$name, $slug, $desc, $price, $stock, $id]);
      }
      $msg = 'Produk diperbarui';
    }
  }

  if ($action === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$id]);
    $msg = 'Produk dihapus';
  }

  header('Location: products.php?msg='.urlencode($msg));
  exit;
}

// Read
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produk (Admin) — Hijab Shop</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container nav">
  <div class="brand">Hijab Shop (Admin)</div>
  <div>
    <a href="../index.php">Lihat Toko</a>
    <a href="logout.php" class="cart-pill">Keluar</a>
  </div>
</div>

<main class="container">
  <?php if (!empty($_GET['msg'])): ?>
    <div class="card" style="border:2px solid #bbf7d0; background:#dcfce7;"><?php echo htmlspecialchars($_GET['msg']); ?></div>
  <?php endif; ?>

  <div class="card">
    <h2>Tambah Produk</h2>
    <form method="post" enctype="multipart/form-data" class="form">
      <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="action" value="create">
      <label>Nama</label>
      <input class="input" name="name" required>
      <label>Slug (unik, huruf-kecil-dengan-strip)</label>
      <input class="input" name="slug" required>
      <label>Harga (angka)</label>
      <input class="input" type="number" name="price" required>
      <label>Stok</label>
      <input class="input" type="number" name="stock" required>
      <label>Deskripsi</label>
      <textarea class="input" name="description" rows="4"></textarea>
      <label>Gambar (jpg/png/webp)</label>
      <input class="file" type="file" name="image" accept="image/*">
      <button class="btn">Simpan</button>
    </form>
  </div>

  <div class="card" style="margin-top:16px;">
    <h2>Daftar Produk</h2>
    <table class="table">
      <thead>
        <tr><th>Gambar</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Slug</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><img src="../<?php echo htmlspecialchars($p['image'] ?: 'assets/placeholder.png'); ?>" style="width:60px; height:60px; object-fit:cover; border-radius:8px;"></td>
          <td><?php echo htmlspecialchars($p['name']); ?></td>
          <td><?php echo rupiah($p['price']); ?></td>
          <td><?php echo (int)$p['stock']; ?></td>
          <td><?php echo htmlspecialchars($p['slug']); ?></td>
          <td style="display:flex; gap:6px;">
            <form method="post" enctype="multipart/form-data" class="form" style="display:inline-grid; grid-template-columns: repeat(7, minmax(80px,1fr)); gap:6px; align-items:center;">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <input class="input" name="name" value="<?php echo htmlspecialchars($p['name']); ?>">
              <input class="input" name="slug" value="<?php echo htmlspecialchars($p['slug']); ?>">
              <input class="input" type="number" name="price" value="<?php echo (int)$p['price']; ?>">
              <input class="input" type="number" name="stock" value="<?php echo (int)$p['stock']; ?>">
              <input class="input" name="description" value="<?php echo htmlspecialchars(mb_strimwidth($p['description'], 0, 120, '…')); ?>">
              <input class="file" type="file" name="image" accept="image/*">
              <button class="btn">Update</button>
            </form>
            <form method="post" onsubmit="return confirm('Yakin hapus?');">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <button class="btn btn-danger">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
