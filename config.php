<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ====== DB CONFIG ======
define('DB_HOST', 'localhost');
define('DB_NAME', 'hijab_shop');
define('DB_USER', 'root');
define('DB_PASS', '');

// ====== PDO ======
try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo "DB connection error.";
  exit;
}

// ====== Helpers ======
function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}
function check_csrf() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
      http_response_code(400);
      exit('Invalid CSRF token');
    }
  }
}

function rupiah($num) {
  return 'Rp ' . number_format($num, 0, ',', '.');
}

// cart structure: $_SESSION['cart'] = [ product_id => qty ]
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

?>
