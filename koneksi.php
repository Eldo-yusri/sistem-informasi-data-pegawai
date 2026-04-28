<?php
// Konfigurasi Database
$server = "localhost"; // Biasanya localhost
$username = "root";    // Default username XAMPP
$password = "";        // Default password XAMPP (kosong)
$database = "bps_kota_tanjungbalai"; // Ganti dengan nama database Anda

// Membuat Koneksi
$koneksi = mysqli_connect($server, $username, $password, $database);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

/**
 * Fungsi untuk mencatat aktivitas pengguna ke dalam tabel logs.
 * @param mysqli $koneksi - Objek koneksi database.
 * @param string $username - Username dari session.
 * @param string $aksi - Deskripsi aksi yang dilakukan.
 */
function catat_log($koneksi, $username, $aksi) {
    $query = "INSERT INTO logs (username, aksi) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $aksi);
    mysqli_stmt_execute($stmt);
}
?>
