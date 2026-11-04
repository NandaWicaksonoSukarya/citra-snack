
// =====================================================
// STATUS PESANAN CITRA SNACK - MYSQL
// =====================================================

const orderId = localStorage.getItem("orderId");

const checkoutData = JSON.parse(
    localStorage.getItem("checkoutData") || "null"
);

// =====================================================
// FORMAT RUPIAH
// =====================================================

function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(Number(number) || 0);
}

// =====================================================
// FORMAT TANGGAL
// =====================================================

function formatTanggal(tanggal) {
    if (!tanggal) return "-";

    const date = new Date(tanggal + "T00:00:00");

    return date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric"
    });
}

// =====================================================
// AMBIL DATA PESANAN DARI MYSQL
// =====================================================

async function loadOrder() {
    if (!orderId || !checkoutData?.whatsapp) {
        alert("Data pesanan tidak ditemukan. Silakan gunakan menu Lacak Pesanan.");
        window.location.href = "index.php";
        return;
    }

    try {
        const response = await fetch("api/order-status.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                orderId: orderId,
                whatsapp: checkoutData.whatsapp
            })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Gagal mengambil data pesanan.");
        }

        displayOrder(result.data);

    } catch (error) {
        console.error("Gagal mengambil pesanan:", error);
        alert(error.message);
    }
}

// =====================================================
// TAMPILKAN DATA PESANAN
// =====================================================

function displayOrder(order) {
    document.getElementById("orderId").textContent =
        order.ID_Pesanan || "-";

    document.getElementById("customerName").textContent =
        order.Nama_Lengkap || "-";

    document.getElementById("customerWhatsapp").textContent =
        order.No_Kontak || "-";

    document.getElementById("customerDate").textContent =
        formatTanggal(order.Tanggal_Dibutuhkan);

    document.getElementById("customerTime").textContent =
        order.Waktu_Dibutuhkan || "-";

    document.getElementById("customerAddress").textContent =
        order.Alamat || "-";

    document.getElementById("orderTotal").textContent =
        formatRupiah(order.Total_Harga);

    displayOrderItems(order.items || []);
    displayPaymentStatus(order);
    updateStatus(order.Status_Pesanan);
}

// =====================================================
// TAMPILKAN DETAIL ITEM
// =====================================================

function displayOrderItems(items) {
    const container = document.getElementById("orderItems");

    container.innerHTML = "";

    if (items.length === 0) {
        container.textContent = "Detail pesanan tidak tersedia.";
        return;
    }

    items.forEach(item => {
        const wrapper = document.createElement("div");
        wrapper.className = "order-item";

        const info = document.createElement("div");
        info.className = "order-item-info";

        const name = document.createElement("h3");
        name.textContent = item.Nama_Item;

        const quantity = document.createElement("span");
        quantity.textContent =
            `${item.Jumlah} × ${formatRupiah(item.Harga_Satuan)}`;

        const subtotal = document.createElement("strong");
        subtotal.textContent = formatRupiah(item.Subtotal);

        info.append(name, quantity);
        wrapper.append(info, subtotal);
        container.appendChild(wrapper);
    });
}

// =====================================================
// STATUS PEMBAYARAN
// =====================================================

function displayPaymentStatus(order) {
    const element = document.getElementById("paymentStatus");

    if (!element) return;

    const status = order.Status_Bayar || "Menunggu";

    element.textContent = status;
    element.className = "payment-status-value";

    if (status === "Berhasil") {
        element.classList.add("paid");
    } else if (status === "Menunggu") {
        element.classList.add("pending");
    } else {
        element.classList.add("failed");
    }

    // Tampilkan area upload jika QRIS belum berhasil dibayar
    const uploadCard = document.getElementById("uploadPaymentCard");

    if (uploadCard) {
        const perluUpload =
            order.Metode_Bayar === "QRIS" &&
            status !== "Berhasil" &&
            !order.Bukti_Pembayaran;

        uploadCard.style.display = perluUpload ? "block" : "none";
    }
}

// =====================================================
// STATUS PESANAN / TIMELINE
// =====================================================

function updateStatus(status) {
    const steps = {
        "Menunggu": 1,
        "Pesanan Diterima": 1,
        "Sedang Diproses": 2,
        "Diproses": 2,
        "Dalam Pengantaran": 3,
        "Selesai": 4
    };

    const currentStep = steps[status] || 1;

    const timelineItems = document.querySelectorAll(".status-step");

    timelineItems.forEach((item, index) => {
        const stepNumber = index + 1;

        item.classList.remove("active", "completed");

        if (stepNumber < currentStep) {
            item.classList.add("completed");
        } else if (stepNumber === currentStep) {
            item.classList.add("active");
        }
    });
}

// =====================================================
// COPY NOMOR PESANAN
// =====================================================

const copyButton = document.getElementById("copyOrderId");

if (copyButton) {
    copyButton.addEventListener("click", async function () {
        const id = document.getElementById("orderId").textContent.trim();

        if (!id || id === "-") return;

        try {
            await navigator.clipboard.writeText(id);

            this.innerHTML = `
                <i class="bi bi-check-lg"></i>
                <span>Tersalin</span>
            `;

            setTimeout(() => {
                this.innerHTML = `
                    <i class="bi bi-copy"></i>
                    <span>Salin Nomor</span>
                `;
            }, 2000);

        } catch (error) {
            alert("Nomor pesanan: " + id);
        }
    });
}

// =====================================================
// JALANKAN
// =====================================================

loadOrder();
