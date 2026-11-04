
// =====================================================
// AMBIL DATA KERANJANG
// =====================================================

const cart =
    JSON.parse(localStorage.getItem("cart")) || [];


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
// CEK KERANJANG
// =====================================================

if (cart.length === 0) {
    alert(
        "Keranjang masih kosong. Silakan pilih menu terlebih dahulu."
    );

    window.location.href = "index.php";
}


// =====================================================
// TAMPILKAN TANGGAL PEMESANAN
// =====================================================

function setTanggalPemesanan() {
    const today = new Date();

    const options = {
        day: "numeric",
        month: "long",
        year: "numeric"
    };

    const formattedDate =
        today.toLocaleDateString("id-ID", options);

    document.getElementById("tanggalPemesanan").value =
        formattedDate;
}

setTanggalPemesanan();


// =====================================================
// SET MINIMUM TANGGAL PESANAN DIBUTUHKAN
// =====================================================

function setMinimumDate() {
    const today = new Date();

    const year = today.getFullYear();

    const month = String(today.getMonth() + 1)
        .padStart(2, "0");

    const day = String(today.getDate())
        .padStart(2, "0");

    const minimumDate = `${year}-${month}-${day}`;

    document.getElementById("tanggalDibutuhkan").min =
        minimumDate;
}

setMinimumDate();


// =====================================================
// TAMPILKAN RINGKASAN PESANAN
// =====================================================

function displayCheckoutItems() {
    const container =
        document.getElementById("checkoutItems");

    const totalElement =
        document.getElementById("checkoutTotal");

    container.innerHTML = "";

    let total = 0;

    cart.forEach(item => {
        const subtotal =
            item.price * item.quantity;

        total += subtotal;

        const element =
            document.createElement("div");

        element.className = "checkout-item";

        element.innerHTML = `
            <div class="checkout-item-image">
                <img
                    src="${item.image || ''}"
                    alt="${item.name}"
                    onerror="this.style.display='none';">
            </div>

            <div class="checkout-item-info">
                <h3>${item.name}</h3>

                <span>
                    ${item.quantity} × ${formatRupiah(item.price)}
                </span>
            </div>

            <div class="checkout-item-price">
                <strong>
                    ${formatRupiah(subtotal)}
                </strong>
            </div>
        `;

        container.appendChild(element);
    });

    totalElement.textContent =
        formatRupiah(total);
}

displayCheckoutItems();


// =====================================================
// FORM CHECKOUT
// =====================================================

document
    .getElementById("checkoutForm")
    .addEventListener("submit", function(event) {

        event.preventDefault();

        // Ambil data pelanggan

        const nama =
            document.getElementById("nama").value.trim();

        const whatsapp =
            document.getElementById("whatsapp").value.trim();

        const tanggalPemesanan =
            document.getElementById("tanggalPemesanan").value;

        const tanggalDibutuhkan =
            document.getElementById("tanggalDibutuhkan").value;

        const waktuDibutuhkan =
            document.getElementById("waktuDibutuhkan").value;

        const alamat =
            document.getElementById("alamat").value.trim();

        const catatan =
            document.getElementById("catatan").value.trim();


        // Validasi

        if (
            !nama ||
            !whatsapp ||
            !tanggalDibutuhkan ||
            !waktuDibutuhkan ||
            !alamat
        ) {
            alert(
                "Silakan lengkapi semua data yang wajib diisi."
            );

            return;
        }


        // Simpan data checkout sementara

        const checkoutData = {
            nama,
            whatsapp,
            tanggalPemesanan,
            tanggalDibutuhkan,
            waktuDibutuhkan,
            alamat,
            catatan
        };

        localStorage.setItem(
            "checkoutData",
            JSON.stringify(checkoutData)
        );


        // Lanjut ke pembayaran

        window.location.href = "payment.php";
    });
