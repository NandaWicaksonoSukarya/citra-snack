<?php
session_start();

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/config/database.php';

// Cek keranjang dari browser dilakukan melalui JavaScript.
// PHP akan menangani penyimpanan pesanan melalui API.
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout | Citra Snack</title>


    <!-- Google Font -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
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
        href="assets/css/checkout.css">

</head>


<body>


    <!-- =====================================================
     NAVBAR
===================================================== -->

    <div id="navbar"></div>



    <!-- =====================================================
     CHECKOUT
===================================================== -->

    <main class="checkout-page">

        <div class="container">


            <!-- PAGE TITLE -->

            <div class="checkout-title">

                <span class="checkout-label">
                    CITRA SNACK
                </span>

                <h1>
                    Buat Pesanan
                </h1>

                <p>
                    Lengkapi data pesanan Anda sebelum melanjutkan
                    ke pembayaran.
                </p>

            </div>



            <div class="row g-4">


                <!-- =================================================
                 FORM DATA PELANGGAN
            ================================================== -->

                <div class="col-lg-7">

                    <div class="checkout-card">


                        <div class="checkout-card-header">

                            <div>

                                <span>
                                    LANGKAH 1
                                </span>

                                <h2>
                                    Data Pesanan
                                </h2>

                            </div>

                        </div>



                        <form id="checkoutForm">


                            <!-- NAMA -->

                            <div class="form-group">

                                <label for="nama">

                                    Nama Lengkap

                                    <span>*</span>

                                </label>

                                <input
                                    type="text"
                                    id="nama"
                                    class="form-control"
                                    placeholder="Masukkan nama lengkap"
                                    required>

                            </div>



                            <!-- WHATSAPP -->

                            <div class="form-group">

                                <label for="whatsapp">

                                    Nomor WhatsApp

                                    <span>*</span>

                                </label>

                                <div class="input-with-icon">

                                    <i class="bi bi-whatsapp"></i>

                                    <input
                                        type="tel"
                                        id="whatsapp"
                                        class="form-control"
                                        placeholder="Contoh: 081234567890"
                                        required>

                                </div>

                                <small>
                                    Nomor ini digunakan untuk menghubungi Anda
                                    terkait pesanan.
                                </small>

                            </div>



                            <!-- TANGGAL PEMESANAN -->

                            <div class="form-group">

                                <label for="tanggalPemesanan">

                                    Tanggal Pemesanan

                                </label>

                                <input
                                    type="text"
                                    id="tanggalPemesanan"
                                    class="form-control"
                                    readonly>

                                <small>
                                    Tanggal diisi otomatis sesuai tanggal
                                    saat pesanan dibuat.
                                </small>

                            </div>



                            <!-- TANGGAL DIBUTUHKAN -->

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label for="tanggalDibutuhkan">

                                            Tanggal Pesanan Dibutuhkan

                                            <span>*</span>

                                        </label>

                                        <input
                                            type="date"
                                            id="tanggalDibutuhkan"
                                            class="form-control"
                                            required>

                                    </div>

                                </div>



                                <!-- WAKTU -->

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label for="waktuDibutuhkan">

                                            Waktu Dibutuhkan

                                            <span>*</span>

                                        </label>

                                        <input
                                            type="time"
                                            id="waktuDibutuhkan"
                                            class="form-control"
                                            required>

                                    </div>

                                </div>

                            </div>



                            <!-- ALAMAT -->

                            <div class="form-group">

                                <label for="alamat">

                                    Alamat Pengantaran

                                    <span>*</span>

                                </label>

                                <textarea
                                    id="alamat"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Masukkan alamat lengkap pengantaran"
                                    required></textarea>

                            </div>



                            <!-- CATATAN -->

                            <div class="form-group">

                                <label for="catatan">

                                    Catatan Tambahan

                                    <small>
                                        (Opsional)
                                    </small>

                                </label>

                                <textarea
                                    id="catatan"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Contoh: Untuk acara ulang tahun, pisahkan snack menjadi 2 bagian."></textarea>

                            </div>



                            <!-- BUTTON -->

                            <div class="checkout-actions">

                                <a
                                    href="index.php"
                                    class="back-button">

                                    <i class="bi bi-arrow-left"></i>

                                    Kembali ke Menu

                                </a>


                                <button
                                    type="submit"
                                    class="continue-button">

                                    Lanjut ke Pembayaran

                                    <i class="bi bi-arrow-right"></i>

                                </button>

                            </div>

                        </form>

                    </div>

                </div>



                <!-- =================================================
                 RINGKASAN PESANAN
            ================================================== -->

                <div class="col-lg-5">

                    <div class="order-summary">


                        <div class="order-summary-header">

                            <div>

                                <span>
                                    PESANAN ANDA
                                </span>

                                <h2>
                                    Ringkasan Pesanan
                                </h2>

                            </div>

                        </div>



                        <!-- ITEMS -->

                        <div
                            class="checkout-items"
                            id="checkoutItems">

                            <!-- Produk dari localStorage -->

                        </div>



                        <!-- TOTAL -->

                        <div class="checkout-total">

                            <span>
                                Total Pesanan
                            </span>

                            <strong id="checkoutTotal">
                                Rp0
                            </strong>

                        </div>



                        <!-- INFO -->

                        <div class="checkout-info">

                            <i class="bi bi-info-circle"></i>

                            <p>
                                Pastikan data pesanan sudah benar
                                sebelum melanjutkan ke pembayaran.
                            </p>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </main>



    <!-- =====================================================
     FOOTER
===================================================== -->

    <div id="footer"></div>



    <!-- =====================================================
     BOOTSTRAP
===================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =====================================================
     COMPONENTS
===================================================== -->

    <script src="assets/js/components.js"></script>


    <!-- =====================================================
     CHECKOUT JS
===================================================== -->

    <script src="assets/js/checkout.js"></script>


</body>

</html>