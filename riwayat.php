<?php
session_start();
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
// Sesuaikan dengan nama database di Laragon/phpMyAdmin kamu (citrasnak)
$db   = "citra_snack";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) { die("Koneksi gagal: " . mysqli_connect_error()); }

// Ambil SEMUA pesanan dari yang TERBARU
$sql = mysqli_query($koneksi, "SELECT * FROM pesanan ORDER BY tanggal_pesanan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan — Citra Snack</title>
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
            max-width: 700px;
            margin: 2rem auto;
            padding: 0 4%;
        }
        h2 {
            text-align: center;
            color: #d35400;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        .kartu {
            background: white;
            padding: 1.2rem;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }
        .status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .Menunggu { background: #fff3cd; color: #856404; }
        .Diproses { background: #cce5ff; color: #004085; }
        .Siap { background: #d4edda; color: #155724; }
        .Selesai { background: #e2e3e5; color: #383d41; }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            border-bottom: 1px dashed #eee;
            font-size: 0.95rem;
            flex-wrap: wrap;
            gap: 0.3rem;
        }
        .info-row:last-child { border-bottom: none; }
        .total {
            font-weight: bold;
            color: #e67e22;
            font-size: 1.05rem;
        }
        .tanggal {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .kosong {
            text-align: center;
            padding: 3rem;
            color: #888;
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
            margin-top: 1rem;
            transition: 0.3s;
        }
        .btn:hover { background-color: #d35400; }
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
        <li><a href="daftar_produk.php">Daftar Produk</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="status_pesanan.php">Status Pesanan</a></li>
        <li><a href="riwayat.php" class="aktif">Riwayat</a></li>
        <li class="menu-admin"><a href="admin/login.php">🔐 Admin</a></li>
    </ul>
</nav>

<!-- Riwayat Pesanan -->
<div class="container">
    <h2>📂 Riwayat Semua Pesanan</h2>

    <?php if (mysqli_num_rows($sql) == 0): ?>
        <div class="kartu kosong">
            Belum ada riwayat pesanan.<br>Yuk pesan camilan sekarang! 😊
            <a href="produk.php" class="btn">Pilih Camilan</a>
        </div>
    <?php else: ?>"
        <?php while ($r = mysqli_fetch_assoc($sql)): ?>
            <div class="kartu">
                <span class="status <?= $r['status_pesanan'] ?>">
                    #<?= str_pad($r['id_pesanan'], 5, '0', STR_PAD_LEFT) ?> - 
                    <?php
                    if ($r['status_pesanan'] == 'Menunggu') echo '⏳ Menunggu';
                    elseif ($r['status_pesanan'] == 'Diproses') echo '🔄 Diproses';
                    elseif ($r['status_pesanan'] == 'Siap') echo '✅ Siap';
                    elseif ($r['status_pesanan'] == 'Selesai') echo '🎉 Selesai';
                    ?>
                </span>
                <div class="tanggal"><?= date('d/m/Y H:i', strtotime($r['tanggal_pesanan'])) ?></div>
                <div class="info-row">
                    <span>Pemesan: <?= $r['nama_pemesan'] ?></span>
                    <span class="total">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></span>
                </div>
                <div class="info-row">
                    <span>Telepon: <?= $r['telepon'] ?></span>
                    <span>Bayar: <?= $r['metode_bayar'] ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 Citra Snack – Sistem Informasi Pemesanan</p>
</footer>

</body>
</html>