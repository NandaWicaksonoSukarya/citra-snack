import { db } from "./firebase-config.js";

import {
    collection,
    query,
    where,
    getDocs
} from "firebase/firestore";


// ===============================
// ELEMENT
// ===============================

const trackingForm =
    document.getElementById("trackingForm");

const orderIdInput =
    document.getElementById("orderId");

const whatsappInput =
    document.getElementById("whatsapp");

const trackingError =
    document.getElementById("trackingError");


// ===============================
// FORM SUBMIT
// ===============================

trackingForm.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();

        // Hilangkan error sebelumnya
        trackingError.style.display = "none";


        // ===============================
        // AMBIL INPUT
        // ===============================

        const orderId =
            orderIdInput.value.trim();

        const whatsapp =
            whatsappInput.value.trim();


        if (!orderId || !whatsapp) {

            trackingError.querySelector("span").textContent =
                "Nomor pesanan dan nomor WhatsApp wajib diisi.";

            trackingError.style.display = "flex";

            return;
        }


        // ===============================
        // TAMPILKAN LOADING
        // ===============================

        const button =
            trackingForm.querySelector(
                ".btn-track"
            );

        const originalButton =
            button.innerHTML;

        button.disabled = true;

        button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm"
                role="status">
            </span>

            Mencari Pesanan...
        `;


        try {

            // ===============================
            // CARI BERDASARKAN ORDER ID
            // ===============================

            const ordersRef =
                collection(db, "orders");

            const q =
                query(
                    ordersRef,
                    where(
                        "orderId",
                        "==",
                        orderId
                    )
                );


            const snapshot =
                await getDocs(q);


            // ===============================
            // PESANAN TIDAK DITEMUKAN
            // ===============================

            if (snapshot.empty) {

                showError(
                    "Nomor pesanan tidak ditemukan."
                );

                return;
            }


            // ===============================
            // CARI DATA YANG NOMOR WHATSAPP-NYA SESUAI
            // ===============================

            let foundOrder = null;

            snapshot.forEach(doc => {

                const order =
                    doc.data();

                const customerWhatsapp =
                    order.customer?.whatsapp || "";

                if (
                    normalizeWhatsapp(
                        customerWhatsapp
                    ) ===
                    normalizeWhatsapp(
                        whatsapp
                    )
                ) {

                    foundOrder = order;
                }

            });


            // ===============================
            // WHATSAPP TIDAK SESUAI
            // ===============================

            if (!foundOrder) {

                showError(
                    "Nomor pesanan atau nomor WhatsApp tidak sesuai."
                );

                return;
            }


            // ===============================
            // SIMPAN ORDER ID
            // ===============================

            localStorage.setItem(
                "orderId",
                foundOrder.orderId
            );


            // ===============================
            // BUKA STATUS PESANAN
            // ===============================

            window.location.href =
                "status-pesanan.html";


        } catch (error) {

            console.error(
                "Gagal mencari pesanan:",
                error
            );

            showError(
                "Terjadi kesalahan saat mencari pesanan."
            );

        } finally {

            button.disabled = false;

            button.innerHTML =
                originalButton;
        }

    }
);


// ===============================
// NORMALIZE WHATSAPP
// ===============================

function normalizeWhatsapp(number) {

    let value =
        number
            .replace(/\D/g, "");


    // 08xxxxxxxx → 628xxxxxxxx
    if (
        value.startsWith("08")
    ) {

        value =
            "62" +
            value.substring(1);
    }


    // +628xxxxxxxx → 628xxxxxxxx
    if (
        value.startsWith("0062")
    ) {

        value =
            value.substring(2);
    }


    return value;
}


// ===============================
// ERROR
// ===============================

function showError(message) {

    trackingError
        .querySelector("span")
        .textContent =
        message;

    trackingError.style.display =
        "flex";
}