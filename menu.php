<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Menu | Citra Snack</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg sticky-top">

    <div class="container">

        <a class="navbar-brand" href="index.html">
            Citra<span>Snack</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.html">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="menu.html">
                        Menu
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Tentang Kami
                    </a>
                </li>

            </ul>

            <!-- CART -->

            <a href="#" class="cart-button" id="cartButton">

                <i class="bi bi-bag"></i>

                <span>Keranjang</span>

                <span class="cart-count" id="cartCount">
                    0
                </span>

            </a>

        </div>

    </div>

</nav>


<!-- HEADER MENU -->

<section class="menu-header">

    <div class="container">

        <div class="menu-header-content">

            <span class="menu-label">
                CITRA SNACK
            </span>

            <h1>
                Pilihan Menu
                <span>Untuk Acara Kamu</span>
            </h1>

            <p>
                Temukan berbagai pilihan snack, nasi kotak,
                dan ayam ingkung untuk melengkapi berbagai acara.
            </p>

        </div>

    </div>

</section>


<!-- MENU -->

<section class="menu-section">

    <div class="container">


        <!-- SEARCH -->

        <div class="menu-toolbar">

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari produk..."
                >

            </div>


            <!-- CATEGORY -->

            <div class="category-filter">

                <button
                    class="category-btn active"
                    data-category="Semua">

                    Semua

                </button>

                <button
                    class="category-btn"
                    data-category="Snack">

                    Snack

                </button>

                <button
                    class="category-btn"
                    data-category="Nasi Kotak">

                    Nasi Kotak

                </button>

                <button
                    class="category-btn"
                    data-category="Ayam Ingkung">

                    Ayam Ingkung

                </button>

            </div>

        </div>


        <!-- PRODUCT -->

        <div class="row g-4" id="productContainer">

            <!-- PRODUCT AKAN DIBUAT JAVASCRIPT -->

        </div>


        <!-- EMPTY -->

        <div
            class="empty-product"
            id="emptyProduct"
            style="display: none;">

            <i class="bi bi-search"></i>

            <h5>Produk tidak ditemukan</h5>

            <p>
                Coba gunakan kata kunci atau kategori lainnya.
            </p>

        </div>

    </div>

</section>


<!-- CART FLOATING -->

<div class="floating-cart">

    <a href="#" id="floatingCartButton">

        <i class="bi bi-bag"></i>

        <span>
            Keranjang
        </span>

        <strong id="floatingCartCount">
            0
        </strong>

    </a>

</div>

<!-- CART MODAL -->

<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="bi bi-bag me-2"></i>
                    Isi Keranjang
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <!-- BODY -->
            <div class="modal-body">

                <!-- PRODUK KERANJANG -->
                <div id="cartItems">

                    <!-- Produk akan ditampilkan JavaScript -->

                </div>


                <!-- KERANJANG KOSONG -->
                <div
                    id="emptyCart"
                    class="text-center py-5">

                    <i class="bi bi-bag-x"></i>

                    <h6 class="mt-3">
                        Keranjang masih kosong
                    </h6>

                    <p>
                        Silakan pilih produk terlebih dahulu.
                    </p>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <div class="cart-summary">

                    <span>
                        Total Keseluruhan
                    </span>

                    <strong id="cartTotal">
                        Rp0
                    </strong>

                </div>

                <button
                    type="button"
                    class="btn checkout-btn"
                    id="checkoutButton">

                    <i class="bi bi-cart-check me-1"></i>
                    Buat Pesanan

                </button>

            </div>

        </div>

    </div>

</div>

<!-- FOOTER -->

<footer>

    <div class="container">

        <div class="row">

            <div class="col-md-6">

                <h5>CitraSnack</h5>

                <p>
                    Menyediakan snack, nasi kotak,
                    dan ayam ingkung untuk berbagai kebutuhan acara.
                </p>

            </div>

            <div class="col-md-3">

                <h5>Menu</h5>

                <p>
                    Snack<br>
                    Nasi Kotak<br>
                    Ayam Ingkung
                </p>

            </div>

            <div class="col-md-3">

                <h5>Kontak</h5>

                <p>
                    <i class="bi bi-whatsapp"></i>
                    WhatsApp<br>

                    <i class="bi bi-geo-alt"></i>
                    Lokasi Citra Snack
                </p>

            </div>

        </div>

        <div class="footer-bottom">

            © 2026 Citra Snack. All Rights Reserved.

        </div>

    </div>

</footer>


<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS Menu -->

<script src="assets/js/menu.js"></script>

</body>
</html>