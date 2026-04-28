<?php
session_start();
include_once 'koneksi.php';

// Proteksi Halaman: Cek apakah pengguna sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

// Logika logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("location:login.php?pesan=logout");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'SIPEG BPS TB'; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* --- GLOBAL & FONT STYLES --- */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            font-size: 0.95rem; /* Sedikit menyesuaikan ukuran font dasar */
            line-height: 1.6; /* Meningkatkan jarak antar baris untuk kenyamanan membaca */
        }

        #wrapper { display: block; transition: all 0.3s; overflow-x: hidden; }

        /* --- SIDEBAR STYLES --- */
        #sidebar-wrapper {
            min-width: 250px;
            max-width: 250px;
            background-color: #ffffff;
            height: 100vh;
            position: fixed; /* Membuat sidebar tetap di tempat saat scroll */
            top: 0;
            left: 0;
            z-index: 1000;
            border-right: 1px solid #e9ecef;            
            
            transition: all 0.3s ease;
        }

        .sidebar-heading {
            font-weight: 700;
            padding: 1.5rem 1.25rem;
            font-size: 1.25rem;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            color: #e86100;
        }

        .list-group-item {
            border: none;
            padding: 0.9rem 1.5rem;
            color: #495057;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
        }
        .list-group-item i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .list-group-item.active {
            background-color: #ffe8d1;
            color: #e86100;
            font-weight: 600;
            border-left: 4px solid #e86100;
        }
        .list-group-item.active i {
            color: #e86100;
        }

        .list-group-item-action:not(.active):hover {
            background-color: #f1f3f5;
            color: #e86100;
            transform: translateX(2px);
        }
        .list-group-item-action:not(.active):hover i {
            color: #e86100;
        }

        /* --- PAGE CONTENT STYLES --- */
        #page-content-wrapper {
            width: auto;
            padding: 1.5rem;
            margin-left: 250px; /* Memberi ruang untuk sidebar */
        }

        /* --- NAVBAR ATAS --- */
        .navbar {
            background-color: #ffffff;
            border-radius: 0.5rem;
            
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 0.8rem 1.5rem;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: #343a40 !important;
        }

        .navbar .nav-link {
            color: #495057 !important;
            
        }

        .dropdown-menu {
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 0.5rem;
        }
        .navbar .nav-link:hover {
            color: #e86100 !important;
        }
        canvas {
            max-width: 100%;
        }


        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #e86100;
        }

        /* --- CUSTOM ELEMENTS --- */
        .alert-custom-success {
            background-color: #e6f9f0;
            color: #1b8755;
            border-color: #d1f3e4;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease-in-out;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }

        .form-control:focus {
            border-color: #fd7e14;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
        }
        .page-header {
            background-color: #ffffff;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* --- TABLE STYLES --- */
        .table-custom th,
        .table-custom td {
            white-space: nowrap; /* Mencegah teks header dan isi pindah baris */
        }

        /* --- ANIMATIONS --- */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #page-content-wrapper {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert {
            animation: slideDown 0.4s ease-out;
        }

    </style>
    <style>
        .toggled #sidebar-wrapper {
            margin-left: -250px; /* Menyembunyikan sidebar */
        }
        .toggled #page-content-wrapper {
            margin-left: 0; /* Konten memenuhi layar */
        }
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -250px; }
            #page-content-wrapper { margin-left: 0; }
            .toggled #sidebar-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>
  
  <?php
        // Logika untuk menentukan halaman aktif
        $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">Menu</div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
                <a href="data_pegawai.php" class="list-group-item list-group-item-action <?php echo in_array($current_page, ['data_pegawai.php', 'tambah_pegawai.php', 'edit_pegawai.php']) ? 'active' : ''; ?>"><i class="bi bi-people-fill"></i> Data Pegawai</a>
                <a href="laporan.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'laporan.php') ? 'active' : ''; ?>"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a>
                <a href="grafik.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'grafik.php') ? 'active' : ''; ?>"><i class="bi bi-bar-chart-line-fill"></i> Grafik</a>
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <a href="manajemen_pengguna.php" class="list-group-item list-group-item-action <?php echo in_array($current_page, ['manajemen_pengguna.php', 'tambah_pengguna.php', 'edit_pengguna.php']) ? 'active' : ''; ?>"><i class="bi bi-person-gear"></i> Manajemen Pengguna</a>
                    <a href="log_aktivitas.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'log_aktivitas.php') ? 'active' : ''; ?>"><i class="bi bi-clock-history"></i> Log Aktivitas</a>
                <?php endif; ?>
                <a href="?action=logout" class="list-group-item list-group-item-action text-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light mb-4 align-items-center">
                <button class="btn btn-warning mr-3" id="menu-toggle">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand mb-0 h1"><?php echo $page_title ?? 'Dashboard'; ?></span>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-person-circle mr-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="ganti_password.php">Ganti Password</a>
                                <a class="dropdown-item" href="?action=logout">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid">
    <script>
    $(document).ready(function() {
        // Cek status sidebar dari localStorage saat halaman dimuat
        if (localStorage.getItem('sidebarState') === 'toggled') {
            $('#wrapper').addClass('toggled');
        }

        $('#menu-toggle').click(function(e) {
            e.preventDefault();
            $('#wrapper').toggleClass('toggled');

            // Simpan status sidebar ke localStorage
            if ($('#wrapper').hasClass('toggled')) {
                localStorage.setItem('sidebarState', 'toggled');
            } else {
                localStorage.removeItem('sidebarState');
            }
        });
    });
    </script>