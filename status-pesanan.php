<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Status Pesanan - Citra Snack</title>


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
        href="assets/css/status-pesanan.css">

</head>


<body>


    <!-- =========================
         NAVBAR
    ========================== -->

    <div id="navbar"></div>



    <!-- =========================
         STATUS PESANAN
    ========================== -->

    <main class="status-page">

        <div class="container">

            <!-- Header -->

            <div class="status-header">

                <div class="success-icon">

                    <i class="bi bi-check-lg"></i>

                </div>

                <h1>
                    Pesanan Berhasil Dibuat
                </h1>

                <p>
                    Terima kasih telah memesan di Citra Snack.
                    Berikut adalah status pesanan Anda.
                </p>

            </div>



            <!-- Nomor Pesanan -->

            <!-- =========================
     NOMOR PESANAN
========================== -->

<div class="order-number-card">

    <div class="order-number-content">

        <div class="order-number-label">

            <i class="bi bi-receipt"></i>

            <span>
                Nomor Pesanan
            </span>

        </div>


        <strong id="orderId">
            -
        </strong>


        <p>
            Simpan nomor pesanan ini untuk melacak
            status pesanan Anda.
        </p>

    </div>


    <button
        type="button"
        id="copyOrderId"
        class="btn-copy-order">

        <i class="bi bi-copy"></i>

        <span>
            Salin Nomor
        </span>

    </button>

</div>

<div class="tracking-notice">

    <i class="bi bi-info-circle"></i>

    <div>

        <strong>
            Jangan lupa simpan nomor pesanan Anda
        </strong>

        <p>
            Nomor pesanan digunakan bersama nomor WhatsApp
            untuk melihat status pesanan melalui menu
            <strong>Lacak Pesanan</strong>.
        </p>

    </div>

</div>

<!-- =========================
     STATUS PEMBAYARAN
========================== -->

<div class="payment-status-card">

    <div class="payment-status-icon">

        <i class="bi bi-credit-card"></i>

    </div>

    <div class="payment-status-info">

        <span class="payment-status-label">
            Status Pembayaran
        </span>

        <strong
            id="paymentStatus"
            class="payment-status-value">

            Memuat...

        </strong>

    </div>

</div>

<!-- UPLOAD BUKTI PEMBAYARAN -->

<div id="uploadPaymentCard" class="payment-status-card"
     style="display: none;">

    <div class="w-100">

        <h3 class="mb-3">
            <i class="bi bi-cloud-arrow-up"></i>
            Upload Bukti Pembayaran
        </h3>

        <p>
            Silakan unggah bukti pembayaran QRIS untuk
            diverifikasi oleh admin.
        </p>

        <form id="paymentProofForm"
              enctype="multipart/form-data">

            <input
                type="file"
                id="paymentProof"
                name="bukti_pembayaran"
                class="form-control mb-3"
                accept="image/jpeg,image/png,image/webp"
                required>

            <button type="submit"
                    class="btn btn-primary-custom">
                <i class="bi bi-upload"></i>
                Kirim Bukti Pembayaran
            </button>

        </form>

        <div id="proofMessage" class="mt-3"></div>

    </div>

</div>

            <!-- Status -->

            <div class="status-card">

                <h2>
                    Status Pesanan
                </h2>


                <div class="status-timeline">


                    <!-- Status 1 -->

                    <div class="status-step active">

                        <div class="status-icon">

                            <i class="bi bi-receipt"></i>

                        </div>

                        <div class="status-content">

                            <h3>
                                Pesanan Diterima
                            </h3>

                            <p>
                                Pesanan Anda telah diterima oleh Citra Snack.
                            </p>

                        </div>

                    </div>



                    <!-- Status 2 -->

                    <div class="status-step">

                        <div class="status-icon">

                            <i class="bi bi-box-seam"></i>

                        </div>

                        <div class="status-content">

                            <h3>
                                Sedang Diproses
                            </h3>

                            <p>
                                Pesanan sedang dipersiapkan.
                            </p>

                        </div>

                    </div>



                    <!-- Status 3 -->

                    <div class="status-step">

                        <div class="status-icon">

                            <i class="bi bi-truck"></i>

                        </div>

                        <div class="status-content">

                            <h3>
                                Dalam Pengantaran
                            </h3>

                            <p>
                                Pesanan sedang dalam perjalanan.
                            </p>

                        </div>

                    </div>



                    <!-- Status 4 -->

                    <div class="status-step">

                        <div class="status-icon">

                            <i class="bi bi-check-circle"></i>

                        </div>

                        <div class="status-content">

                            <h3>
                                Selesai
                            </h3>

                            <p>
                                Pesanan telah selesai.
                            </p>

                        </div>

                    </div>


                </div>

            </div>



            <!-- Detail Pesanan -->

            <div class="order-detail-card">

                <div class="card-header-custom">

                    <h2>
                        Detail Pesanan
                    </h2>

                </div>


                <div id="orderItems">

                    <!-- Diisi oleh JavaScript -->

                </div>


                <div class="order-total">

                    <span>
                        Total Pesanan
                    </span>

                    <strong id="orderTotal">
                        Rp0
                    </strong>

                </div>

            </div>



            <!-- Data Pelanggan -->

            <div class="customer-detail-card">

                <h2>
                    Informasi Pemesan
                </h2>


                <div class="customer-info">

                    <div>

                        <span>
                            Nama
                        </span>

                        <strong id="customerName">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            WhatsApp
                        </span>

                        <strong id="customerWhatsapp">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            Tanggal Dibutuhkan
                        </span>

                        <strong id="customerDate">
                            -
                        </strong>

                    </div>


                    <div>

                        <span>
                            Waktu
                        </span>

                        <strong id="customerTime">
                            -
                        </strong>

                    </div>


                    <div class="customer-address">

                        <span>
                            Alamat
                        </span>

                        <strong id="customerAddress">
                            -
                        </strong>

                    </div>

                </div>

            </div>



            <!-- Tombol -->

            <div class="status-actions">

                <a
                    href="index.php"
                    class="btn btn-primary-custom">

                    <i class="bi bi-arrow-left"></i>

                    Kembali ke Menu

                </a>

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


    <!-- Status Pesanan -->

    <script src="assets/js/status-pesanan.js?v=2"></script>

</body>

</html>