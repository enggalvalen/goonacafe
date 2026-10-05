-- Database Goona Cafe (MySQL / MariaDB, XAMPP)
-- Versi HOSTING: tanpa CREATE DATABASE / USE. Pilih database dulu di phpMyAdmin, lalu Import file ini.
CREATE TABLE produk (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT NOT NULL,
  harga INT UNSIGNED NOT NULL,
  kategori VARCHAR(20) NOT NULL,
  foto VARCHAR(150) NOT NULL DEFAULT '',
  favorit TINYINT(1) NOT NULL DEFAULT 0,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  urutan INT NOT NULL DEFAULT 0,
  dibuat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE produk_varian (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  produk_id INT UNSIGNED NOT NULL,
  nama VARCHAR(40) NOT NULL,
  harga INT UNSIGNED NOT NULL,
  urutan INT NOT NULL DEFAULT 0,
  FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pesanan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  hp VARCHAR(20) NOT NULL,
  outlet VARCHAR(60) NOT NULL,
  pengambilan VARCHAR(20) NOT NULL,
  metode VARCHAR(20) NOT NULL,
  catatan VARCHAR(200) NOT NULL DEFAULT '',
  total INT UNSIGNED NOT NULL,
  status ENUM('baru','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'baru',
  dibuat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pesanan_item (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT UNSIGNED NOT NULL,
  produk_id INT UNSIGNED NULL,
  nama_produk VARCHAR(100) NOT NULL,
  varian VARCHAR(40) NOT NULL DEFAULT '',
  harga INT UNSIGNED NOT NULL,
  qty INT UNSIGNED NOT NULL,
  catatan VARCHAR(120) NOT NULL DEFAULT '',
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pesan_kontak (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  pesan TEXT NOT NULL,
  dibaca TINYINT(1) NOT NULL DEFAULT 0,
  dibuat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admin (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  dibuat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Menu Goona Cafe (sesuai papan menu)
INSERT INTO produk (id, nama, deskripsi, harga, kategori, foto, favorit, aktif, urutan) VALUES
(1, 'Americano', 'Espresso dan air, tegas dan bersih. Pilih disajikan panas atau dingin.', 17000, 'coffee', 'assets/img/produk/americano.jpg', 0, 1, 1),
(2, 'Osmanthus Coffee', 'Kopi dingin dengan sentuhan aroma bunga osmanthus yang harum dan lembut.', 23000, 'coffee', 'assets/img/produk/osmanthus-coffee.jpg', 0, 1, 2),
(3, 'Rujak Coffee', 'Racikan kopi dengan rasa unik khas Goona yang bikin penasaran.', 25000, 'coffee', 'assets/img/produk/rujak-coffee.jpg', 0, 1, 3),
(4, 'Creamy Reog Latte', 'Kopi susu dengan gula aren yang creamy, cocok untuk menemani aktivitas sehari-hari.', 25000, 'coffee', 'assets/img/produk/creamy-reog-latte.jpg', 1, 1, 4),
(5, 'Baileys Coffee', 'Kopi susu dengan rasa Baileys yang halus dan creamy.', 25000, 'coffee', 'assets/img/produk/baileys-coffee.jpg', 0, 1, 5),
(6, 'Butterschotch Coffee', 'Racikan kopi khas dengan aroma butterscotch yang kuat dan rasa seimbang.', 28000, 'coffee', 'assets/img/produk/butterschotch-coffee.jpg', 0, 1, 6),
(7, 'Vanilla Latte', 'Latte lembut dengan aroma vanila yang manis dan menenangkan.', 28000, 'coffee', 'assets/img/produk/vanilla-latte.jpg', 0, 1, 7),
(8, 'Caramel Latte', 'Latte dengan saus karamel yang manis, gurih, dan creamy.', 28000, 'coffee', 'assets/img/produk/caramel-latte.jpg', 0, 1, 8),
(9, 'Chestnut Latte', 'Latte dengan rasa chestnut yang hangat dan sedikit gurih.', 28000, 'coffee', 'assets/img/produk/chestnut-latte.jpg', 0, 1, 9),
(10, 'Milk Tea', 'Teh susu yang lembut dan creamy, favorit semua kalangan.', 22000, 'tea', 'assets/img/produk/milk-tea.jpg', 0, 1, 10),
(11, 'Jasmine Tea', 'Teh melati dengan aroma harum yang ringan. Pilih panas atau dingin.', 15000, 'tea', 'assets/img/produk/jasmine-tea.jpg', 0, 1, 11),
(12, 'Lychee Tea', 'Teh segar dengan rasa leci yang manis dan ringan.', 20000, 'tea', 'assets/img/produk/lychee-tea.jpg', 0, 1, 12),
(13, 'Thai Tea', 'Teh Thailand dengan susu yang manis, pekat, dan creamy.', 22000, 'tea', 'assets/img/produk/thai-tea.jpg', 0, 1, 13),
(14, 'Cochoniken', 'Minuman cokelat yang pekat dan manis. Pilih panas atau dingin.', 19000, 'noncoffee', 'assets/img/produk/cochoniken.jpg', 0, 1, 14),
(15, 'Matchana', 'Perpaduan matcha premium dengan susu segar.', 25000, 'noncoffee', 'assets/img/produk/matchana.jpg', 1, 1, 15),
(16, 'Milky Brown Sugar', 'Susu segar dengan sirup brown sugar yang manis dan creamy.', 22000, 'noncoffee', 'assets/img/produk/milky-brown-sugar.jpg', 0, 1, 16),
(17, 'Strawberry Lemonade', 'Minuman segar perpaduan stroberi dan lemon.', 20000, 'noncoffee', 'assets/img/produk/strawberry-lemonade.jpg', 1, 1, 17),
(18, 'Paket Gandini', 'Nasi + Sayap/Paha Bawah. Gratis air mineral dan sambal.', 25000, 'ayam', 'assets/img/produk/paket-gandini.jpg', 0, 1, 18),
(19, 'Paket Reog', 'Nasi + Dada/Paha Atas. Gratis air mineral dan sambal.', 30000, 'ayam', 'assets/img/produk/paket-reog.jpg', 0, 1, 19),
(20, 'Paket Niken 1', 'Snack + Sayap/Paha Bawah. Gratis air mineral dan sambal.', 35000, 'ayam', 'assets/img/produk/paket-niken-1.jpg', 0, 1, 20),
(21, 'Paket Niken 2', 'Snack + Dada/Paha Atas. Gratis air mineral dan sambal.', 40000, 'ayam', 'assets/img/produk/paket-niken-2.jpg', 0, 1, 21),
(22, 'Kulit Krispy', 'Kulit ayam goreng yang renyah dan gurih, cocok untuk camilan.', 15000, 'snacks', 'assets/img/produk/kulit-krispy.jpg', 0, 1, 22),
(23, 'Kentang', 'Kentang goreng potong tebal yang renyah di luar dan lembut di dalam.', 15000, 'snacks', 'assets/img/produk/kentang.jpg', 0, 1, 23),
(24, 'Donat', 'Isi 3. Camilan manis yang cocok ditemani secangkir kopi.', 20000, 'snacks', 'assets/img/produk/donat.jpg', 0, 1, 24),
(25, 'Nasi', 'Nasi putih hangat.', 10000, 'alacarte', 'assets/img/produk/nasi.jpg', 0, 1, 25),
(26, 'Sayap/Paha Bawah', 'Ayam goreng krispi bagian sayap atau paha bawah.', 18000, 'alacarte', 'assets/img/produk/sayap-paha-bawah.jpg', 0, 1, 26),
(27, 'Dada/Paha Atas', 'Ayam goreng krispi bagian dada atau paha atas.', 25000, 'alacarte', 'assets/img/produk/dada-paha-atas.jpg', 0, 1, 27),
(28, 'Sambal', 'Sambal pedas khas Goona.', 5000, 'alacarte', 'assets/img/produk/sambal.jpg', 0, 1, 28),
(29, 'Air Mineral', 'Air mineral dingin.', 5000, 'alacarte', 'assets/img/produk/air-mineral.jpg', 0, 1, 29);

INSERT INTO produk_varian (id, produk_id, nama, harga, urutan) VALUES
(1, 1, 'Hot', 17000, 1),
(2, 1, 'Ice', 20000, 2),
(3, 11, 'Hot', 15000, 1),
(4, 11, 'Ice', 15000, 2),
(5, 14, 'Hot', 19000, 1),
(6, 14, 'Ice', 22000, 2);
