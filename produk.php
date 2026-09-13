<?php
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "citra_snack";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) { die("Koneksi gagal: " . mysqli_connect_error()); }

// Ambil data produk dari database
$sql = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id_produk ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk — Citra Snack</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        body {
            background-color: #fffbf2;
            color: #333;
            line-height: 1.6;
        }
        .navbar {
            background: linear-gradient(135deg, #e67e22, #d35400);
            padding: 1rem 4%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            flex-wrap: wrap;
        }
        .logo {
            font-size: 1.6rem;
            font-weight: bold;
            color: white;
        }
        .menu {
            list-style: none;
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.9rem;
            border-radius: 6px;
            transition: 0.3s;
        }
        .menu a:hover, .menu a.aktif {
            background-color: rgba(255,255,255,0.25);
        }
        .menu-admin a {
            background-color: rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 4%;
        }
        h2 {
            text-align: center;
            color: #d35400;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        .grid-produk {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }
        .kartu-produk {
            background: white;
            border-radius: 12px;
            padding: 1.2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        .kartu-produk:hover {
            transform: translateY(-5px);
        }
        .kartu-produk h3 {
            color: #d35400;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .harga {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e67e22;
            margin: 0.5rem 0;
        }
        .stok {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 0.8rem;
        }
        .deskripsi {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 0.7rem;
            background-color: #e67e22;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #d35400;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1.2rem;
            margin-top: 3rem;
        }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 0.8rem; }
            .menu { justify-content: center; }
        }
    </style>
</head>
<body>

<!-- Navigasi -->
<nav class="navbar">
    <div class="logo">🍪 Citra Snack</div>
    <ul class="menu">
        <li><a href="index.php">Beranda</a></li>
        <li><a href="produk.php" class="aktif">Daftar Produk</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="status_pesanan.php">Status Pesanan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li class="menu-admin"><a href="admin/login.php">🔐 Admin</a></li>
    </ul>
</nav>

<!-- Daftar Produk -->
<div class="container">
    <h2>📋 Daftar Camilan Citra Snack</h2>
    <div class="grid-produk">
    <?php while ($p = mysqli_fetch_assoc($sql)) { ?>
        <div class="kartu-produk">
            <h3><?= $p['nama_produk'] ?></h3>
            <p class="harga">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
            <p class="stok">✅ Stok tersedia: <?= $p['stok'] ?> porsi</p>
            <p class="deskripsi"><?= $p['deskripsi'] ?></p>
            <a href="detail_produk.php?id=<?= $p['id_produk'] ?>" class="btn">Lihat Detail & Pesan</a>
        </div>
    <?php } ?>
    </div>
</div>

<footer>
    <p>&copy; 2026 Citra Snack — Sistem Informasi Pemesanan</p>
</footer>

</body>
</html>