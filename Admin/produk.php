<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Pastikan admin sudah login
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

    <title>Produk - Admin Citra Snack</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="assets/css/admin-produk.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
</head>

<body>

    <div class="admin-layout">

        <!-- SIDEBAR -->
       <?php include __DIR__ . "/sidebar.php"; ?>


        <!-- MAIN -->
        <main class="main-content">

            <div class="page-header">

                <div>
                    <h1>Produk</h1>

                    <p>
                        Kelola produk dan harga Citra Snack.
                    </p>
                </div>

                <button
                    type="button"
                    class="btn-add-product"
                    id="addProductButton">

                    <i class="bi bi-plus-lg"></i>
                    Tambah Produk

                </button>

            </div>


            <!-- TOOLBAR -->

            <div class="product-toolbar">

                <div class="product-count">

                    <i class="bi bi-box-seam"></i>

                    <span id="productCount">
                        0 produk
                    </span>

                </div>

            </div>


            <!-- TABLE -->

            <div class="product-card">

                <div class="table-responsive">

                    <table class="product-table">

                        <thead>

                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <tbody id="productsTableBody">

                            <tr>

                                <td
                                    colspan="5"
                                    class="loading-state">

                                    <div
                                        class="spinner-border spinner-border-sm">
                                    </div>

                                    Memuat produk...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </main>

    </div>


    <!-- =========================
         MODAL TAMBAH / EDIT
    ========================== -->

    <div
        class="modal fade"
        id="productModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <div>

                        <h5
                            class="modal-title"
                            id="productModalTitle">

                            Tambah Produk

                        </h5>

                        <p class="modal-subtitle">

                            Isi informasi produk di bawah.

                        </p>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <form id="productForm">

                        <input
                            type="hidden"
                            id="productId">


                        <!-- NAMA -->

                        <div class="form-group">

                            <label for="namaProduk">
                                Nama Produk
                            </label>

                            <input
                                type="text"
                                id="namaProduk"
                                class="form-control"
                                placeholder="Contoh: Snack Box Premium"
                                required>

                        </div>


                        <!-- KATEGORI -->

                        <div class="form-group">

                            <label for="kategori">
                                Kategori
                            </label>

                            <select
                                id="kategori"
                                class="form-control"
                                required>

                                <option value="">
                                    Pilih kategori
                                </option>

                                <option value="Snack">
                                    Snack
                                </option>

                                <option value="Nasi Kotak">
                                    Nasi Kotak
                                </option>

                                <option value="Ayam Ingkung">
                                    Ayam Ingkung
                                </option>

                            </select>

                        </div>


                        <!-- HARGA + STOK -->

                        <div class="form-row">

                            <div class="form-group">

                                <label for="harga">
                                    Harga
                                </label>

                                <div class="input-price">

                                    <span>Rp</span>

                                    <input
                                        type="number"
                                        id="harga"
                                        class="form-control"
                                        placeholder="7000"
                                        min="0"
                                        required>

                                </div>

                            </div>


                            <div class="form-group">

                                <label for="stok">
                                    Stok
                                </label>

                                <input
                                    type="number"
                                    id="stok"
                                    class="form-control"
                                    placeholder="100"
                                    min="0"
                                    required>

                            </div>

                        </div>


                        <!-- DESKRIPSI -->

                        <div class="form-group">

                            <label for="deskripsi">
                                Deskripsi
                            </label>

                            <textarea
                                id="deskripsi"
                                class="form-control"
                                rows="3"
                                placeholder="Deskripsi produk..."></textarea>

                        </div>


                        <!-- FOTO -->

                        <div class="form-group">

                            <label for="foto">
                                Foto Produk
                            </label>

                            <input
                                type="file"
                                id="foto"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp">

                            <small>
                                Pilih JPG, PNG, atau WebP.
                                Maksimal 5 MB.
                            </small>


                            <!-- PREVIEW -->

                           <div
    id="imagePreview"
    style="
        display: none;
        margin-top: 8px;
        width: 90px;
        height: 70px;
        padding: 4px;
        overflow: hidden;
        background: #faf9f5;
        border: 1px solid #eee8dc;
        border-radius: 8px;
    ">

    <img
        id="previewImage"
        src=""
        alt="Preview Produk"
        style="
            display: block;
            width: 80px;
            height: 60px;
            max-width: 80px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 6px;
        ">

</div>

                        </div>

                    </form>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn-cancel"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="button"
                        class="btn-save"
                        id="saveProductButton">

                        <i class="bi bi-check-lg"></i>

                        Simpan Produk

                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================
         MODAL HAPUS
    ========================== -->

    <div
        class="modal fade"
        id="deleteModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content delete-modal">

                <div class="delete-icon">

                    <i class="bi bi-trash"></i>

                </div>

                <h5>
                    Hapus Produk?
                </h5>

                <p>

                    Produk
                    <strong id="deleteProductName">
                        -
                    </strong>

                    akan dihapus dari katalog.

                </p>

                <div class="delete-actions">

                    <button
                        type="button"
                        class="btn-cancel"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="button"
                        class="btn-delete"
                        id="confirmDeleteButton">

                        Hapus Produk

                    </button>

                </div>

            </div>

        </div>

    </div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/admin-produk.js?v=4"></script>
<script src="assets/js/sidebar.js?v=11"></script>
</body>

</html>