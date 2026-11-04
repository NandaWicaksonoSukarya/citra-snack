
document.addEventListener("DOMContentLoaded", function () {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    if (!sidebarToggle) {
        console.error("Tombol hamburger tidak ditemukan!");
        return;
    }

    function isMobile() {
        return window.innerWidth <= 768;
    }

    sidebarToggle.addEventListener("click", function () {
        if (isMobile()) {
            document.body.classList.toggle("mobile-sidebar-open");
        } else {
            document.body.classList.toggle("sidebar-collapsed");
        }
    });

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", function () {
            document.body.classList.remove("mobile-sidebar-open");
        });
    }

    document.querySelectorAll(".sidebar-nav .sidebar-link").forEach(function (link) {
        link.addEventListener("click", function () {
            if (isMobile()) {
                document.body.classList.remove("mobile-sidebar-open");
            }
        });
    });

    window.addEventListener("resize", function () {
        document.body.classList.remove(
            "sidebar-collapsed",
            "mobile-sidebar-open"
        );
    });
});
