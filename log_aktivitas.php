<?php
$page_title = "Log Aktivitas";
include 'template_header.php';

// Proteksi Halaman: Hanya admin yang boleh mengakses halaman ini
if ($_SESSION['level'] != 'admin') {
    header("location:index.php?pesan=akses_ditolak");
    exit;
}
?>

<div class="page-header">
    <h4 class="font-weight-bold mb-0">Log Aktivitas Pengguna</h4>
    <p class="text-muted mb-0">Rekam jejak semua aktivitas penting dalam sistem.</p>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-custom">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Waktu</th>
                        <th>Username</th>
                        <th>Aksi yang Dilakukan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // --- LOGIKA PAGINASI ---
                    $limit = 15; // Jumlah log per halaman
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $count_query = "SELECT COUNT(*) as total FROM logs";
                    $count_result = mysqli_query($koneksi, $count_query);
                    $total_data = mysqli_fetch_assoc($count_result)['total'];
                    $total_pages = ceil($total_data / $limit);
                    // --- END LOGIKA PAGINASI ---

                    $query = "SELECT username, aksi, waktu FROM logs ORDER BY waktu DESC LIMIT $limit OFFSET $offset";
                    $result = mysqli_query($koneksi, $query);
                    $no = $offset + 1;

                    if (mysqli_num_rows($result) > 0) {
                        while ($data = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars(date('d M Y, H:i:s', strtotime($data['waktu']))) . "</td>";
                            echo "<td>" . htmlspecialchars($data['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['aksi']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center py-4">Belum ada aktivitas yang tercatat.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Navigasi Paginasi -->
    <div class="card-footer bg-white">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <?php
                // Link ke halaman sebelumnya
                $prev_page = $page > 1 ? $page - 1 : 1;
                echo '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="?page=' . $prev_page . '">Sebelumnya</a></li>';

                // Link halaman
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }

                // Link ke halaman berikutnya
                $next_page = $page < $total_pages ? $page + 1 : $total_pages;
                echo '<li class="page-item ' . ($page >= $total_pages ? 'disabled' : '') . '"><a class="page-link" href="?page=' . $next_page . '">Berikutnya</a></li>';
                ?>
            </ul>
        </nav>
    </div>
</div>

<?php include 'template_footer.php'; ?>