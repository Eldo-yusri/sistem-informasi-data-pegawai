<?php
// 1. Mulai Session untuk menyimpan status login
session_start();

// 2. Sertakan file koneksi database
include 'koneksi.php';

$pesan_error = ''; // Variabel untuk menyimpan pesan error
$remembered_username = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : '';



// Cek apakah tombol 'login' sudah ditekan
if (isset($_POST['login'])) {
    
    // Ambil data dari form dan sanitasi (membersihkan input)
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Query untuk mencari pengguna berdasarkan username
    $query = "SELECT * FROM pengguna WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah username ditemukan
    if (mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);

        // Verifikasi password yang diinput dengan hash yang ada di database
        if (password_verify($password, $data['password'])) {
            // Jika password cocok, buat session
            $_SESSION['username'] = $data['username'];
            $_SESSION['level'] = $data['level'];
            $_SESSION['status'] = "login";

            // Logika untuk "Ingat Saya"
            if (!empty($_POST['ingat_saya'])) {
                // Set cookie selama 30 hari
                setcookie('remember_user', $username, time() + (86400 * 30), "/"); // 86400 = 1 hari
            } else {
                // Hapus cookie jika tidak dicentang
                setcookie('remember_user', '', time() - 3600, "/");
            }

            // Arahkan ke halaman Dashboard
            header("location:index.php");
            exit;
        }
    } else {
        // Siapkan pesan error untuk ditampilkan di dalam form
        $pesan_error = 'Username atau Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pegawai BPS</title>
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
            /* Menggunakan gambar sebagai latar belakang */
            background-image: url('assets/background-login.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh; /* Memastikan gradasi memenuhi seluruh layar */
            font-family: 'Poppins', sans-serif;
            display: flex; /* Menggunakan flexbox untuk menengahkan konten */
            align-items: center; /* Menengahkan secara vertikal */
            justify-content: center; /* Menengahkan secara horizontal */
        }
        .login-container { 
            /* margin-top tidak lagi diperlukan karena flexbox */
        }
        .card {
            /* Menggunakan latar belakang semi-transparan agar kontras dengan gambar */
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px; /* Membuat sudut lebih tumpul */
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out forwards;
            overflow: hidden; /* Memastikan border-radius diterapkan pada konten anak */
        }
        .card-header {
            background-color: transparent; /* Header transparan */
            color: #333; /* Warna teks lebih gelap untuk kontras */
            border-bottom: none; /* Menghapus garis bawah */
            padding: 2rem 1.5rem; /* Padding lebih besar */
            display: flex; /* Menggunakan flexbox untuk konten header */
            flex-direction: column; /* Konten disusun vertikal */
            align-items: center; /* Menengahkan konten secara horizontal */
            justify-content: center; /* Menengahkan konten secara vertikal */
        }
        .card-header .logo-container {
            margin-bottom: 1rem; /* Spasi antara logo dan teks */
        }
        .card-header .logo-container img {
            width: 80px; /* Ukuran logo */
            height: auto;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2)); /* Bayangan pada logo */
        }
        .card-header h4 {
            font-weight: 700; /* Font lebih tebal */
            margin-bottom: 0.25rem; /* Spasi bawah untuk judul */
            color: #0d47a1; /* Warna biru tua untuk judul */
        }
        .card-header p {
            font-weight: 400;
            color: #555;
            margin-bottom: 0;
        }
        .card-body {
            padding: 2rem; /* Padding lebih besar untuk body kartu */
        }
        .form-group {
            margin-bottom: 1.5rem; /* Spasi antar form group */
        }
        .form-control {
            transition: all 0.3s ease-in-out; /* Transisi lebih halus */
            border-radius: 8px; /* Sudut input lebih tumpul */
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            border-color: #fd7e14;
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.4); /* Bayangan fokus lebih kuat */
            transform: translateY(-1px); /* Efek sedikit terangkat saat fokus */
        }
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
            font-weight: 600; /* Teks tombol lebih tebal */
            padding: 0.75rem 1.5rem; /* Padding tombol lebih besar */
            border-radius: 8px; /* Sudut tombol lebih tumpul */
            transition: all 0.3s ease-in-out;
        }
        .btn-orange:hover {
            transform: translateY(-3px); /* Efek sedikit terangkat saat hover lebih kuat */
            background-color: #e86100; /* Oranye lebih gelap saat hover */
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); /* Bayangan saat hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 login-container">
                <div class="card">
                    <div class="card-header text-center">
                        <div class="logo-container">
                            <!-- Ganti URL ini dengan path logo BPS Anda yang sebenarnya -->
                            <img src="assets/bps.png" alt="Logo BPS">
                        </div>
                        <h4>Data Pegawai BPS Kota Tanjungbalai</h4>
                        <p>Silakan login untuk melanjutkan</p>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($pesan_error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $pesan_error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST" novalidate>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($remembered_username); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ingat_saya" name="ingat_saya" <?php echo !empty($remembered_username) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="ingat_saya">Ingat Saya</label>
                                </div>
                                <div>
                                    <a href="lupa_password.php">Lupa Password?</a>
                                </div>
                            </div>
                            <button type="submit" name="login" class="btn btn-orange btn-block">LOGIN</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>