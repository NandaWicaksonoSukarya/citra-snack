
// =====================================================
// LOAD NAVBAR
// =====================================================

fetch("/citra-snack/components/navbar.html")
    .then(response => {
        if (!response.ok) {
            throw new Error("Navbar gagal dimuat");
        }

        return response.text();
    })
    .then(data => {
        const navbar = document.getElementById("navbar");

        if (!navbar) return;

        navbar.innerHTML = data;

        // Login admin dengan klik 5 kali
        const adminTrigger = document.getElementById("adminTrigger");

        let clickCount = 0;
        let clickTimer;

        if (adminTrigger) {
            adminTrigger.addEventListener("click", function (event) {
                event.preventDefault();

                clickCount++;

                clearTimeout(clickTimer);

                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 2000);

                if (clickCount >= 5) {
                    clickCount = 0;

                    window.location.href =
                        "/citra-snack/Admin/login.php";
                }
            });
        }
    })
    .catch(error => console.error(error));


// =====================================================
// LOAD FOOTER
// =====================================================

fetch("/citra-snack/components/footer.html")
    .then(response => {
        if (!response.ok) {
            throw new Error("Footer gagal dimuat");
        }

        return response.text();
    })
    .then(data => {
        const footer = document.getElementById("footer");

        if (!footer) return;

        footer.innerHTML = data;
    })
    .catch(error => console.error(error));
