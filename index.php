<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Menu | Citra Snack</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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

    <!-- CSS Utama -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- CSS Menu -->
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>


<body>
<div id="navbar"></div>
<!-- =====================================================
     MENU PAGE
===================================================== -->

<main class="menu-page">

    <div class="container">

        <div class="row g-4">


            <!-- =================================================
                 BAGIAN MENU
            ================================================== -->

            <div class="col-lg-8">


                <!-- TITLE -->

                <div class="menu-title">

                    <div>

                        <span class="menu-label">
                            CITRA SNACK
                        </span>

                        <h1>
                            Menu Digital
                        </h1>

                        <p>
                            Pilih berbagai menu favorit untuk kebutuhan
                            acara dan pesanan Anda.
                        </p>

                    </div>

                </div>



                <!-- =================================================
                     TOOLBAR
                ================================================== -->

                <div class="menu-toolbar">


                    <!-- CATEGORY FILTER -->

                    <div class="category-filter">

                        <button
                            type="button"
                            class="category-btn active"
                            data-category="Semua">

                            Semua

                        </button>


                        <button
                            type="button"
                            class="category-btn"
                            data-category="Snack">

                            Snack

                        </button>


                        <button
                            type="button"
                            class="category-btn"
                            data-category="Nasi Kotak">

                            Nasi Kotak

                        </button>


                        <button
                            type="button"
                            class="category-btn"
                            data-category="Ayam Ingkung">

                            Ayam Ingkung

                        </button>

                    </div>



                    <!-- SEARCH -->

                    <div class="search-box">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Cari menu..."
                            autocomplete="off">

                    </div>

                </div>



                <!-- =================================================
                     PRODUCT CONTAINER
                ================================================== -->

                <div
                    class="row g-3"
                    id="productContainer">

                    <!--
                        Produk akan dibuat
                        melalui JavaScript
                    -->

                </div>



                <!-- =================================================
                     EMPTY PRODUCT
                ================================================== -->

                <div
                    class="empty-product"
                    id="emptyProduct"
                    style="display: none;">

                    <i class="bi bi-search"></i>

                    <h5>
                        Produk tidak ditemukan
                    </h5>

                    <p>
                        Coba gunakan kata kunci atau kategori lainnya.
                    </p>

                </div>

            </div>



            <!-- =================================================
                 KERANJANG
            ================================================== -->

            <div class="col-lg-4">

                <div class="cart-panel">


                    <!-- =================================================
                         CART HEADER
                    ================================================== -->

                    <div class="cart-panel-header">

                        <div>

                            <span class="cart-label">
                                PESANAN
                            </span>

                            <h2>
                                Keranjang
                            </h2>

                        </div>


                        <button
                            type="button"
                            id="clearCart">

                            Kosongkan

                        </button>

                    </div>



                    <!-- =================================================
                         CART ITEMS
                    ================================================== -->

                    <div
                        class="cart-items"
                        id="cartItems">

                        <!--
                            Isi keranjang
                            dibuat oleh JavaScript
                        -->

                    </div>



                    <!-- =================================================
                         EMPTY CART
                    ================================================== -->

                    <div
                        class="empty-cart"
                        id="emptyCart">

                        <i class="bi bi-bag"></i>

                        <p>
                            Keranjang masih kosong
                        </p>

                        <small>
                            Pilih menu untuk menambahkan pesanan.
                        </small>

                    </div>



                    <!-- =================================================
                         CART FOOTER
                    ================================================== -->

                    <div class="cart-footer">


                        <!-- TOTAL -->

                        <div class="cart-total">

                            <span>
                                Total:
                            </span>

                            <strong id="cartTotal">
                                Rp0
                            </strong>

                        </div>



                        <!-- CHECKOUT -->

                        <button
                            type="button"
                            class="checkout-btn"
                            id="checkoutButton">

                            <span>
                                Buat Pesanan
                            </span>

                            <i class="bi bi-arrow-right"></i>

                        </button>

                    </div>

                </div>

            </div>


        </div>

    </div>
</main>
<div id="footer"></div>


<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>



<!-- =====================================================
     MENU JS
===================================================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/menu.js"></script>
<script src="assets/js/components.js"></script>

</body>

</html>