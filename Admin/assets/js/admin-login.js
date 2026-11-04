import { app } from "../../../assets/js/firebase-config.js";

import {
    getAuth,
    signInWithEmailAndPassword
} from "firebase/auth";

const auth = getAuth(app);

const loginForm =
    document.getElementById("loginForm");

const emailInput =
    document.getElementById("email");

const passwordInput =
    document.getElementById("password");

const loginButton =
    document.getElementById("loginButton");

const loginError =
    document.getElementById("loginError");

const togglePassword =
    document.getElementById("togglePassword");


// =========================
// TOGGLE PASSWORD
// =========================

togglePassword.addEventListener(
    "click",
    function () {

        const icon =
            this.querySelector("i");

        if (passwordInput.type === "password") {

            passwordInput.type = "text";

            icon.className =
                "bi bi-eye-slash";

        } else {

            passwordInput.type = "password";

            icon.className =
                "bi bi-eye";
        }
    }
);


// =========================
// LOGIN
// =========================

loginForm.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();

        loginError.style.display = "none";

        const email =
            emailInput.value.trim();

        const password =
            passwordInput.value;

        loginButton.disabled = true;

        loginButton.innerHTML =
            `<span>Memproses...</span>`;

        try {

            await signInWithEmailAndPassword(
                auth,
                email,
                password
            );

            // Login berhasil
            window.location.href =
                "dashboard.html";

        } catch (error) {

            console.error(
                "Login error:",
                error
            );

            let message =
                "Email atau password salah.";

            if (
                error.code ===
                "auth/invalid-email"
            ) {

                message =
                    "Format email tidak valid.";

            } else if (
                error.code ===
                "auth/invalid-credential"
            ) {

                message =
                    "Email atau password salah.";

            } else if (
                error.code ===
                "auth/user-disabled"
            ) {

                message =
                    "Akun admin dinonaktifkan.";

            } else if (
                error.code ===
                "auth/too-many-requests"
            ) {

                message =
                    "Terlalu banyak percobaan. Coba lagi nanti.";

            } else if (
                error.code ===
                "auth/network-request-failed"
            ) {

                message =
                    "Koneksi ke Firebase gagal.";

            } else if (
                error.code ===
                "auth/api-key-not-valid"
            ) {

                message =
                    "API key Firebase tidak valid.";
            }

            loginError.querySelector(
                "span"
            ).textContent = message;

            loginError.style.display =
                "flex";

            loginButton.disabled =
                false;

            loginButton.innerHTML =
                `<span>Masuk ke Dashboard</span>`;
        }
    }
);