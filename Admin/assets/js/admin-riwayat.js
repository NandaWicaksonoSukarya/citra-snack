
const API_URL = "../api/admin-history.php";

// =========================
// DOM
// =========================

const historyTableBody = document.getElementById("historyTableBody");
const orderCount = document.getElementById("orderCount");
const statusFilter = document.getElementById("statusFilter");
const searchInput = document.getElementById("searchInput");

// =========================
// MODAL
// =========================

const historyModal = new bootstrap.Modal(
    document.getElementById("historyModal")
);

// =========================
// DETAIL DOM
// =========================

const modalOrderId = document.getElementById("modalOrderId");
const detailName = document.getElementById("detailName");
const detailWhatsapp = document.getElementById("detailWhatsapp");
const detailDate = document.getElementById("detailDate");
const detailTime = document.getElementById("detailTime");
const detailAddress = document.getElementById("detailAddress");
const detailItems = document.getElementById("detailItems");
const detailTotal = document.getElementById("detailTotal");
const detailPaymentStatus = document.getElementById("detailPaymentStatus");
const detailNotes = document.getElementById("detailNotes");

// =========================
// DATA
// =========================

let allOrders = [];

// =========================
// API REQUEST
// =========================

async function apiRequest(url) {
    const response = await fetch(url);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Gagal mengambil data.");
    }

    return result.data;
}

// =========================
// LOAD HISTORY
// =========================

async function loadHistory() {
    historyTableBody.innerHTML = `
        <tr>
            <td colspan="7" class="empty-state">
                <i class="bi bi-hourglass-split"></i>
                <p>Memuat riwayat pesanan...</p>
            </td>
        </tr>
    `;

    try {
        allOrders = await apiRequest(
            `${API_URL}?action=list`
        );

        renderHistory();

    } catch (error) {
        console.error("Gagal memuat riwayat:", error);

        historyTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>${escapeHtml(error.message)}</p>
                </td>
            </tr>
        `;

        orderCount.textContent = "0 pesanan";
    }
}

// =========================
// RENDER HISTORY
// =========================

function renderHistory() {
    const filter = statusFilter.value;
    const search = searchInput.value.trim().toLowerCase();

    const filteredOrders = allOrders.filter(order => {
        const matchStatus =
            filter === "semua" ||
            order.Status_Pesanan === filter;

        const orderId = String(order.ID_Pesanan || "").toLowerCase();

        const customerName = String(
            order.Nama_Lengkap || ""
        ).toLowerCase();

        const matchSearch =
            !search ||
            orderId.includes(search) ||
            customerName.includes(search);

        return matchStatus && matchSearch;
    });

    orderCount.textContent =
        `${filteredOrders.length} pesanan`;

    if (filteredOrders.length === 0) {
        historyTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <i class="bi bi-clock-history"></i>
                    <p>Tidak ada riwayat pesanan.</p>
                </td>
            </tr>
        `;
        return;
    }

    historyTableBody.innerHTML = filteredOrders.map(order => {
        const paymentStatus =
            order.Status_Bayar || "Menunggu";

        const orderStatus = order.Status_Pesanan;

        return `
            <tr>
                <td>
                    <span class="order-id">
                        #${escapeHtml(order.ID_Pesanan)}
                    </span>
                </td>

                <td>
                    ${escapeHtml(order.Nama_Lengkap)}
                </td>

                <td>
                    ${formatDate(order.Tanggal_Pesan)}
                </td>

                <td>
                    <strong>
                        ${formatRupiah(order.Total_Harga)}
                    </strong>
                </td>

                <td>
                    ${paymentBadge(paymentStatus)}
                </td>

                <td>
                    ${orderStatusBadge(orderStatus)}
                </td>

                <td>
                    <button
                        type="button"
                        class="detail-button"
                        data-order-id="${Number(order.ID_Pesanan)}">

                        <i class="bi bi-eye"></i>
                        Detail
                    </button>
                </td>
            </tr>
        `;
    }).join("");
}

// =========================
// OPEN DETAIL
// =========================

async function openDetail(id) {
    try {
        const order = await apiRequest(
            `${API_URL}?action=detail&id=${encodeURIComponent(id)}`
        );

        modalOrderId.textContent =
            `Pesanan #${order.ID_Pesanan}`;

        detailName.textContent =
            order.Nama_Lengkap || "-";

        detailWhatsapp.textContent =
            order.No_Kontak || "-";

        detailDate.textContent =
            formatDate(order.Tanggal_Dibutuhkan);

        detailTime.textContent =
            order.Waktu_Dibutuhkan || "-";

        detailAddress.textContent =
            order.Alamat || "-";

        detailNotes.textContent =
            order.Catatan || "-";

        detailTotal.textContent =
            formatRupiah(order.Total_Harga);

        detailPaymentStatus.innerHTML =
            paymentBadge(order.Status_Bayar || "Menunggu");

        renderItems(order.items || []);

        historyModal.show();

    } catch (error) {
        console.error("Gagal membuka detail:", error);
        alert(error.message);
    }
}

// =========================
// EVENT DETAIL BUTTON
// =========================

historyTableBody.addEventListener("click", event => {
    const button = event.target.closest(".detail-button");

    if (!button) return;

    const id = button.dataset.orderId;

    openDetail(id);
});

// =========================
// RENDER ITEMS
// =========================

function renderItems(items) {
    if (!Array.isArray(items) || items.length === 0) {
        detailItems.innerHTML = "<p>Tidak ada data produk.</p>";
        return;
    }

    detailItems.innerHTML = items.map(item => {
        const name = item.Nama_Item || "Produk";
        const quantity = Number(item.Jumlah || 0);
        const price = Number(item.Harga_Satuan || 0);
        const subtotal = Number(item.Subtotal || price * quantity);

        return `
            <div class="detail-item">
                <div>
                    <div class="detail-item-name">
                        ${escapeHtml(name)}
                    </div>

                    <div class="detail-item-qty">
                        ${quantity} x ${formatRupiah(price)}
                    </div>
                </div>

                <div class="detail-item-price">
                    ${formatRupiah(subtotal)}
                </div>
            </div>
        `;
    }).join("");
}

// =========================
// PAYMENT BADGE
// =========================

function paymentBadge(status) {
    const normalized = String(status).toLowerCase();

    let className = "status-menunggu";

    if (
        normalized.includes("dibayar") ||
        normalized.includes("lunas") ||
        normalized.includes("paid")
    ) {
        className = "status-dibayar";
    } else if (
        normalized.includes("gagal") ||
        normalized.includes("failed")
    ) {
        className = "status-gagal";
    }

    return `
        <span class="status-badge ${className}">
            ${escapeHtml(status)}
        </span>
    `;
}

// =========================
// ORDER STATUS BADGE
// =========================

function orderStatusBadge(status) {
    const className =
        status === "Dibatalkan"
            ? "status-dibatalkan"
            : "status-selesai";

    return `
        <span class="status-badge ${className}">
            ${escapeHtml(status)}
        </span>
    `;
}

// =========================
// FORMAT RUPIAH
// =========================

function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(Number(value) || 0);
}

// =========================
// FORMAT DATE
// =========================

function formatDate(value) {
    if (!value) return "-";

    const date = new Date(
        String(value).replace(" ", "T")
    );

    if (isNaN(date.getTime())) return "-";

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    });
}

// =========================
// ESCAPE HTML
// =========================

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// =========================
// FILTER
// =========================

statusFilter.addEventListener("change", renderHistory);
searchInput.addEventListener("input", renderHistory);

// =========================
// START
// =========================

loadHistory();
