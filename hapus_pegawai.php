<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman: Pastikan hanya user yang sudah login yang bisa mengakses
if ($_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

// Proteksi berdasarkan Level: Hanya admin yang boleh menghapus
if ($_SESSION['level'] != 'admin') {
    // Alihkan ke halaman data pegawai dengan pesan error jika bukan admin
    header("location:data_pegawai.php?pesan=akses_ditolak");
    exit;
}

// Cek apakah parameter NIP ada di URL
if (isset($_GET['nip'])) {
    $nip = mysqli_real_escape_string($koneksi, $_GET['nip']);

    // Buat query untuk menghapus data menggunakan prepared statement
    $query = "DELETE FROM pegawai WHERE nip = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $nip);

    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) {
        // Catat aktivitas ke log
        catat_log($koneksi, $_SESSION['username'], "Menghapus pegawai dengan NIP: " . $nip);

        // Jika berhasil, redirect kembali ke halaman data pegawai dengan pesan sukses
        header("location:data_pegawai.php?pesan=hapus_sukses");
    } else {
        // Jika gagal, redirect dengan pesan error
        header("location:data_pegawai.php?pesan=hapus_gagal");
    }
} else {
    // Jika tidak ada NIP, kembalikan ke halaman data pegawai
    header("location:data_pegawai.php");
}
?>