
-- MySQL schema & seed
CREATE DATABASE IF NOT EXISTS hijab_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hijab_shop;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description TEXT,
  price INT NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  customer_phone VARCHAR(40) NOT NULL,
  customer_address TEXT NOT NULL,
  total INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL,
  price INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- sample data
INSERT INTO products (name, slug, description, price, stock, image) VALUES
('Hijab Pashmina Ceruty', 'hijab-pashmina-ceruty', 'Pashmina ceruty premium, adem dan jatuh.', 69000, 50, 'assets/pashmina.jpg'),
('Hijab Segi Empat Voal', 'hijab-segi-empat-voal', 'Voal premium anti geser, nyaman dipakai seharian.', 59000, 40, 'assets/voal.jpg'),
('Hijab Instan Sport', 'hijab-instan-sport', 'Praktis untuk aktivitas harian dan olahraga.', 49000, 60, 'assets/instan.jpg');
