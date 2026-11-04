
<?php
$currentPage = basename($_SERVER["PHP_SELF"]);
?>

<button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="Buka atau tutup menu">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        Citra<span>Snack</span>
    </div>

    <div class="admin-label">ADMIN PANEL</div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>

        <a href="pesanan.php" class="sidebar-link <?= $currentPage === 'pesanan.php' ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i>
            <span>Pesanan</span>
        </a>

        <a href="produk.php" class="sidebar-link <?= $currentPage === 'produk.php' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i>
            <span>Produk</span>
        </a>

        <a href="riwayat.php" class="sidebar-link <?= $currentPage === 'riwayat.php' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="../index.php" class="sidebar-link">
            <i class="bi bi-globe"></i>
            <span>Lihat Website</span>
        </a>

        <button type="button" id="logoutButton" class="sidebar-link logout-button">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </button>
    </div>
</aside>
