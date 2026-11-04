import { app, db } from "../../../assets/js/firebase-config.js";

import {
    getAuth,
    onAuthStateChanged,
    signOut
} from "firebase/auth";

import {
    collection,
    getDocs,
    query,
    orderBy
} from "firebase/firestore";


const auth = getAuth(app);


// =========================
// ELEMENT
// =========================

const adminEmail =
    document.getElementById("adminEmail");

const totalOrders =
    document.getElementById("totalOrders");

const pendingOrders =
    document.getElementById("pendingOrders");

const completedOrders =
    document.getElementById("completedOrders");

const totalRevenue =
    document.getElementById("totalRevenue");

const ordersTableBody =
    document.getElementById("ordersTableBody");

const logoutButton =
    document.getElementById("logoutButton");


// =========================
// CEK LOGIN
// =========================

onAuthStateChanged(auth, async (user) => {

    if (!user) {

        window.location.href =
            "login.php";

        return;
    }

    // tampilkan email admin
    adminEmail.textContent =
        user.email || "Admin";

    // ambil pesanan
    await loadOrders();

});


// =========================
// LOAD ORDERS
// =========================

async function loadOrders() {

    try {

        const ordersRef =
            collection(db, "orders");

        const ordersQuery =
            query(
                ordersRef,
                orderBy(
                    "createdAt",
                    "desc"
                )
            );

        const snapshot =
            await getDocs(ordersQuery);

        let orders = [];

        snapshot.forEach((doc) => {

            orders.push({
                id: doc.id,
                ...doc.data()
            });

        });


        // =========================
        // STATISTICS
        // =========================

        totalOrders.textContent =
            orders.length;


        const pendingCount =
            orders.filter(order =>
                order.orderStatus ===
                "Pesanan Diterima" ||
                order.orderStatus ===
                "Sedang Diproses"
            ).length;

        pendingOrders.textContent =
            pendingCount;


        const completedCount =
            orders.filter(order =>
                order.orderStatus ===
                "Selesai"
            ).length;

        completedOrders.textContent =
            completedCount;


        const revenue =
            orders.reduce(
                (total, order) => {

                    if (
                        order.paymentStatus ===
                        "Dibayar"
                    ) {
                        return total +
                            Number(
                                order.totalHarga || 0
                            );
                    }

                    return total;

                },
                0
            );


        totalRevenue.textContent =
            formatRupiah(revenue);


        // =========================
        // TABLE
        // =========================

        displayOrders(
            orders.slice(0, 10)
        );


    } catch (error) {

        console.error(
            "Gagal mengambil pesanan:",
            error
        );

        ordersTableBody.innerHTML = `
            <tr>
                <td
                    colspan="6"
                    class="empty-state">

                    Gagal memuat data pesanan.

                </td>
            </tr>
        `;

    }

}


// =========================
// DISPLAY ORDERS
// =========================

function displayOrders(orders) {

    if (orders.length === 0) {

        ordersTableBody.innerHTML = `
            <tr>
                <td
                    colspan="6"
                    class="empty-state">

                    Belum ada pesanan.

                </td>
            </tr>
        `;

        return;
    }


    ordersTableBody.innerHTML =
        orders.map(order => {

            const nama =
                order.customer?.nama ||
                "-";

            const total =
                formatRupiah(
                    order.totalHarga || 0
                );

            const payment =
                order.paymentStatus ||
                "Belum Dibayar";

            const status =
                order.orderStatus ||
                "Pesanan Diterima";


            return `
                <tr>

                    <td>
                        <span class="order-id">
                            ${escapeHtml(
                                order.orderId || "-"
                            )}
                        </span>
                    </td>

                    <td>
                        <span class="customer-name">
                            ${escapeHtml(nama)}
                        </span>
                    </td>

                    <td>
                        <span class="order-price">
                            ${total}
                        </span>
                    </td>

                    <td>
                        ${paymentBadge(payment)}
                    </td>

                    <td>
                        ${statusBadge(status)}
                    </td>

                    <td>

                        <a
                            href="#"
                            class="btn-detail"
                            title="Lihat Detail">

                            <i class="bi bi-eye"></i>

                        </a>

                    </td>

                </tr>
            `;

        }).join("");

}


// =========================
// PAYMENT BADGE
// =========================

function paymentBadge(status) {

    let className =
        "payment-pending";

    if (status === "Dibayar") {
        className =
            "payment-paid";
    }

    if (
        status === "Gagal" ||
        status === "Kadaluarsa"
    ) {
        className =
            "payment-failed";
    }

    return `
        <span class="${className}">
            ${escapeHtml(status)}
        </span>
    `;
}


// =========================
// ORDER STATUS BADGE
// =========================

function statusBadge(status) {

    let className =
        "status-received";


    if (status === "Sedang Diproses") {

        className =
            "status-processing";

    } else if (status === "Dalam Pengantaran") {

        className =
            "status-delivery";

    } else if (status === "Selesai") {

        className =
            "status-completed";

    } else if (
        status === "Dibatalkan"
    ) {

        className =
            "status-cancelled";
    }


    return `
        <span class="status-badge ${className}">
            ${escapeHtml(status)}
        </span>
    `;
}


// =========================
// FORMAT RUPIAH
// =========================

function formatRupiah(number) {

    return new Intl.NumberFormat(
        "id-ID",
        {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }
    ).format(number);

}


// =========================
// ESCAPE HTML
// =========================

function escapeHtml(value) {

    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");

}


// =========================
// LOGOUT
// =========================

logoutButton.addEventListener(
    "click",
    async () => {

        try {

            await signOut(auth);

            window.location.href =
                "login.php";

        } catch (error) {

            console.error(
                "Logout gagal:",
                error
            );

        }

    }
);