<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Cek login admin
if (!isset($_SESSION["ID_Admin"])) {
    header("Location: login.php");
    exit;
}

// Ambil nama admin
$namaAdmin = $_SESSION["Nama_Admin"] ?? "Admin";

// ================================
// STATISTIK DASHBOARD
// ================================

// Total pesanan
$result = $conn->query("SELECT COUNT(*) AS total FROM pesanan");
$totalOrders = $result ? (int) $result->fetch_assoc()["total"] : 0;

// Pesanan menunggu atau sedang diproses
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM pesanan
    WHERE Status_Pesanan IN ('Menunggu', 'Diproses')
");
$pendingOrders = $result ? (int) $result->fetch_assoc()["total"] : 0;

// Pesanan selesai
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM pesanan
    WHERE Status_Pesanan = 'Selesai'
");
$completedOrders = $result ? (int) $result->fetch_assoc()["total"] : 0;

// Total pendapatan dari pembayaran yang sudah lunas
$result = $conn->query("
    SELECT COALESCE(SUM(p.Total_Harga), 0) AS total
    FROM pesanan p
    INNER JOIN pembayaran pb
        ON pb.ID_Pesanan = p.ID_Pesanan
    WHERE pb.Status_Bayar IN ('Lunas', 'Berhasil', 'Dibayar')
");
$totalRevenue = $result
    ? (float) $result->fetch_assoc()["total"]
    : 0;

// ================================
// PESANAN TERBARU
// ================================

$sql = "
    SELECT
        p.ID_Pesanan,
        p.Tanggal_Pesan,
        p.Total_Harga,
        p.Status_Pesanan,
        pl.Nama_Lengkap,
        (
            SELECT pb.Status_Bayar
            FROM pembayaran pb
            WHERE pb.ID_Pesanan = p.ID_Pesanan
            ORDER BY pb.ID_Pembayaran DESC
            LIMIT 1
        ) AS Status_Bayar
    FROM pesanan p
    LEFT JOIN pelanggan pl
        ON pl.ID_Pelanggan = p.ID_Pelanggan
    ORDER BY p.Tanggal_Pesan DESC
    LIMIT 5
";

$ordersResult = $conn->query($sql);

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function formatRupiah($angka)
{
    return "Rp" . number_format((float) $angka, 0, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Citra Snack</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
</head>

<body>

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <?php include __DIR__ . "/sidebar.php"; ?>

        <!-- MAIN -->
        <main class="admin-main">

            <!-- HEADER -->
            <header class="admin-header">

                <div>
                    <span class="header-label">Dashboard</span>

                    <h1>
                        Selamat Datang, <?= e($namaAdmin); ?>
                    </h1>
                </div>

                <div class="admin-profile">

                    <div class="admin-avatar">
                        <i class="bi bi-person"></i>
                    </div>

                    <div class="admin-profile-info">
                        <strong>
                            <?= e($namaAdmin); ?>
                        </strong>

                        <span>Administrator</span>
                    </div>

                </div>

            </header>

            <!-- STATISTICS -->
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <div class="stat-content">
                        <span>Total Pesanan</span>
                        <strong><?= $totalOrders; ?></strong>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div class="stat-content">
                        <span>Menunggu Diproses</span>
                        <strong><?= $pendingOrders; ?></strong>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div class="stat-content">
                        <span>Selesai</span>
                        <strong><?= $completedOrders; ?></strong>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div class="stat-content">
                        <span>Total Pendapatan</span>
                        <strong><?= formatRupiah($totalRevenue); ?></strong>
                    </div>
                </div>

            </section>

            <!-- ORDERS -->
            <section class="orders-card">

                <div class="section-header">
                    <div>
                        <h2>Pesanan Terbaru</h2>
                        <p>Daftar pesanan yang masuk ke Citra Snack.</p>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="orders-table">

                        <thead>
                            <tr>
                                <th>Nomor Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>

                                <?php while ($order = $ordersResult->fetch_assoc()): ?>

                                    <tr>
                                        <td>
                                            #<?= e($order["ID_Pesanan"]); ?>
                                        </td>

                                        <td>
                                            <?= e($order["Nama_Lengkap"] ?? "-"); ?>
                                        </td>

                                        <td>
                                            <?= formatRupiah($order["Total_Harga"]); ?>
                                        </td>

                                        <td>
                                            <?= e($order["Status_Bayar"] ?? "Belum ada"); ?>
                                        </td>

                                        <td>
                                            <?= e($order["Status_Pesanan"]); ?>
                                        </td>

                                        <td>
                                            <a
                                                href="pesanan.php?id=<?= (int) $order["ID_Pesanan"]; ?>"
                                                class="btn btn-sm btn-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="empty-state">
                                        Belum ada pesanan.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>


    <script src="assets/js/sidebar.js?v=10"></script>
</body>

</html>

<?php
$conn->close();
?>