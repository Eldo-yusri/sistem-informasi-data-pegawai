<?php 
$page_title = "Dashboard";
include 'template_header.php'; // Menggunakan template header yang sudah mencakup session_start() dan koneksi

// Cek apakah pengguna sudah login. Jika belum, kembalikan ke halaman login
if ($_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

// Logika untuk mengambil data grafik (dipindahkan dari grafik.php untuk digunakan di sini)
$query_gender = "SELECT jenis_kelamin, COUNT(*) as jumlah FROM pegawai GROUP BY jenis_kelamin";
$result_gender = mysqli_query($koneksi, $query_gender);
$data_gender = ['labels' => [], 'data' => []];
while($row = mysqli_fetch_assoc($result_gender)) { $data_gender['labels'][] = $row['jenis_kelamin']; $data_gender['data'][] = $row['jumlah']; }
$json_gender = json_encode($data_gender);

// Data untuk Grafik Golongan
$query_golongan = "SELECT golongan, COUNT(*) as jumlah FROM pegawai WHERE golongan != '' GROUP BY golongan ORDER BY golongan ASC";
$result_golongan = mysqli_query($koneksi, $query_golongan);
$data_golongan = ['labels' => [], 'data' => []];
while($row = mysqli_fetch_assoc($result_golongan)){
    $data_golongan['labels'][] = $row['golongan'];
    $data_golongan['data'][] = $row['jumlah'];
}
$json_golongan = json_encode($data_golongan);

// Data untuk Pegawai Terbaru
$query_terbaru = "SELECT nama, jabatan, tmt_kerja FROM pegawai ORDER BY tmt_kerja DESC LIMIT 5";
$result_terbaru = mysqli_query($koneksi, $query_terbaru);

// Data untuk Info Sistem
$php_version = phpversion();
$mysql_version = mysqli_get_server_info($koneksi);
?>
<!-- Konten halaman dimulai di sini, karena header sudah di-include -->
<div class="page-header">
    <h4 class="font-weight-bold mb-0">Dashboard</h4>
    <p class="text-muted mb-0">Ringkasan Informasi Data Pegawai BPS Kota Tanjungbalai.</p>
</div>
<div class="row">
                    <?php 
                    // Contoh Query untuk statistik
                    $total_pegawai_q = mysqli_query($koneksi, "SELECT COUNT(nip) AS total FROM pegawai");
                    $total_pegawai = mysqli_fetch_assoc($total_pegawai_q)['total'];

                    $total_l_q = mysqli_query($koneksi, "SELECT COUNT(nip) AS total FROM pegawai WHERE jenis_kelamin='Laki-laki'");
                    $total_l = mysqli_fetch_assoc($total_l_q)['total'];

                    $total_p_q = mysqli_query($koneksi, "SELECT COUNT(nip) AS total FROM pegawai WHERE jenis_kelamin='Perempuan'");
                    $total_p = mysqli_fetch_assoc($total_p_q)['total'];

                    $total_golongan_q = mysqli_query($koneksi, "SELECT COUNT(DISTINCT golongan) AS total FROM pegawai WHERE golongan != ''");
                    $total_golongan = mysqli_fetch_assoc($total_golongan_q)['total'];
                    ?>

    <div class="col-lg-3 col-md-6 mb-4">
        <a href="data_pegawai.php" class="text-decoration-none text-dark">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-primary text-white mr-3"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <p class="text-muted mb-0">Total Pegawai</p>
                        <h4 class="font-weight-bold mb-0"><?php echo $total_pegawai; ?></h4>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <a href="data_pegawai.php?filter_gender=Laki-laki" class="text-decoration-none text-dark">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-info text-white mr-3"><i class="bi bi-gender-male"></i></div>
                    <div>
                        <p class="text-muted mb-0">Pegawai Pria</p>
                        <h4 class="font-weight-bold mb-0"><?php echo $total_l; ?></h4>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <a href="data_pegawai.php?filter_gender=Perempuan" class="text-decoration-none text-dark">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-danger text-white mr-3"><i class="bi bi-gender-female"></i></div>
                    <div>
                        <p class="text-muted mb-0">Pegawai Wanita</p>
                        <h4 class="font-weight-bold mb-0"><?php echo $total_p; ?></h4>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <a href="data_pegawai.php" class="text-decoration-none text-dark"> <!-- Mengarahkan ke data_pegawai.php tanpa filter spesifik untuk golongan -->
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-warning text-white mr-3"><i class="bi bi-bar-chart-steps"></i></div>
                    <div>
                        <p class="text-muted mb-0">Jenis Golongan</p>
                        <h4 class="font-weight-bold mb-0"><?php echo $total_golongan; ?></h4>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

                <!-- Baris untuk Grafik -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100"> <!-- Removed shadow-sm as card already has shadow -->
                            <div class="card-header font-weight-bold">
                                Komposisi Pegawai Berdasarkan Jenis Kelamin
                            </div>
                            <div class="card-body">
                                <canvas id="grafikGender"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100"> <!-- Removed shadow-sm as card already has shadow -->
                            <div class="card-header font-weight-bold">
                                Distribusi Pegawai Berdasarkan Golongan
                            </div>
                            <div class="card-body">
                                <canvas id="grafikGolongan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris untuk Daftar Pegawai Terbaru -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card"> <!-- Removed shadow-sm as card already has shadow -->
                            <div class="card-header font-weight-bold">
                                <i class="bi bi-person-plus-fill mr-2"></i>5 Pegawai Terbaru (Berdasarkan TMT Kerja)
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php if(mysqli_num_rows($result_terbaru) > 0): ?>
                                    <?php while($pegawai = mysqli_fetch_assoc($result_terbaru)): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($pegawai['nama']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($pegawai['jabatan']); ?></small>
                                            </div>
                                            <span class="badge badge-info badge-pill"><?php echo date('d M Y', strtotime($pegawai['tmt_kerja'])); ?></span>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-center py-3">Belum ada data pegawai.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <!-- Panel Akses Cepat -->
                        <div class="card mb-4"> <!-- Removed shadow-sm as card already has shadow -->
                            <div class="card-header font-weight-bold">
                                <i class="bi bi-lightning-charge-fill mr-2"></i>Akses Cepat
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="tambah_pegawai.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Tambah Pegawai Baru <i class="bi bi-plus-circle"></i>
                                </a>
                                <a href="laporan.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Cetak Laporan <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Panel Info Sistem -->
                        <div class="card"> <!-- Removed shadow-sm as card already has shadow -->
                            <div class="card-header font-weight-bold">
                                <i class="bi bi-hdd-stack-fill mr-2"></i>Informasi Sistem
                            </div>
                            <div class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Versi PHP <span class="badge badge-secondary"><?php echo $php_version; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Versi MySQL <span class="badge badge-secondary"><?php echo $mysql_version; ?></span>
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari PHP
        const dataGender = <?php echo $json_gender; ?>;
        const dataGolongan = <?php echo $json_golongan; ?>;

        // Grafik Jenis Kelamin (Pie Chart)
        const ctxGender = document.getElementById('grafikGender').getContext('2d');
        new Chart(ctxGender, {
            type: 'doughnut', // Mengubah tipe grafik menjadi Doughnut
            data: {
                labels: dataGender.labels,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataGender.data,
                    backgroundColor: [ // Warna baru yang lebih profesional
                        'rgba(23, 162, 184, 0.8)', // Info Blue
                        'rgba(220, 53, 69, 0.8)'   // Danger Red
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            }
        });

        // Grafik Golongan (Bar Chart)
        const ctxGolongan = document.getElementById('grafikGolongan').getContext('2d');
        new Chart(ctxGolongan, {
            type: 'bar', // Tipe tetap bar, namun diubah menjadi horizontal di options
            data: {
                labels: dataGolongan.labels,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataGolongan.data,
                    backgroundColor: 'rgba(232, 97, 0, 0.7)', // Primary Orange
                    borderColor: 'rgba(232, 97, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Ini kunci untuk membuat bar chart menjadi horizontal
                responsive: true,
                plugins: { legend: { display: false } }, // Sembunyikan legenda agar lebih rapi
                scales: {
                    x: { beginAtZero: true } // Sumbu X sekarang yang dimulai dari 0
                }
            }
        });
    </script>

<style>
    /* Style tambahan khusus untuk ikon di dashboard */
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
<?php include 'template_footer.php'; ?>