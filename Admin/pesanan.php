<?php
session_start();

if (!isset($_SESSION["ID_Admin"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pesanan - Admin Citra Snack</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet" href="assets/css/admin-pesanan.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
</head>

<body>

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <?php include __DIR__ . "/sidebar.php"; ?>


        <!-- MAIN -->

        <main class="admin-main">

            <header class="admin-header">

                <div>

                    <span class="header-label">
                        Manajemen Pesanan
                    </span>

                    <h1>
                        Pesanan
                    </h1>

                </div>

                <div class="admin-profile">

                    <div class="admin-avatar">
                        <i class="bi bi-person"></i>
                    </div>

                    <div class="admin-profile-info">

                        <strong id="adminEmail">
                            <?= htmlspecialchars($_SESSION["Nama_Admin"] ?? "Administrator") ?>
                        </strong>

                        <span>
                            Administrator
                        </span>

                    </div>

                </div>

            </header>


            <!-- FILTER -->

            <section class="filter-card">

                <div class="filter-title">
                    <i class="bi bi-funnel"></i>
                    Filter Pesanan
                </div>

                <select id="statusFilter">

                    <option value="semua">
                        Semua Status
                    </option>

                    <option value="Pesanan Diterima">
                        Pesanan Diterima
                    </option>

                    <option value="Sedang Diproses">
                        Sedang Diproses
                    </option>

                    <option value="Dalam Pengantaran">
                        Dalam Pengantaran
                    </option>

                    <option value="Selesai">
                        Selesai
                    </option>

                    <option value="Dibatalkan">
                        Dibatalkan
                    </option>
                    <option value="Menunggu">Menunggu</option>
                    <option value="Diproses">Diproses</option>

                </select>

            </section>


            <!-- ORDERS -->

            <section class="orders-card">

                <div class="section-header">

                    <div>

                        <h2>
                            Daftar Pesanan
                        </h2>

                        <p>
                            Kelola pesanan pelanggan Citra Snack.
                        </p>

                    </div>

                    <span id="orderCount">
                        0 pesanan
                    </span>

                </div>


                <div class="table-responsive">

                    <table class="orders-table">

                        <thead>

                            <tr>

                                <th>
                                    Nomor Pesanan
                                </th>

                                <th>
                                    Pelanggan
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Total
                                </th>

                                <th>
                                    Pembayaran
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody id="ordersTableBody">

                            <tr>

                                <td
                                    colspan="7"
                                    class="empty-state">

                                    Memuat pesanan...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>


    <!-- DETAIL MODAL -->

    <div
        class="modal fade"
        id="orderDetailModal"
        tabindex="-1">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <div>

                        <h5 class="modal-title">
                            Detail Pesanan
                        </h5>

                        <small id="modalOrderId">
                            -
                        </small>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <!-- CUSTOMER -->

                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-person"></i>
                            Data Pelanggan
                        </h6>

                        <div class="customer-detail-grid">

                            <div>
                                <span>Nama</span>
                                <strong id="detailName">-</strong>
                            </div>

                            <div>
                                <span>WhatsApp</span>
                                <strong id="detailWhatsapp">-</strong>
                            </div>

                            <div>
                                <span>Tanggal Dibutuhkan</span>
                                <strong id="detailDate">-</strong>
                            </div>

                            <div>
                                <span>Waktu</span>
                                <strong id="detailTime">-</strong>
                            </div>

                            <div class="full-width">
                                <span>Alamat</span>
                                <strong id="detailAddress">-</strong>
                            </div>

                            <div class="full-width">
                                <span>Catatan</span>
                                <strong id="detailNotes">-</strong>
                            </div>

                        </div>

                    </div>


                    <!-- ITEMS -->

                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-basket"></i>
                            Pesanan
                        </h6>

                        <div id="detailItems">
                            -
                        </div>

                        <div class="detail-total">

                            <span>
                                Total
                            </span>

                            <strong id="detailTotal">
                                Rp0
                            </strong>

                        </div>

                    </div>


                    <!-- PAYMENT -->

                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-credit-card"></i>
                            Pembayaran
                        </h6>

                        <div class="payment-detail">

                            <span>
                                Status Pembayaran
                            </span>

                            <strong id="detailPaymentStatus">
                                -
                            </strong>

                        </div>

                    </div>


                    <!-- STATUS -->

                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-arrow-repeat"></i>
                            Status Pesanan
                        </h6>

                        <select
                            id="detailOrderStatus"
                            class="form-select">

                            <option value="Pesanan Diterima">
                                Pesanan Diterima
                            </option>

                            <option value="Sedang Diproses">
                                Sedang Diproses
                            </option>

                            <option value="Dalam Pengantaran">
                                Dalam Pengantaran
                            </option>

                            <option value="Selesai">
                                Selesai
                            </option>

                            <option value="Dibatalkan">
                                Dibatalkan
                            </option>

                        </select>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <button
                        type="button"
                        id="saveStatusButton"
                        class="btn btn-save-status">

                        <i class="bi bi-check-lg"></i>
                        Simpan Status

                    </button>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-pesanan.js?v=1"></script>
    <script src="assets/js/sidebar.js?v=11"></script>

</body>

</html>