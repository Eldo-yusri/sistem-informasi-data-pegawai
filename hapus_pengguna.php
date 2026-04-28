<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman
if ($_SESSION['status'] != "login" || $_SESSION['level'] != 'admin') {
    header("location:index.php?pesan=akses_ditolak");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil username untuk log sebelum dihapus
    $stmt_get = mysqli_prepare($koneksi, "SELECT username FROM pengguna WHERE id = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $id);
    mysqli_stmt_execute($stmt_get);
    $result = mysqli_stmt_get_result($stmt_get);
    $data = mysqli_fetch_assoc($result);
    $username_dihapus = $data['username'];

    // Pencegahan agar admin tidak bisa menghapus akunnya sendiri
    if ($_SESSION['username'] == $username_dihapus) {
        header("location:manajemen_pengguna.php?pesan=hapus_gagal_sendiri");
        exit;
    }

    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM pengguna WHERE id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    if (mysqli_stmt_execute($stmt_delete)) {
        catat_log($koneksi, $_SESSION['username'], "Menghapus pengguna: " . $username_dihapus);
        header("location:manajemen_pengguna.php?pesan=hapus_sukses");
    }
}
?>