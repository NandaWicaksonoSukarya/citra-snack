<?php 
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "citra_snack";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citra Snack — Sistem Informasi Pemesanan</title>
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

        /* Navbar */
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

        /* Bagian Utama */
        .hero {
            text-align: center;
            padding: 3.5rem 4%;
            background: #fff3e0;
        }

        .hero h1 {
            font-size: 2.4rem;
            color: #d35400;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto 2rem;
        }

        .tombol-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.85rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-utama {
            background-color: #e67e22;
            color: white;
        }

        .btn-utama:hover {
            background-color: #d35400;
        }

        .btn-kedua {
            background-color: white;
            color: #e67e22;
            border: 2px solid #e67e22;
        }

        .btn-kedua:hover {
            background-color: #fff3e0;
        }

        /* Bagian Alur Pemesanan */
        .alur {
            padding: 3rem 4%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .alur h2 {
            text-align: center;
            color: #d35400;
            margin-bottom: 2rem;
        }

        .langkah {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .langkah-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .nomor {
            display: inline-block;
            width: 45px;
            height: 45px;
            line-height: 45px;
            background: #e67e22;
            color: white;
            border-radius: 50%;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.8rem;
        }

        .langkah-item h3 {
            color: #d35400;
            margin-bottom: 0.5rem;
        }

        /* Bagian Masalah */
        .masalah {
            padding: 2rem 4% 3rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .masalah h2 {
            text-align: center;
            color: #d35400;
            margin-bottom: 1.5rem;
        }

        .masalah ul {
            list-style: none;
        }

        .masalah li {
            padding: 0.8rem 0;
            border-bottom: 1px dashed #f0c8a0;
            font-size: 1.05rem;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1.2rem;
            margin-top: 1rem;
        }

        /* Tampilan HP */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 0.8rem;
            }
            .menu {
                justify-content: center;
            }
            .hero h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<!-- Navigasi — Menu Sesuai Alur Pelanggan -->
<nav class="navbar">
    <div class="logo">🍪 Citra Snack</div>
    <ul class="menu">
        <li><a href="index.php" class="aktif">Beranda</a></li>
        <li><a href="produk.php">Daftar Produk</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="status_pesanan.php">Status Pesanan</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li class="menu-admin"><a href="admin/login.php"> Admin</a></li>
    </ul>
</nav>

<!-- Bagian Utama / Beranda -->
<section class="hero">
    <h1>Selamat Datang di Citra Snack</h1>
    <p>Sistem Informasi Pemesanan Camilan — Mudah, Cepat, dan Praktis!<br>
    Pilih camilan favoritmu, masukkan ke keranjang, lalu pesan. Pantau status pesananmu secara langsung.</p>
    <div class="tombol-group">
        <a href="daftar_produk.php" class="btn btn-utama">Lihat Daftar Produk</a>
        <a href="keranjang.php" class="btn btn-kedua">Lihat Keranjang</a>
    </div>
</section>

<!-- Alur Pemesanan Pelanggan -->
<section class="alur">
    <h2> Cara Memesan di Citra Snack</h2>
    <div class="langkah">
        <div class="langkah-item">
            <span class="nomor">1</span>
            <h3>Daftar Produk</h3>
            <p>Lihat dan pilih camilan yang kamu inginkan</p>
        </div>
        <div class="langkah-item">
            <span class="nomor">2</span>
            <h3>Detail & Keranjang</h3>
            <p>Lihat detail produk, lalu tambahkan ke keranjang</p>
        </div>
        <div class="langkah-item">
            <span class="nomor">3</span>
            <h3>Checkout</h3>
            <p>Isi data pemesan, pilih metode pembayaran</p>
        </div>
        <div class="langkah-item">
            <span class="nomor">4</span>
            <h3>Pantau Pesanan</h3>
            <p>Lihat status: Menunggu → Diproses → Siap → Selesai</p>
        </div>
    </div>
</section>

<!-- Permasalahan yang Diatasi -->
<section class="masalah">
    <h2>Permasalahan yang Diselesaikan</h2>
    <ul>
        <li>❌ Pemesanan masih manual & menunggu lama → ✅ Pesan kapan saja secara online</li>
        <li>❌ Sulit memantau pesanan → ✅ Lihat status secara langsung</li>
        <li>❌ Kesalahan pencatatan pesanan → ✅ Tersistem & akurat</li>
        <li>❌ Stok tidak terlihat jelas → ✅ Informasi produk tersedia lengkap</li>
    </ul>
</section>

<footer>
    <p>&copy; 2026 Citra Snack — Sistem Informasi Pemesanan | Semua Hak Dilindungi</p>
</footer>

</body>
</html>