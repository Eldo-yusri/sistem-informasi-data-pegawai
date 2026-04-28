<?php
$page_title = "Ganti Password";
include 'template_header.php';

$pesan = '';
$pesan_tipe = ''; // 'sukses' atau 'error'

if (isset($_POST['simpan_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $username = $_SESSION['username'];

    // 1. Ambil hash password saat ini dari database
    $stmt = mysqli_prepare($koneksi, "SELECT password FROM pengguna WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $hash_password_db = $user['password'];

    // 2. Verifikasi password lama
    // Catatan: password_verify() bisa memverifikasi hash MD5 jika hash-nya tidak valid untuk algoritma modern
    // Namun, untuk keamanan, kita akan memverifikasi dengan password_verify()
    if (password_verify($password_lama, $hash_password_db)) {
        // 3. Cek apakah password baru dan konfirmasi cocok
        if ($password_baru === $konfirmasi_password) {
            // 4. Hash password baru dengan standar keamanan modern
            $hash_password_baru = password_hash($password_baru, PASSWORD_DEFAULT);

            // 5. Update password di database
            $update_stmt = mysqli_prepare($koneksi, "UPDATE pengguna SET password = ? WHERE username = ?");
            mysqli_stmt_bind_param($update_stmt, "ss", $hash_password_baru, $username);
            if (mysqli_stmt_execute($update_stmt)) {
                $pesan = "Password berhasil diperbarui!";
                $pesan_tipe = 'sukses';
            } else {
                $pesan = "Terjadi kesalahan saat memperbarui password.";
                $pesan_tipe = 'error';
            }
        } else {
            $pesan = "Password baru dan konfirmasi tidak cocok.";
            $pesan_tipe = 'error';
        }
    } else {
        $pesan = "Password lama yang Anda masukkan salah.";
        $pesan_tipe = 'error';
    }
}
?>

<div class="page-header">
    <h4 class="font-weight-bold mb-0">Ganti Password</h4>
    <p class="text-muted mb-0">Ubah password Anda secara berkala untuk menjaga keamanan akun.</p>
</div>

<?php if (!empty($pesan)): ?>
    <div class="alert <?php echo ($pesan_tipe == 'sukses') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
        <?php echo $pesan; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="ganti_password.php" method="POST">
            <div class="form-group">
                <label for="password_lama">Password Lama</label>
                <input type="password" class="form-control" id="password_lama" name="password_lama" required>
            </div>
            <div class="form-group">
                <label for="password_baru">Password Baru</label>
                <input type="password" class="form-control" id="password_baru" name="password_baru" required>
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
            </div>
            <button type="submit" name="simpan_password" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php include 'template_footer.php'; ?>