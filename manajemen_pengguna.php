<?php
$page_title = "Manajemen Pengguna";
include 'template_header.php';

// Proteksi Halaman: Hanya admin yang boleh mengakses halaman ini
if ($_SESSION['level'] != 'admin') {
    header("location:index.php?pesan=akses_ditolak");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center page-header">
    <div>
        <h4 class="font-weight-bold mb-0">Manajemen Pengguna</h4>
        <p class="text-muted mb-0">Kelola akun pengguna sistem.</p>
    </div>
    <a href="tambah_pengguna.php" class="btn btn-primary font-weight-bold"><i class="bi bi-plus-circle-fill mr-2"></i>Tambah Pengguna</a>
</div>

<?php if(isset($_GET['pesan'])): ?>
    <div class="alert alert-custom-success" role="alert">
        <?php 
            if($_GET['pesan'] == 'tambah_sukses') echo 'Pengguna baru berhasil ditambahkan!';
            if($_GET['pesan'] == 'edit_sukses') echo 'Data pengguna berhasil diperbarui!';
            if($_GET['pesan'] == 'hapus_sukses') echo 'Pengguna berhasil dihapus!';
        ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-custom">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT id, username, level FROM pengguna ORDER BY username ASC";
                    $result = mysqli_query($koneksi, $query);
                    $no = 1;

                    if (mysqli_num_rows($result) > 0) {
                        while ($data = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($data['username']) . "</td>";
                            echo "<td><span class='badge " . ($data['level'] == 'admin' ? 'badge-danger' : 'badge-secondary') . "'>" . htmlspecialchars($data['level']) . "</span></td>";
                            echo "<td>";
                            echo '<a href="edit_pengguna.php?id=' . $data['id'] . '" class="btn btn-warning btn-sm mr-1" title="Edit Pengguna"><i class="bi bi-pencil-square"></i></a>';
                            
                            // Tombol hapus tidak akan muncul untuk akun yang sedang login
                            if ($_SESSION['username'] != $data['username']) {
                                echo '<a href="hapus_pengguna.php?id=' . $data['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus pengguna ini?\')" title="Hapus Pengguna"><i class="bi bi-trash3-fill"></i></a>';
                            }

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center py-4">Belum ada data pengguna.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'template_footer.php'; ?>