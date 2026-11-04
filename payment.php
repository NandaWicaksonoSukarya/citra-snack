<?php
session_start();

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Pembayaran - Citra Snack</title>


    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- CSS -->
    <link
        rel="stylesheet"
        href="assets/css/style.css">

    <link
        rel="stylesheet"
        href="assets/css/components.css">

    <link
        rel="stylesheet"
        href="assets/css/payment.css">

</head>


<body>


    <!-- =========================
         NAVBAR
    ========================== -->

    <div id="navbar"></div>



    <!-- =========================
         PAYMENT PAGE
    ========================== -->

    <main class="payment-page">

        <div class="container">

            <div class="payment-header">

                <span class="payment-step">
                    Langkah 2 dari 2
                </span>

                <h1>
                    Pembayaran
                </h1>

                <p>
                    Pilih metode pembayaran untuk menyelesaikan pesanan Anda.
                </p>

            </div>



            <div class="row g-4">


                <!-- =========================
                     PAYMENT METHOD
                ========================== -->

                <div class="col-lg-7">

                    <div class="payment-card">

                        <div class="payment-card-header">

                            <div class="payment-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>

                            <div>

                                <h2>
                                    Metode Pembayaran
                                </h2>

                                <p>
                                    Pilih salah satu metode pembayaran
                                </p>

                            </div>

                        </div>



                        <div class="payment-methods">


                            <!-- QRIS -->

                            <label
                                class="payment-option"
                                for="qris">

                                <input
                                    type="radio"
                                    name="paymentMethod"
                                    id="qris"
                                    value="QRIS">

                                <div class="payment-option-content">

                                    <div class="payment-option-icon">
                                        <i class="bi bi-qr-code"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            QRIS
                                        </strong>

                                        <span>
                                            Bayar menggunakan QRIS
                                        </span>

                                    </div>

                                </div>

                                <i class="bi bi-check-circle-fill payment-check"></i>

                            </label>



                            <!-- COD -->

                            <label
                                class="payment-option"
                                for="cod">

                                <input
                                    type="radio"
                                    name="paymentMethod"
                                    id="cod"
                                    value="COD">

                                <div class="payment-option-content">

                                    <div class="payment-option-icon">
                                        <i class="bi bi-cash-coin"></i>
                                    </div>

                                    <div>

                                        <strong>
                                            Bayar di Tempat
                                        </strong>

                                        <span>
                                            Pembayaran saat pesanan diterima
                                        </span>

                                    </div>

                                </div>

                                <i class="bi bi-check-circle-fill payment-check"></i>

                            </label>

                        </div>



                        <!-- =========================
                             QRIS DETAIL
                        ========================== -->

                        <div id="qrisPayment" style="display: none;">
                            <div class="payment-info">
                                <h3>
                                    <i class="bi bi-qr-code"></i>
                                    Pembayaran QRIS
                                </h3>

                                <p>
                                    Silakan scan kode QRIS berikut untuk melakukan pembayaran.
                                </p>

                                <div class="text-center my-4">
                                    <img
                                        src="assets/images/qris-citra-snack.png"
                                        alt="QRIS Citra Snack"
                                        class="img-fluid"
                                        style="max-width: 280px; border-radius: 12px;">

                                    <p class="mt-3">
                                        <strong>QRIS Citra Snack</strong>
                                    </p>
                                </div>

                                <div class="alert alert-light">
                                    <i class="bi bi-info-circle"></i>
                                    Setelah membayar, kirim bukti pembayaran melalui WhatsApp
                                    untuk verifikasi admin.
                                </div>
                            </div>
                        </div>



                        <!-- =========================
                             COD DETAIL
                        ========================== -->

                        <div
                            id="codPayment"
                            class="payment-detail"
                            style="display: none;">

                            <div class="detail-title">

                                <i class="bi bi-cash-coin"></i>

                                <span>
                                    Bayar di Tempat
                                </span>

                            </div>


                            <div class="cod-info">

                                <i class="bi bi-info-circle"></i>

                                <p>
                                    Pembayaran dilakukan saat pesanan
                                    diterima. Pastikan Anda menyiapkan
                                    uang sesuai total pesanan.
                                </p>

                            </div>

                        </div>



                        <!-- =========================
                             BUTTON
                        ========================== -->

                        <div class="payment-actions">

                            <a
                                href="checkout.php"
                                class="back-button">

                                <i class="bi bi-arrow-left"></i>

                                Kembali

                            </a>


                            <button
                                type="button"
                                id="confirmPayment"
                                class="continue-button">

                                Konfirmasi Pesanan

                                <i class="bi bi-arrow-right"></i>

                            </button>

                        </div>

                    </div>

                </div>



                <!-- =========================
                     ORDER SUMMARY
                ========================== -->

                <div class="col-lg-5">

                    <div class="order-summary payment-summary">

                        <div class="summary-header">

                            <h2>
                                Ringkasan Pesanan
                            </h2>

                            <i class="bi bi-receipt"></i>

                        </div>


                        <div id="paymentItems"
                            class="payment-items">
                        </div>


                        <div class="summary-divider"></div>


                        <div class="summary-total">

                            <span>
                                Total Pembayaran
                            </span>

                            <strong id="paymentTotal">
                                Rp0
                            </strong>

                        </div>


                        <div class="summary-info">

                            <i class="bi bi-shield-check"></i>

                            <span>
                                Pesanan Anda akan diproses setelah
                                pembayaran dikonfirmasi.
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>



    <!-- =========================
         FOOTER
    ========================== -->

    <div id="footer"></div>



    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- Components -->

    <script src="assets/js/components.js"></script>


    <!-- Payment -->

    <script src="assets/js/payment.js?v=4"></script>

</body>

</html>