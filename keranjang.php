<?php
session_start();
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "citra_snack";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) { die("Koneksi gagal: " . mysqli_connect_error()); }

// Inisialisasi keranjang
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah produk ke keranjang
if (isset($_GET['id_produk'])) {
    $id_produk = $_GET['id_produk'];
    $jumlah = $_GET['jumlah'];
    $sql = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_produk'");
    $p = mysqli_fetch_assoc($sql);

    // Cek apakah produk sudah ada di keranjang
    $ada = false;
    foreach ($_SESSION['keranjang'] as &$item) {
        if ($item['id_produk'] == $id_produk) {
            $item['jumlah'] += $jumlah;
            $ada = true;
            break;
        }
    }
    if (!$ada) {
        $_SESSION['keranjang'][] = [
            'id_produk' => $id_produk,
            'nama_produk' => $p['nama_produk'],
            'harga' => $p['harga'],
            'jumlah' => $jumlah
        ];
    }
    header("Location: keranjang.php");
}

// Hapus dari keranjang
if (isset($_GET['hapus'])) {
    unset($_SESSION['keranjang'][$_GET['hapus']]);
    header("Location: keranjang.php");
}

// Hitung total
$total = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['harga'] * $item['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja — Citra Snack</title>
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
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 4%;
        }
        h2 {
            text-align: center;
            color: #d35400;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        .kartu {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            margin-bottom: 1rem;
        }
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px dashed #f0c8a0;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .item:last-child { border-bottom: none; }
        .nama { font-weight: bold; color: #d35400; }
        .hapus {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }
        .total {
            font-size: 1.4rem;
            font-weight: bold;
            color: #e67e22;
            text-align: right;
            margin: 1.5rem 0;
        }
        .btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 0.9rem;
            background-color: #e67e22;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover { background-color: #d35400; }
        .btn-kembali {
            display: inline-block;
            margin-bottom: 1rem;
            color: #e67e22;
            text-decoration: none;
        }
        .kosong {
            text-align: center;
            padding: 3rem;
            color: #888;
            font-size: 1.1rem;
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
        <li><a href="produk.php">Daftar Produk</a></li>
        <li><a href="keranjang.php" class="aktif">Keranjang</a></li>
        <li><a href="status_pesanan.php">Status Pesanan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li class="menu-admin"><a href="admin/login.php">🔐 Admin</a></li>
    </ul>
</nav>

<!-- Keranjang Belanja -->
<div class="container">
    <a href="daftar_produk.php" class="btn-kembali">← Lanjut Belanja</a>
    <h2>🛒 Keranjang Belanja</h2>

    <?php if (empty($_SESSION['keranjang'])) { ?>
        <div class="kartu kosong">
            Keranjang masih kosong. Yuk pilih camilan favoritmu! 😊
        </div>
    <?php } else { ?>
        <div class="kartu">
            <?php foreach ($_SESSION['keranjang'] as $i => $item) { ?>
                <div class="item">
                    <div>
                        <span class="nama"><?= $item['nama_produk'] ?></span><br>
                        Rp <?= number_format($item['harga'], 0, ',', '.') ?> × <?= $item['jumlah'] ?> =
                        <strong>Rp <?= number_format($item['harga']*$item['jumlah'], 0, ',', '.') ?></strong>
                    </div>
                    <a href="keranjang.php?hapus=<?= $i ?>" class="hapus">❌ Hapus</a>
                </div>
            <?php } ?>
            <div class="total">Total: Rp <?= number_format($total, 0, ',', '.') ?></div>
        </div>
        <a href="checkout.php" class="btn">✅ Lanjut ke Checkout</a>
    <?php } ?>
</div>

<footer>
    <p>&copy; 2026 Citra Snack — Sistem Informasi Pemesanan</p>
</footer>

</body>
</html>