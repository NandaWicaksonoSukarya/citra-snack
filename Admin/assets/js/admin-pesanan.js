
const API_URL = "../api/admin-orders.php";

// =====================================
// ELEMENT
// =====================================

const ordersTableBody = document.getElementById("ordersTableBody");
const orderCount = document.getElementById("orderCount");
const statusFilter = document.getElementById("statusFilter");

const orderDetailModalElement = document.getElementById("orderDetailModal");
const orderDetailModal = bootstrap.Modal.getOrCreateInstance(orderDetailModalElement);

const saveStatusButton = document.getElementById("saveStatusButton");
const detailOrderStatus = document.getElementById("detailOrderStatus");

let allOrders = [];
let selectedOrder = null;

// =====================================
// API
// =====================================

async function apiResponse(response) {
    const text = await response.text();

    let result;

    try {
        result = JSON.parse(text);
    } catch {
        console.error("Respons API:", text);
        throw new Error("Respons server tidak valid.");
    }

    if (response.status === 401) {
        window.location.href = "login.php";
        throw new Error("Sesi admin berakhir.");
    }

    if (!response.ok || result.success !== true) {
        throw new Error(result.message || "Terjadi kesalahan.");
    }

    return result;
}

// =====================================
// FORMAT
// =====================================

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    })[char]);
}

function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(Number(value) || 0);
}

function formatTanggal(value) {
    if (!value) return "-";

    const date = new Date(value.replace(" ", "T"));

    if (isNaN(date.getTime())) return value;

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    });
}

function statusClass(status) {
    const classes = {
        "Pesanan Diterima": "bg-primary",
        "Sedang Diproses": "bg-warning text-dark",
        "Dalam Pengantaran": "bg-info text-dark",
        "Selesai": "bg-success",
        "Dibatalkan": "bg-danger",
        "Menunggu": "bg-secondary",
        "Diproses": "bg-warning text-dark"
    };

    return classes[status] || "bg-secondary";
}

// =====================================
// LOAD ORDERS
// =====================================

async function loadOrders() {
    ordersTableBody.innerHTML = `
        <tr>
            <td colspan="7" class="empty-state">
                Memuat pesanan...
            </td>
        </tr>
    `;

    try {
        const status = statusFilter.value;

        const params = new URLSearchParams({
            status: status
        });

        const response = await fetch(`${API_URL}?${params}`);
        const result = await apiResponse(response);

        allOrders = result.data || [];

        renderOrders();

    } catch (error) {
        console.error("Load pesanan:", error);

        ordersTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    ${escapeHtml(error.message)}
                </td>
            </tr>
        `;

        orderCount.textContent = "0 pesanan";
    }
}

// =====================================
// RENDER ORDERS
// =====================================

function renderOrders() {
    orderCount.textContent = `${allOrders.length} pesanan`;

    if (allOrders.length === 0) {
        ordersTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    Belum ada pesanan.
                </td>
            </tr>
        `;
        return;
    }

    ordersTableBody.innerHTML = allOrders.map(order => `
        <tr>
            <td>
                <strong>
                    CS-${String(order.ID_Pesanan).padStart(5, "0")}
                </strong>
            </td>

            <td>
                ${escapeHtml(order.Nama_Lengkap)}
            </td>

            <td>
                ${formatTanggal(order.Tanggal_Pesan)}
            </td>

            <td>
                ${formatRupiah(order.Total_Harga)}
            </td>

            <td>
                ${escapeHtml(order.Status_Bayar || "Belum Ada")}
            </td>

            <td>
                <span class="badge ${statusClass(order.Status_Pesanan)}">
                    ${escapeHtml(order.Status_Pesanan)}
                </span>
            </td>

            <td>
                <button
                    type="button"
                    class="btn btn-sm btn-primary btn-detail-order"
                    data-id="${Number(order.ID_Pesanan)}">
                    <i class="bi bi-eye"></i>
                    Detail
                </button>
            </td>
        </tr>
    `).join("");
}

// =====================================
// FILTER
// =====================================

statusFilter.addEventListener("change", loadOrders);

// =====================================
// DETAIL ORDER
// =====================================

async function openOrderDetail(id) {
    try {
        selectedOrder = null;

        document.getElementById("modalOrderId").textContent = "Memuat...";
        document.getElementById("detailItems").textContent = "Memuat item pesanan...";

        orderDetailModal.show();

        const response = await fetch(`${API_URL}?id=${encodeURIComponent(id)}`);
        const result = await apiResponse(response);

        const order = result.data;

        selectedOrder = order;

        document.getElementById("modalOrderId").textContent =
            `CS-${String(order.ID_Pesanan).padStart(5, "0")}`;

        document.getElementById("detailName").textContent =
            order.Nama_Lengkap || "-";

        document.getElementById("detailWhatsapp").textContent =
            order.No_Kontak || "-";

        document.getElementById("detailDate").textContent =
            formatTanggal(order.Tanggal_Dibutuhkan);

        document.getElementById("detailTime").textContent =
            order.Waktu_Dibutuhkan || "-";

        document.getElementById("detailAddress").textContent =
            order.Alamat || "-";

        document.getElementById("detailNotes").textContent =
            order.Catatan || "-";

        document.getElementById("detailTotal").textContent =
            formatRupiah(order.Total_Harga);

        document.getElementById("detailPaymentStatus").textContent =
            order.Status_Bayar || "Belum Ada";

        detailOrderStatus.value = order.Status_Pesanan;

        renderOrderItems(order.items || []);

    } catch (error) {
        console.error("Detail pesanan:", error);
        alert(error.message || "Gagal mengambil detail pesanan.");
        orderDetailModal.hide();
    }
}

// =====================================
// RENDER ITEMS
// =====================================

function renderOrderItems(items) {
    const container = document.getElementById("detailItems");

    if (items.length === 0) {
        container.innerHTML = `
            <p class="text-muted">Belum ada detail item pesanan.</p>
        `;
        return;
    }

    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    ${items.map(item => `
                        <tr>
                            <td>
                                ${escapeHtml(item.Nama_Item)}
                                <small class="text-muted d-block">
                                    ${escapeHtml(item.Jenis_Item)}
                                </small>
                            </td>

                            <td>${Number(item.Jumlah)}</td>

                            <td>${formatRupiah(item.Harga_Satuan)}</td>

                            <td>${formatRupiah(item.Subtotal)}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        </div>
    `;
}

// =====================================
// EVENT DETAIL
// =====================================

ordersTableBody.addEventListener("click", event => {
    const button = event.target.closest(".btn-detail-order");

    if (!button) return;

    openOrderDetail(button.dataset.id);
});

// =====================================
// UPDATE STATUS
// =====================================

saveStatusButton.addEventListener("click", async () => {
    if (!selectedOrder) {
        alert("Data pesanan belum tersedia.");
        return;
    }

    const newStatus = detailOrderStatus.value;

    if (!newStatus) {
        alert("Silakan pilih status pesanan.");
        return;
    }

    const originalText = saveStatusButton.innerHTML;

    saveStatusButton.disabled = true;
    saveStatusButton.innerHTML = `
        <span class="spinner-border spinner-border-sm"></span>
        Menyimpan...
    `;

    try {
        const formData = new FormData();

        formData.append("id", selectedOrder.ID_Pesanan);
        formData.append("status", newStatus);

        const response = await fetch(API_URL, {
            method: "POST",
            body: formData
        });

        const result = await apiResponse(response);

        alert(result.message || "Status berhasil diperbarui.");

        orderDetailModal.hide();

        selectedOrder = null;

        await loadOrders();

    } catch (error) {
        console.error("Update status:", error);
        alert(error.message || "Gagal memperbarui status.");

    } finally {
        saveStatusButton.disabled = false;
        saveStatusButton.innerHTML = originalText;
    }
});

// =====================================
// START
// =====================================

loadOrders();
