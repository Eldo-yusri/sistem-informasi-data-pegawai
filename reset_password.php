<?php
$page_title = "Reset Password";
include 'koneksi.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
$pesan = '';
$pesan_tipe = ''; // 'sukses' atau 'error'
$token_valid = false;

if (empty($token)) {
    $pesan = "Token tidak valid atau tidak ditemukan.";
    $pesan_tipe = 'error';
} else {
    // 1. Cek apakah token ada di database dan belum kedaluwarsa (misal: 1 jam)
    $stmt_cek = mysqli_prepare($koneksi, "SELECT email FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
    mysqli_stmt_bind_param($stmt_cek, "s", $token);
    mysqli_stmt_execute($stmt_cek);
    $result = mysqli_stmt_get_result($stmt_cek);

    if (mysqli_num_rows($result) > 0) {
        $token_valid = true;
        $data = mysqli_fetch_assoc($result);
        $email = $data['email'];
    } else {
        $pesan = "Token tidak valid atau sudah kedaluwarsa.";
        $pesan_tipe = 'error';
    }
}

// Proses form jika password baru disubmit
if (isset($_POST['simpan_password_baru']) && $token_valid) {
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($password_baru === $konfirmasi_password) {
        // Hash password baru
        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

        // Update password di tabel pengguna
        $stmt_update = mysqli_prepare($koneksi, "UPDATE pengguna SET password = ? WHERE email = ?");
        mysqli_stmt_bind_param($stmt_update, "ss", $hashed_password, $email);
        
        if (mysqli_stmt_execute($stmt_update)) {
            // Hapus token yang sudah digunakan dari database
            $stmt_hapus = mysqli_prepare($koneksi, "DELETE FROM password_resets WHERE email = ?");
            mysqli_stmt_bind_param($stmt_hapus, "s", $email);
            mysqli_stmt_execute($stmt_hapus);

            $pesan = "Password Anda telah berhasil direset. Silakan login dengan password baru Anda.";
            $pesan_tipe = 'sukses';
            $token_valid = false; // Sembunyikan form setelah berhasil
        } else {
            $pesan = "Gagal memperbarui password.";
            $pesan_tipe = 'error';
        }
    } else {
        $pesan = "Password baru dan konfirmasi tidak cocok.";
        $pesan_tipe = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - SIPEG BPS TB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { 
            background-image: url('assets/background-login.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center bg-transparent border-0 pt-4">
                        <h4>Atur Ulang Password</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($pesan)): ?>
                            <div class="alert <?php echo ($pesan_tipe == 'sukses') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                <?php echo $pesan; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($token_valid): ?>
                            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                                <div class="form-group">
                                    <label for="password_baru">Password Baru:</label>
                                    <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                                </div>
                                <div class="form-group">
                                    <label for="konfirmasi_password">Konfirmasi Password Baru:</label>
                                    <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                                </div>
                                <button type="submit" name="simpan_password_baru" class="btn btn-primary btn-block">Simpan Password Baru</button>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary mt-2">Kembali ke Halaman Login</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>