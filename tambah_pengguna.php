<?php
$page_title = "Tambah Pengguna";
include 'template_header.php';

// Proteksi Halaman
if ($_SESSION['level'] != 'admin') {
    header("location:index.php?pesan=akses_ditolak");
    exit;
}

$pesan_error = '';
if (isset($_POST['simpan'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $level = $_POST['level'];

    // Cek apakah username sudah ada
    $cek_stmt = mysqli_prepare($koneksi, "SELECT id FROM pengguna WHERE username = ?");
    mysqli_stmt_bind_param($cek_stmt, "s", $username);
    mysqli_stmt_execute($cek_stmt);
    mysqli_stmt_store_result($cek_stmt);

    if (mysqli_stmt_num_rows($cek_stmt) > 0) {
        $pesan_error = "Username sudah digunakan. Silakan pilih username lain.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert ke database
        $insert_stmt = mysqli_prepare($koneksi, "INSERT INTO pengguna (username, password, email, level) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $hashed_password, $email, $level);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            catat_log($koneksi, $_SESSION['username'], "Menambahkan pengguna baru: " . $username);
            header("location:manajemen_pengguna.php?pesan=tambah_sukses");
            exit;
        } else {
            $pesan_error = "Gagal menyimpan data pengguna.";
        }
    }
}
?>

<h1 class="mt-4">Formulir Tambah Pengguna Baru</h1>
<p>Silakan isi data pengguna baru di bawah ini.</p>

<?php if (!empty($pesan_error)): ?>
    <div class="alert alert-danger" role="alert"><?php echo $pesan_error; ?></div>
<?php endif; ?>

<form action="tambah_pengguna.php" method="POST">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="level">Level</label>
        <select class="form-control" id="level" name="level">
            <option value="operator">Operator</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <button type="submit" name="simpan" class="btn btn-primary">Simpan Pengguna</button>
    <a href="manajemen_pengguna.php" class="btn btn-secondary">Batal</a>
</form>

<?php include 'template_footer.php'; ?>