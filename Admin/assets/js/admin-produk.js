
const API_URL = "../api/admin-products.php";

// =====================================
// ELEMENT
// =====================================

const productsTableBody = document.getElementById("productsTableBody");
const productCount = document.getElementById("productCount");

const addProductButton = document.getElementById("addProductButton");
const productForm = document.getElementById("productForm");

const productId = document.getElementById("productId");
const namaProduk = document.getElementById("namaProduk");
const kategori = document.getElementById("kategori");
const harga = document.getElementById("harga");
const stok = document.getElementById("stok");
const deskripsi = document.getElementById("deskripsi");
const foto = document.getElementById("foto");

const imagePreview = document.getElementById("imagePreview");
const previewImage = document.getElementById("previewImage");

const productModalElement = document.getElementById("productModal");
const productModal = bootstrap.Modal.getOrCreateInstance(productModalElement);

const deleteModalElement = document.getElementById("deleteModal");
const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteModalElement);

const productModalTitle = document.getElementById("productModalTitle");
const saveProductButton = document.getElementById("saveProductButton");

const deleteProductName = document.getElementById("deleteProductName");
const confirmDeleteButton = document.getElementById("confirmDeleteButton");

let allProducts = [];
let selectedProduct = null;

// =====================================
// API RESPONSE
// =====================================

async function readApiResponse(response) {
    const text = await response.text();

    let result;

    try {
        result = JSON.parse(text);
    } catch (error) {
        console.error("Respons API bukan JSON:", text);
        throw new Error("Respons server tidak valid. Periksa PHP API.");
    }

    if (!response.ok || result.success !== true) {
        throw new Error(
            result.message || `Server error (${response.status})`
        );
    }

    return result;
}

// =====================================
// FORMAT
// =====================================

function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(Number(number) || 0);
}

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    })[char]);
}

// =====================================
// LOAD PRODUCTS
// =====================================

async function loadProducts() {
    try {
        const response = await fetch(API_URL);
        const result = await readApiResponse(response);

        allProducts = (result.data || []).map(item => ({
            id: Number(item.ID_Produk),
            nama_produk: item.Nama_Produk,
            kategori: item.Kategori || "",
            harga: Number(item.Harga),
            stok: Number(item.Stok),
            foto: item.Foto_Produk || "",
            deskripsi: item.Deskripsi || ""
        }));

        renderProducts();

    } catch (error) {
        console.error("Load produk:", error);

        productsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="empty-state">
                    Gagal memuat data produk.
                </td>
            </tr>
        `;
    }
}

// =====================================
// RENDER PRODUCTS
// =====================================

function renderProducts() {
    productCount.textContent = `${allProducts.length} produk`;

    if (allProducts.length === 0) {
        productsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="empty-state">
                    Belum ada produk.
                </td>
            </tr>
        `;
        return;
    }

    productsTableBody.innerHTML = allProducts.map(product => `
        <tr>
            <td>
                <div class="product-info">
                    ${product.foto ? `
                        <img
                            src="../${escapeHtml(product.foto)}"
                            class="product-image"
                            alt="${escapeHtml(product.nama_produk)}"
                            onerror="this.style.display='none'">
                    ` : ""}

                    <div>
                        <span class="product-name">
                            ${escapeHtml(product.nama_produk)}
                        </span>

                        <span class="product-description">
                            ${escapeHtml(product.deskripsi || "-")}
                        </span>
                    </div>
                </div>
            </td>

            <td>
                <span class="category-badge">
                    ${escapeHtml(product.kategori)}
                </span>
            </td>

            <td>
                <span class="product-price">
                    ${formatRupiah(product.harga)}
                </span>
            </td>

            <td>
                <span class="${product.stok <= 0 ? "stock-empty" : "product-stock"}">
                    ${product.stok}
                </span>
            </td>

            <td>
                <div class="product-actions">
                    <button
                        type="button"
                        class="btn-action btn-edit"
                        data-id="${product.id}"
                        title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button
                        type="button"
                        class="btn-action btn-delete-product"
                        data-id="${product.id}"
                        title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join("");
}

// =====================================
// ADD PRODUCT
// =====================================

addProductButton.addEventListener("click", () => {
    resetForm();

    productModalTitle.textContent = "Tambah Produk";

    saveProductButton.innerHTML = `
        <i class="bi bi-check-lg"></i>
        Simpan Produk
    `;

    productModal.show();
});

// =====================================
// IMAGE PREVIEW
// =====================================

foto.addEventListener("change", () => {
    const file = foto.files[0];

    if (!file) {
        imagePreview.style.display = "none";
        previewImage.src = "";
        return;
    }

    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if (!allowedTypes.includes(file.type)) {
        alert("Format foto harus JPG, PNG, atau WebP.");
        foto.value = "";
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        alert("Ukuran foto maksimal 5 MB.");
        foto.value = "";
        return;
    }

    const reader = new FileReader();

    reader.onload = event => {
        previewImage.src = event.target.result;
        imagePreview.style.display = "block";
    };

    reader.readAsDataURL(file);
});

// =====================================
// EDIT PRODUCT
// =====================================

function openEditProduct(id) {
    const product = allProducts.find(
        item => item.id === Number(id)
    );

    if (!product) return;

    selectedProduct = product;

    productId.value = product.id;
    namaProduk.value = product.nama_produk;
    kategori.value = product.kategori;
    harga.value = product.harga;
    stok.value = product.stok;
    deskripsi.value = product.deskripsi;

    foto.value = "";

    if (product.foto) {
        previewImage.src = "../" + product.foto;
        imagePreview.style.display = "block";
    } else {
        imagePreview.style.display = "none";
    }

    productModalTitle.textContent = "Edit Produk";

    saveProductButton.innerHTML = `
        <i class="bi bi-check-lg"></i>
        Simpan Perubahan
    `;

    productModal.show();
}

// =====================================
// SAVE PRODUCT
// =====================================

saveProductButton.addEventListener("click", async () => {
    if (!productForm.checkValidity()) {
        productForm.reportValidity();
        return;
    }

    const file = foto.files[0];
    const isEdit = Boolean(productId.value);

    if (!isEdit && !file) {
        alert("Silakan pilih foto produk.");
        return;
    }

    const originalText = saveProductButton.innerHTML;

    saveProductButton.disabled = true;
    saveProductButton.innerHTML = `
        <span class="spinner-border spinner-border-sm"></span>
        Menyimpan...
    `;

    try {
        const formData = new FormData();

        formData.append("action", isEdit ? "update" : "create");
        formData.append("id", productId.value);
        formData.append("nama_produk", namaProduk.value.trim());
        formData.append("kategori", kategori.value);
        formData.append("harga", harga.value);
        formData.append("stok", stok.value);
        formData.append("deskripsi", deskripsi.value.trim());

        if (selectedProduct?.foto) {
            formData.append("foto_lama", selectedProduct.foto);
        }

        if (file) {
            formData.append("foto", file);
        }

        const response = await fetch(API_URL, {
            method: "POST",
            body: formData
        });

        const result = await readApiResponse(response);

        alert(result.message || "Produk berhasil disimpan.");

        productModal.hide();
        resetForm();

        await loadProducts();

    } catch (error) {
        console.error("Simpan produk:", error);
        alert(error.message || "Gagal menyimpan produk.");

    } finally {
        saveProductButton.disabled = false;
        saveProductButton.innerHTML = originalText;
    }
});

// =====================================
// DELETE PRODUCT
// =====================================

function openDeleteProduct(id) {
    const product = allProducts.find(
        item => item.id === Number(id)
    );

    if (!product) return;

    selectedProduct = product;
    deleteProductName.textContent = product.nama_produk;

    deleteModal.show();
}

confirmDeleteButton.addEventListener("click", async () => {
    if (!selectedProduct) return;

    confirmDeleteButton.disabled = true;
    confirmDeleteButton.textContent = "Menghapus...";

    try {
        const formData = new FormData();

        formData.append("action", "delete");
        formData.append("id", selectedProduct.id);

        const response = await fetch(API_URL, {
            method: "POST",
            body: formData
        });

        const result = await readApiResponse(response);

        alert(result.message || "Produk berhasil dihapus.");

        deleteModal.hide();
        selectedProduct = null;

        await loadProducts();

    } catch (error) {
        console.error("Hapus produk:", error);
        alert(error.message || "Gagal menghapus produk.");

    } finally {
        confirmDeleteButton.disabled = false;
        confirmDeleteButton.textContent = "Hapus Produk";
    }
});

// =====================================
// EVENT EDIT & DELETE
// =====================================

productsTableBody.addEventListener("click", event => {
    const editButton = event.target.closest(".btn-edit");
    const deleteButton = event.target.closest(".btn-delete-product");

    if (editButton) {
        openEditProduct(editButton.dataset.id);
    }

    if (deleteButton) {
        openDeleteProduct(deleteButton.dataset.id);
    }
});

// =====================================
// RESET FORM
// =====================================

function resetForm() {
    productForm.reset();

    productId.value = "";
    selectedProduct = null;

    imagePreview.style.display = "none";
    previewImage.src = "";
}

// =====================================
// START
// =====================================

loadProducts();
