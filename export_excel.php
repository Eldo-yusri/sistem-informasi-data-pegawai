<?php
// Sertakan file koneksi
include 'koneksi.php';

// Nama file yang akan di-download
$filename = "laporan_data_pegawai_" . date('Ymd') . ".csv";

// Set header untuk memberitahu browser bahwa ini adalah file CSV yang akan di-download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Buka output stream PHP
$output = fopen('php://output', 'w');

// Tulis baris header ke file CSV
fputcsv($output, [
    'NIP BPS', 'NIP', 'Nama', 'Tempat Lahir', 'Tgl Lahir', 
    'Jenis Kelamin', 'Agama', 'Gol Akhir', 'TMT Gol', 'Jabatan', 
    'Unit Kerja', 'TMT Kerja', 'Status.pend(SK)', 'Kode.org', 'Wilayah'
]);

// Query untuk mengambil semua data pegawai
$query = "SELECT nip_bps, nip, nama, tempat_lahir, tgl_lahir, jenis_kelamin, agama, golongan, tmt_gol, jabatan, unit_kerja, tmt_kerja, status_pend, kode_org, wilayah FROM pegawai";

// Ambil nilai filter dari URL
$filter_unit = isset($_GET['filter_unit']) ? $_GET['filter_unit'] : '';
$filter_golongan = isset($_GET['filter_golongan']) ? $_GET['filter_golongan'] : '';
$filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';

$conditions = [];
if (!empty($filter_unit)) {
    $conditions[] = "unit_kerja = '" . mysqli_real_escape_string($koneksi, $filter_unit) . "'";
}
if (!empty($filter_golongan)) {
    $conditions[] = "golongan = '" . mysqli_real_escape_string($koneksi, $filter_golongan) . "'";
}
if (!empty($filter_gender)) {
    $conditions[] = "jenis_kelamin = '" . mysqli_real_escape_string($koneksi, $filter_gender) . "'";
}
if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}
$query .= " ORDER BY nama ASC";
$result = mysqli_query($koneksi, $query);

// Loop melalui setiap baris data dari database
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Tulis setiap baris data ke file CSV
        fputcsv($output, [
            $row['nip_bps'], $row['nip'], $row['nama'], $row['tempat_lahir'], $row['tgl_lahir'],
            $row['jenis_kelamin'], $row['agama'], $row['golongan'], $row['tmt_gol'], $row['jabatan'],
            $row['unit_kerja'], $row['tmt_kerja'], $row['status_pend'], $row['kode_org'], $row['wilayah']
        ]);
    }
}

// Tutup output stream
fclose($output);
exit;
?>