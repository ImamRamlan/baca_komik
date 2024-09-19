<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">

        </div>
        <div class="sidebar-brand-text mx-3">Baca Komik</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="halaman.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        MAIN FEATURES
    </div>

    <!-- Menu items for Admin -->
    <?php if ($_SESSION["hak_akses"] == 'Admin' || $_SESSION["hak_akses"] == 'Editor') : ?>
        <li class="nav-item <?php echo ($current_page == 'data_komik.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="data_komik.php">
                <i class="nav-icon fas fa-book"></i>
                <span>Data Komik</span></a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'data_halaman.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="data_halaman.php">
                <i class="nav-icon fas fa-pen"></i>
                <span>Data Halaman</span></a>
        </li>
    <?php endif; ?>

    <?php if ($_SESSION["hak_akses"] == 'Admin') : ?>
        <li class="nav-item <?php echo ($current_page == 'data_admin.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="data_admin.php">
                <i class="nav-icon fas fa-tags"></i>
                <span>Data Admin - Editor</span></a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'data_pengguna.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="data_pengguna.php">
                <i class="nav-icon fas fa-tags"></i>
                <span>Data Pengguna</span></a>
        </li>
    <?php endif; ?>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        MORE FEATURES
    </div>
    <li class="nav-item <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
        <a href="logout.php" class="nav-link" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            Keluar
        </a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>