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

    <title>Riwayat Pesanan - CitraSnack</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/admin-riwayat.css">
<link rel="stylesheet" href="assets/css/sidebar.css">
</head>

<body>

    <!-- SIDEBAR -->
    <?php include __DIR__ . "/sidebar.php"; ?>


    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <!-- HEADER -->
        <div class="page-header">

            <div>
                <h1>Riwayat Pesanan</h1>
                <p>
                    Lihat riwayat pesanan yang telah selesai atau dibatalkan.
                </p>
            </div>

        </div>


        <!-- FILTER -->
        <div class="filter-card">

            <div class="filter-left">

                <label for="statusFilter">
                    Status
                </label>

                <select id="statusFilter" class="form-select">
                    <option value="semua">
                        Semua
                    </option>

                    <option value="Selesai">
                        Selesai
                    </option>

                    <option value="Dibatalkan">
                        Dibatalkan
                    </option>
                </select>

            </div>


            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari ID pesanan atau nama pelanggan...">

            </div>

        </div>


        <!-- TABLE CARD -->
        <div class="table-card">

            <div class="table-header">

                <div>
                    <h5>Daftar Riwayat</h5>

                    <span id="orderCount">
                        0 pesanan
                    </span>
                </div>

            </div>


            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>ID Pesanan</th>

                            <th>Pelanggan</th>

                            <th>Tanggal</th>

                            <th>Total</th>

                            <th>Pembayaran</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody id="historyTableBody">

                        <tr>

                            <td colspan="7" class="empty-state">

                                <i class="bi bi-clock-history"></i>

                                <p>
                                    Memuat riwayat pesanan...
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </main>


    <!-- DETAIL MODAL -->
    <div
        class="modal fade"
        id="historyModal"
        tabindex="-1"
        aria-hidden="true">

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

                        <div class="detail-grid">

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

                        </div>

                    </div>


                    <!-- ADDRESS -->
                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-geo-alt"></i>
                            Alamat
                        </h6>

                        <p id="detailAddress">
                            -
                        </p>

                    </div>


                    <!-- ITEMS -->
                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-bag"></i>
                            Produk
                        </h6>

                        <div id="detailItems">
                            -
                        </div>

                    </div>


                    <!-- PAYMENT -->
                    <div class="detail-section">

                        <div class="summary-row">

                            <span>
                                Status Pembayaran
                            </span>

                            <span
                                id="detailPaymentStatus"
                                class="status-badge">
                                -
                            </span>

                        </div>


                        <div class="summary-row total-row">

                            <span>
                                Total Pesanan
                            </span>

                            <strong id="detailTotal">
                                Rp 0
                            </strong>

                        </div>

                    </div>


                    <!-- NOTES -->
                    <div class="detail-section">

                        <h6>
                            <i class="bi bi-chat-left-text"></i>
                            Catatan
                        </h6>

                        <p id="detailNotes">
                            -
                        </p>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- BOOTSTRAP -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- RIWAYAT -->
    <script
        type="module"
        src="assets/js/admin-riwayat.js">
    </script>
    <script src="assets/js/sidebar.js?v=11"></script>

</body>

</html>