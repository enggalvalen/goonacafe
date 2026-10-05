# Goona Cafe — Website (HTML + PHP + CSS + JS)

## Cara menjalankan di XAMPP
1. Copy folder `goonakafe` ke `htdocs` XAMPP (mis. `C:\xampp\htdocs\goonakafe`).
2. Start **Apache** di XAMPP Control Panel.
3. Buka `http://localhost/goonakafe/`

## Struktur folder
```
goonakafe/
├── index.php            Home
├── produk.php           Daftar menu + filter kategori
├── produk-detail.php    Detail produk (?id=)
├── about.php            About Us + Team Pengembangan
├── blog.php             Goona Stories
├── blog-detail.php      Detail artikel (?slug=)
├── contact.php          Contact + form pesan
├── faq.php              FAQ (accordion)
├── cart.php             Keranjang
├── cart-action.php      Proses tambah / ubah jumlah / hapus
├── checkout.php         Checkout (simpan pesanan ke database) + halaman pesanan diterima
├── install.php          Pasang database sekali (hapus setelah selesai)
├── database.sql         Skema + 29 menu
├── admin/               Panel admin (login, pesanan, pesan, produk)
├── includes/
│   ├── config.php           Kontak/sosmed ($kontak), daftar outlet ($lokasi), helper e()
│   ├── header.php           Navbar + badge Cart
│   ├── footer.php           Footer 4 kolom + widget chat
│   ├── products-data.php    Data produk + format_rupiah()
│   ├── cart-functions.php   Keranjang berbasis PHP session
│   ├── blog-data.php        Data artikel blog
│   └── faq-data.php         Data FAQ
└── assets/
    ├── css/style.css
    ├── js/script.js
    └── img/             Taruh foto asli di sini
```

## Database dan panel admin
1. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Buka `http://localhost/goonakafe/install.php` sekali. Ini membuat database `goonakafe` (user `root`, tanpa password) dan mengisi 29 menu. Kalau dibuka lagi, data tidak ditimpa.
3. Buka `http://localhost/goonakafe/admin/setup.php`, isi password untuk username `admingoona` (minimal 8 karakter). Halaman ini hanya bisa dipakai sekali dan hanya dari komputer lokal.
4. **Hapus file `install.php`** dari folder setelah berhasil.
5. Masuk di `http://localhost/goonakafe/admin/login.php`.

Isi panel admin: Ringkasan, Pesanan (cari, filter status, ubah status baru/diproses/selesai/dibatalkan, tombol chat WhatsApp pemesan), Pesan Contact (tandai dibaca, hapus), Produk (ubah harga, deskripsi, foto, varian Hot/Ice, ketersediaan, menu favorit Home, tambah/hapus produk).

**Lupa password admin:** buka phpMyAdmin, database `goonakafe`, tabel `admin`, hapus barisnya, lalu buka `admin/setup.php` lagi untuk membuat password baru.

## Menerbitkan website (GitHub + hosting PHP gratis)

GitHub Pages **tidak bisa** menjalankan PHP dan MySQL. Jadi: **GitHub** menyimpan kode (dan jadi bukti tugas), sedangkan website hidupnya berjalan di **hosting PHP + MySQL gratis**, mis. InfinityFree. Tampilan panel hosting bisa berubah; ikuti istilah yang mirip.

### A. Simpan kode di GitHub
1. Buat akun di github.com, lalu buat repository baru bernama `goonakafe` (saran: **Private**, karena `includes/config.php` berisi nomor WhatsApp dan rekening).
2. Pasang Git (git-scm.com). Di folder `goonakafe`, buka terminal:
   ```
   git init
   git add .
   git commit -m "Goona Cafe website"
   git branch -M main
   git remote add origin https://github.com/USERNAME/goonakafe.git
   git push -u origin main
   ```
3. Cek di GitHub bahwa file `includes/db-local.php` **tidak** ikut terunggah (sudah dicegah oleh `.gitignore`). Setiap ada perubahan: `git add .`, `git commit -m "pesan"`, `git push`.

### B. Siapkan hosting
1. Daftar di hosting gratis berbasis PHP + MySQL (mis. InfinityFree), buat akun hosting, dan pilih subdomain gratis.
2. Di panel, buka **MySQL Databases**, buat database (mis. `goonakafe`). Catat 4 data: **MySQL Hostname**, **nama database lengkap** (biasanya berawalan seperti `if0_12345678_`), **username**, dan **password**.
3. Upload semua isi folder `goonakafe` ke folder web hosting (biasanya `htdocs` atau `public_html`), lewat File Manager atau FTP (mis. FileZilla). Pastikan file tersembunyi `.htaccess` ikut terunggah, termasuk yang ada di `includes/` dan `assets/img/produk/`.
4. Di server, buat file `includes/db-local.php` (salin isi dari `includes/db-local.example.php`), isi 4 data database tadi, dan ganti `SETUP_KODE` dengan kode rahasia panjang buatanmu sendiri (minimal 8 karakter).

### C. Pasang database dan akun admin
Pilih salah satu cara untuk database:
- **Otomatis:** buka `https://ALAMATMU/install.php?kode=KODE_RAHASIAMU`.
- **Manual:** di phpMyAdmin, pilih database kamu, lalu Import `database-hosting.sql`.

Lalu buat password admin: buka `https://ALAMATMU/admin/setup.php?kode=KODE_RAHASIAMU`, isi password untuk `admingoona`. Masuk lewat `https://ALAMATMU/admin/login.php`.

### D. Setelah online
1. **Hapus `install.php`** dari server, dan ubah atau hapus baris `SETUP_KODE` di `db-local.php`.
2. Kalau SSL gratis (HTTPS) sudah aktif di panel hosting, buka `.htaccess` di folder utama dan hapus tanda `#` pada blok pengalihan HTTPS.
3. Ganti `assets/img/qris.png` dengan QRIS asli sebelum ada pembeli sungguhan.
4. Uji: buka Produk, tambah ke keranjang, checkout, lalu cek pesanan muncul di admin.

### Catatan hosting gratis
- Perubahan kode lewat GitHub **tidak otomatis** sampai ke hosting: unggah ulang file yang berubah. Foto yang diunggah lewat admin tersimpan di server, jadi jangan menimpa folder `assets/img/produk/` dengan versi lama.
- Hosting gratis biasanya punya batas ukuran file, pemakaian, dan tidak cocok untuk pembeli sungguhan. Untuk tugas kuliah sudah cukup.
- **Lupa password admin:** di phpMyAdmin, hapus baris di tabel `admin`, lalu buka `admin/setup.php?kode=...` lagi.

## Yang sudah jalan
- Keranjang pakai PHP session: tambah dari Produk / Detail Produk, ubah jumlah, hapus, badge jumlah di navbar.
- Checkout: validasi data pemesan, pilih outlet dan Dine In / Take Away, lalu halaman "Pesanan diterima" dengan kode pesanan.
- Filter produk: semua produk selalu dirender, filter hanya menyembunyikan kartu (tidak ada halaman kosong lagi).
- Blog, Blog Detail, FAQ, About (tim), widget chat sesuai wireframe terbaru.

## Pembayaran
- Metode ada di `$pembayaran` pada `includes/config.php`: QRIS, Transfer Bank BRI, DANA.
- **QRIS** baru muncul di Checkout setelah file QRIS asli Goona Cafe disimpan sebagai `assets/img/qris.png`.
- Setelah checkout, halaman "Pesanan diterima" menampilkan detail pembayaran dan tombol kirim bukti lewat WhatsApp.
- Pembayaran belum diverifikasi otomatis; pesanan hanya tercatat di session.

## Yang masih perlu diisi / dikembangkan
- `assets/img/qris.png`: QRIS asli Goona Cafe.
- Foto: ganti kotak gradasi (`.thumb`, `.art`, `.detail-photo`) dengan `<img src="assets/img/...">`; foto tim di array `$tim` pada `about.php`.
- Link Google Maps outlet Gubeng Lama, Semut, dan Bangil di `$lokasi_maps` (`config.php`).
- Tanggal dan isi blog (`blog-data.php`) masih draf.
- Simpan pesanan dan pesan contact ke MySQL (lihat komentar `TODO` di `checkout.php` dan `contact.php`).
