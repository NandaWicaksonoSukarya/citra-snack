// =====================================================
// AMBIL DATA KERANJANG DAN CHECKOUT
// =====================================================

const cart = JSON.parse(localStorage.getItem("cart")) || [];

const checkoutData =
    JSON.parse(localStorage.getItem("checkoutData")) || null;


// =====================================================
// FORMAT RUPIAH
// =====================================================

function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(number);
}


// =====================================================
// CEK DATA
// =====================================================

if (
    cart.length === 0 ||
    !checkoutData ||
    Object.keys(checkoutData).length === 0
) {
    alert("Data pesanan tidak ditemukan.");
    window.location.href = "index.php";
}


// =====================================================
// TAMPILKAN PESANAN
// =====================================================

function displayPaymentItems() {
    const container = document.getElementById("paymentItems");
    const totalElement = document.getElementById("paymentTotal");

    container.innerHTML = "";

    let total = 0;

    cart.forEach(item => {
        const subtotal = Number(item.price) * Number(item.quantity);
        total += subtotal;

        const element = document.createElement("div");
        element.className = "payment-item";

        element.innerHTML = `
            <div class="payment-item-image">
                <img
                    src="${item.image || ''}"
                    alt="${item.name}"
                    onerror="this.style.display='none';">
            </div>

            <div class="payment-item-info">
                <h3>${item.name}</h3>
                <span>
                    ${item.quantity} × ${formatRupiah(item.price)}
                </span>
            </div>

            <div class="payment-item-price">
                ${formatRupiah(subtotal)}
            </div>
        `;

        container.appendChild(element);
    });

    totalElement.textContent = formatRupiah(total);
}

displayPaymentItems();


// =====================================================
// PILIH METODE PEMBAYARAN
// =====================================================

const paymentMethods = document.querySelectorAll(
    'input[name="paymentMethod"]'
);

const qrisPayment = document.getElementById("qrisPayment");
const codPayment = document.getElementById("codPayment");

paymentMethods.forEach(method => {
    method.addEventListener("change", function () {
        qrisPayment.style.display =
            this.value === "QRIS" ? "block" : "none";

        codPayment.style.display =
            this.value === "COD" ? "block" : "none";
    });
});


// =====================================================
// FUNGSI WHATSAPP
// =====================================================

function bukaWhatsApp(orderId, metodePembayaran, total) {
    const nomorCitraSnack = "6281385277154";

    let daftarPesanan = "";

    cart.forEach(item => {
        const subtotal =
            Number(item.price) * Number(item.quantity);

        daftarPesanan +=
            `- ${item.name} × ${item.quantity} = ${formatRupiah(subtotal)}\n`;
    });

    const pesanWhatsApp =
`Halo Citra Snack 👋

Saya ingin mengonfirmasi pesanan:

Nomor Pesanan:
${orderId}

Nama:
${checkoutData.nama}

No. WhatsApp:
${checkoutData.whatsapp}

Pesanan:
${daftarPesanan}
Total:
${formatRupiah(total)}

Metode Pembayaran:
${metodePembayaran}

Tanggal Dibutuhkan:
${checkoutData.tanggalDibutuhkan}

Waktu:
${checkoutData.waktuDibutuhkan}

Alamat:
${checkoutData.alamat}

Catatan:
${checkoutData.catatan || "-"}

${metodePembayaran === "QRIS"
    ? "Saya akan mengirimkan bukti pembayaran QRIS melalui WhatsApp."
    : "Saya memilih pembayaran COD."}

Terima kasih 🙏`;

    const whatsappURL =
        "https://wa.me/" +
        nomorCitraSnack +
        "?text=" +
        encodeURIComponent(pesanWhatsApp);

    window.open(whatsappURL, "_blank");
}


// =====================================================
// KONFIRMASI PESANAN
// =====================================================

const confirmButton = document.getElementById("confirmPayment");

confirmButton.addEventListener("click", async function () {

    // Cegah klik berulang
    if (confirmButton.disabled) return;

    // ==========================================
    // CEK METODE PEMBAYARAN
    // ==========================================

    const selectedPayment = document.querySelector(
        'input[name="paymentMethod"]:checked'
    );

    if (!selectedPayment) {
        alert("Silakan pilih metode pembayaran terlebih dahulu.");
        return;
    }

    // ==========================================
    // CEK DATA
    // ==========================================

    if (!cart.length || !checkoutData) {
        alert("Data pesanan tidak ditemukan.");
        window.location.href = "index.php";
        return;
    }

    // ==========================================
    // SIAPKAN TOMBOL
    // ==========================================

    const originalButton = confirmButton.innerHTML;

    confirmButton.disabled = true;
    confirmButton.innerHTML = `
        <span class="spinner-border spinner-border-sm"></span>
        Menyimpan Pesanan...
    `;

    try {

        // ==========================================
        // SIMPAN PESANAN KE MYSQL
        // ==========================================

        const response = await fetch("./api/create-order.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                checkoutData: checkoutData,
                cart: cart,
                paymentMethod: selectedPayment.value
            })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(
                result.message || "Gagal menyimpan pesanan."
            );
        }

        // ==========================================
        // AMBIL DATA DARI SERVER
        // ==========================================

        const orderId = result.data.orderId;
        const total = Number(result.data.totalHarga);

        // Simpan nomor pesanan
        localStorage.setItem("orderId", orderId);

        // ==========================================
        // SIMPAN DATA UNTUK HALAMAN STATUS
        // ==========================================

        localStorage.setItem(
            "lastOrder",
            JSON.stringify({
                orderId: orderId,
                totalHarga: total,
                paymentMethod: selectedPayment.value
            })
        );

        // ==========================================
        // HAPUS KERANJANG
        // ==========================================

        localStorage.removeItem("cart");

        // ==========================================
        // BUKA WHATSAPP
        // ==========================================

        bukaWhatsApp(
            orderId,
            selectedPayment.value,
            total
        );

        alert(
            "Pesanan berhasil disimpan!\nNomor Pesanan: " + orderId
        );

        // ==========================================
        // PINDAH KE HALAMAN STATUS
        // ==========================================

        window.location.href = "status-pesanan.php";

    } catch (error) {

        console.error("Gagal membuat pesanan:", error);

        alert(error.message || "Terjadi kesalahan saat menyimpan pesanan.");

        confirmButton.disabled = false;
        confirmButton.innerHTML = originalButton;
    }
});
