<?php
session_start();
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "citra_snack";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) { die("Koneksi gagal: " . mysqli_connect_error()); }

// Ambil nomor pesanan terakhir yang baru dikirim
$id_pesanan = isset($_SESSION['pesanan_terakhir']) ? $_SESSION['pesanan_terakhir'] : 0;

// Ambil data pesanan
$pesanan = null;
$rincian = [];
if ($id_pesanan > 0) {
    $sql = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan='$id_pesanan'");
    $pesanan = mysqli_fetch_assoc($sql);

    // Ambil rincian belanja
    $sql2 = mysqli_query($koneksi, "SELECT dp.*, p.nama_produk, p.harga 
        FROM detail_pesanan dp 
        JOIN produk p ON dp.id_produk = p.id_produk 
        WHERE dp.id_pesanan='$id_pesanan'");
    while ($r = mysqli_fetch_assoc($sql2)) {
        $rincian[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan — Citra Snack</title>
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
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .status-box {
            text-align: center;
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            font-weight: bold;
        }
        .Menunggu { background: #fff3cd; color: #856404; }
        .Diproses { background: #cce5ff; color: #004085; }
        .Siap { background: #d4edda; color: #155724; }
        .Selesai { background: #e2e3e5; color: #383d41; }
        .langkah-status {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
            text-align: center;
        }
        .langkah-status div {
            font-size: 0.9rem;
            color: #aaa;
        }
        .langkah-status div.aktif {
            color: #e67e22;
            font-weight: bold;
        }
        .rincian {
            margin-top: 1.5rem;
            border-top: 1px dashed #ddd;
            padding-top: 1.5rem;
        }
        .rincian p { margin: 0.4rem 0; }
        .total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e67e22;
            text-align: right;
            margin-top: 1rem;
        }
        .info { margin: 0.5rem 0; }
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
            margin-top: 1.5rem;
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
            .langkah-status { font-size: 0.8rem; }
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
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="status_pesanan.php" class="aktif">Status Pesanan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li class="menu-admin"><a href="admin/login.php">🔐 Admin</a></li>
    </ul>
</nav>

<!-- Status Pesanan -->
<div class="container">
    <h2>📦 Status Pesanan Saya</h2>

    <?php if ($pesanan): ?>
    <div class="kartu">
        <h3 style="text-align:center; color:#d35400; margin-bottom:0.5rem;">Nomor Pesanan: #<?= str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) ?></h3>

        <?php $status = $pesanan['status_pesanan']; ?>
        <div class="status-box <?= $status ?>">
            <?php
            if ($status == 'Menunggu') echo '⏳ Menunggu Konfirmasi';
            elseif ($status == 'Diproses') echo '🔄 Sedang Diproses';
            elseif ($status == 'Siap') echo '✅ Siap Diambil/Dikirim';
            elseif ($status == 'Selesai') echo '🎉 Pesanan Selesai';
            ?>
        </div>

        <div class="langkah-status">
            <div class="<?= $status=='Menunggu'||$status=='Diproses'||$status=='Siap'||$status=='Selesai'?'aktif':'' ?>">⏳<br>Menunggu</div>
            <div class="<?= $status=='Diproses'||$status=='Siap'||$status=='Selesai'?'aktif':'' ?>">🔄<br>Diproses</div>
            <div class="<?= $status=='Siap'||$status=='Selesai'?'aktif':'' ?>">✅<br>Siap</div>
            <div class="<?= $status=='Selesai'?'aktif':'' ?>">🎉<br>Selesai</div>
        </div>

        <div class="info">
            <p><strong>Nama Pemesan:</strong> <?= $pesanan['nama_pemesan'] ?></p>
            <p><strong>Telepon:</strong> <?= $pesanan['telepon'] ?></p>
            <p><strong>Alamat:</strong> <?= $pesanan['alamat'] ?></p>
            <p><strong>Metode Bayar:</strong> <?= $pesanan['metode_bayar'] ?></p>
            <p><strong>Tanggal Pesan:</strong> <?= date('d/m/Y H:i', strtotime($pesanan['tanggal_pesanan'])) ?></p>
        </div>

        <div class="rincian">
            <h4>📋 Rincian Pesanan:</h4>
            <?php foreach ($rincian as $r): ?>
            <p>• <?= $r['nama_produk'] ?> × <?= $r['jumlah'] ?> = Rp <?= number_format($r['subtotal'], 0, ',', '.') ?></p>
            <?php endforeach; ?>
            <div class="total">Total: Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></div>
        </div>

        <a href="riwayat.php" class="btn">📂 Lihat Riwayat Semua Pesanan</a>
    </div>

    <?php else: ?>
    <div class="kartu kosong">
        Belum ada pesanan yang dikirim.<br>
        Silakan selesaikan pemesanan terlebih dahulu. 😊
        <a href="produk.php" class="btn">Pilih Camilan Sekarang</a>
    </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 Citra Snack — Sistem Informasi Pemesanan</p>
</footer>

</body>
</html>