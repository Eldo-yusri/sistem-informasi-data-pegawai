<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman
if ($_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

// Cek apakah tombol 'simpan' telah ditekan
if (isset($_POST['simpan'])) {
    // Ambil data dari form (tanpa sanitasi karena pakai prepared statements)
    $nip_lama = $_POST['nip_lama'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $golongan = $_POST['golongan'];
    $jabatan = $_POST['jabatan'];
    $unit_kerja = $_POST['unit_kerja'];
    $tmt_kerja = $_POST['tmt_kerja'];
    $nip_bps = $_POST['nip_bps'];
    $kode_org = $_POST['kode_org'];
    $wilayah = $_POST['wilayah'];
    $tmt_gol = $_POST['tmt_gol'];
    $status_pend = $_POST['status_pend'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $agama = $_POST['agama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Query untuk UPDATE data pegawai
    $query = "UPDATE pegawai SET 
                nip = ?,
                nip_bps = ?,
                nama = ?,
                kode_org = ?,
                wilayah = ?,
                tgl_lahir = ?,
                jenis_kelamin = ?,
                golongan = ?,
                tmt_gol = ?,
                status_pend = ?,
                tempat_lahir = ?,
                agama = ?,
                jabatan = ?,
                unit_kerja = ?,
                tmt_kerja = ? 
              WHERE nip = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param(
        $stmt, 
        "ssssssssssssssss", 
        $nip, $nip_bps, $nama, $kode_org, $wilayah, 
        $tgl_lahir, $jenis_kelamin, $golongan, $tmt_gol, 
        $status_pend, $tempat_lahir, $agama, $jabatan, 
        $unit_kerja, $tmt_kerja, $nip_lama
    );

    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) {
        // Catat aktivitas ke log
        catat_log($koneksi, $_SESSION['username'], "Mengedit data pegawai dengan NIP: " . $nip_lama);

        // Jika berhasil, alihkan kembali ke halaman data_pegawai.php
        header("location:data_pegawai.php?pesan=edit_sukses");
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal menyimpan perubahan: " . mysqli_stmt_error($stmt);
    }
} else {
    header("location:data_pegawai.php");
}
?>