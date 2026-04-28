<?php
$page_title = "Edit Pengguna";
include 'template_header.php';

// Proteksi Halaman
if ($_SESSION['level'] != 'admin') {
    header("location:index.php?pesan=akses_ditolak");
    exit;
}

if (!isset($_GET['id'])) {
    header("location:manajemen_pengguna.php");
    exit;
}

$id = $_GET['id'];
$pesan_error = '';

// Ambil data pengguna saat ini
$stmt = mysqli_prepare($koneksi, "SELECT username, email, level FROM pengguna WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("location:manajemen_pengguna.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $username_baru = $_POST['username'];
    $email_baru = $_POST['email'];
    $level_baru = $_POST['level'];
    $password_baru = $_POST['password'];

    // Bangun query update
    if (!empty($password_baru)) {
        // Jika password diisi, update password juga
        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
        $update_stmt = mysqli_prepare($koneksi, "UPDATE pengguna SET username = ?, email = ?, level = ?, password = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "ssssi", $username_baru, $email_baru, $level_baru, $hashed_password, $id);
    } else {
        // Jika password kosong, hanya update username dan level
        $update_stmt = mysqli_prepare($koneksi, "UPDATE pengguna SET username = ?, email = ?, level = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "sssi", $username_baru, $email_baru, $level_baru, $id);
    }

    if (mysqli_stmt_execute($update_stmt)) {
        catat_log($koneksi, $_SESSION['username'], "Mengedit pengguna: " . $username_baru);
        header("location:manajemen_pengguna.php?pesan=edit_sukses");
        exit;
    } else {
        $pesan_error = "Gagal memperbarui data pengguna.";
    }
}
?>

<h1 class="mt-4">Formulir Edit Pengguna</h1>
<p>Ubah data pengguna di bawah ini.</p>

<?php if (!empty($pesan_error)): ?>
    <div class="alert alert-danger" role="alert"><?php echo $pesan_error; ?></div>
<?php endif; ?>

<form action="edit_pengguna.php?id=<?php echo $id; ?>" method="POST">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password Baru (Opsional)</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
    </div>
    <div class="form-group">
        <label for="level">Level</label>
        <select class="form-control" id="level" name="level">
            <option value="operator" <?php echo ($data['level'] == 'operator') ? 'selected' : ''; ?>>Operator</option>
            <option value="admin" <?php echo ($data['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
    </div>
    <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
    <a href="manajemen_pengguna.php" class="btn btn-secondary">Batal</a>
</form>

<?php include 'template_footer.php'; ?>