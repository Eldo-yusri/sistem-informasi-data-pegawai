<?php
$page_title = "Lupa Password";
include 'koneksi.php';

$pesan = '';
$pesan_tipe = ''; // 'sukses' atau 'error'

if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);

    // 1. Cek apakah email terdaftar di tabel pengguna
    $stmt_cek = mysqli_prepare($koneksi, "SELECT username FROM pengguna WHERE email = ?");
    mysqli_stmt_bind_param($stmt_cek, "s", $email);
    mysqli_stmt_execute($stmt_cek);
    $result = mysqli_stmt_get_result($stmt_cek);

    if (mysqli_num_rows($result) > 0) {
        // 2. Buat token unik yang aman
        $token = bin2hex(random_bytes(50));

        // 3. Simpan token ke database
        $stmt_simpan = mysqli_prepare($koneksi, "INSERT INTO password_resets (email, token) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt_simpan, "ss", $email, $token);
        mysqli_stmt_execute($stmt_simpan);

        // 4. Kirim email ke pengguna
        // **PENTING**: Ini memerlukan konfigurasi SMTP di php.ini dan sendmail.ini pada XAMPP Anda.
        // Tanpa konfigurasi, email TIDAK akan terkirim.
        $subjek = "Reset Password Akun SIPEG BPS TB";
        
        // Dapatkan host saat ini (misal: localhost)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['PHP_SELF']);
        $base_url = rtrim($protocol . $host . $path, '/\\');

        $link_reset = $base_url . "/reset_password.php?token=" . $token;

        $isi_email = "
        Halo,\n
        Kami menerima permintaan untuk mereset password akun Anda.\n
        Silakan klik link di bawah ini untuk melanjutkan:\n
        $link_reset\n\n
        Jika Anda tidak merasa meminta reset password, abaikan email ini.\n
        Link ini akan kedaluwarsa dalam 1 jam.\n\n
        Terima kasih,\n
        Administrator SIPEG BPS TB
        ";
        $headers = "From: no-reply@bps-tanjungbalai.go.id";

        // Menggunakan fungsi mail() bawaan PHP
        if (mail($email, $subjek, $isi_email, $headers)) {
            $pesan = "Link reset password telah dikirim ke email Anda.";
            $pesan_tipe = 'sukses';
        } else {
            $pesan = "Gagal mengirim email. Silakan hubungi administrator. Mungkin ada masalah konfigurasi server email.";
            $pesan_tipe = 'error';
        }
    }
    // Selalu tampilkan pesan sukses untuk mencegah user enumeration (mengetahui email mana yang terdaftar)
    $pesan = "Jika email Anda terdaftar dalam sistem, sebuah link untuk mereset password akan dikirimkan.";
    $pesan_tipe = 'sukses';
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
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body { 
            background-image: url('assets/background-login.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
            animation: slideUp 0.6s ease-out forwards;
            overflow: hidden;
        }
        .card-header {
            background-color: transparent;
            color: #333;
            border-bottom: none;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .card-header .logo-container {
            margin-bottom: 1rem;
        }
        .card-header .logo-container img {
            width: 80px;
            height: auto;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));
        }
        .card-header h4 {
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #0d47a1;
        }
        .card-body {
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <div class="logo-container">
                            <img src="assets/bps.png" alt="Logo BPS">
                        </div>
                        <h4>Lupa Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pesan)): ?>
                            <div class="alert <?php echo ($pesan_tipe == 'sukses') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                <?php echo $pesan; ?>
                            </div>
                        <?php endif; ?>

                        <form action="lupa_password.php" method="POST" class="text-left">
                            <p class="text-muted text-center mb-4">Masukkan alamat email yang terhubung dengan akun Anda. Kami akan mengirimkan link untuk mengatur ulang password Anda.</p>
                            <div class="form-group">
                                <label for="email">Alamat Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" name="reset_password" class="btn btn-primary btn-block">Kirim Link Reset</button>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <a href="login.php">Kembali ke Halaman Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>