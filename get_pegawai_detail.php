<?php
include 'koneksi.php';

header('Content-Type: application/json'); // Memberitahu browser bahwa respons adalah JSON

$response = ['status' => 'error', 'message' => ''];

if (isset($_GET['nip'])) {
    $nip = mysqli_real_escape_string($koneksi, $_GET['nip']);

    $query = "SELECT * FROM pegawai WHERE nip = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $nip);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if ($data) {
            $response = ['status' => 'success', 'data' => $data];
        } else {
            $response['message'] = 'Data pegawai tidak ditemukan.';
        }
    } else {
        $response['message'] = 'Gagal mempersiapkan query.';
    }
} else {
    $response['message'] = 'NIP tidak disediakan.';
}

echo json_encode($response);
?>