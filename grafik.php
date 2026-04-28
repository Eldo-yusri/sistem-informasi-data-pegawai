<?php 
$page_title = "Grafik Pegawai";
include 'template_header.php';
 
// --- LOGIKA PENGAMBILAN DATA UNTUK GRAFIK ---

// 1. Data untuk Grafik Jenis Kelamin
$query_gender = "SELECT jenis_kelamin, COUNT(*) as jumlah FROM pegawai GROUP BY jenis_kelamin";
$result_gender = mysqli_query($koneksi, $query_gender);
$data_gender = [];
while($row = mysqli_fetch_assoc($result_gender)){
    $data_gender['labels'][] = $row['jenis_kelamin'];
    $data_gender['data'][] = $row['jumlah'];
}
$json_gender = json_encode($data_gender);

// 2. Data untuk Grafik Golongan
$query_golongan = "SELECT golongan, COUNT(*) as jumlah FROM pegawai WHERE golongan != '' GROUP BY golongan ORDER BY golongan ASC";
$result_golongan = mysqli_query($koneksi, $query_golongan);
$data_golongan = [];
while($row = mysqli_fetch_assoc($result_golongan)){
    $data_golongan['labels'][] = $row['golongan'];
    $data_golongan['data'][] = $row['jumlah'];
}
$json_golongan = json_encode($data_golongan);

// 3. Data untuk Grafik Unit Kerja
$query_unit = "SELECT unit_kerja, COUNT(*) as jumlah FROM pegawai WHERE unit_kerja != '' GROUP BY unit_kerja ORDER BY jumlah DESC";
$result_unit = mysqli_query($koneksi, $query_unit);
$data_unit = [];
while($row = mysqli_fetch_assoc($result_unit)){
    $data_unit['labels'][] = $row['unit_kerja'];
    $data_unit['data'][] = $row['jumlah'];
}
$json_unit = json_encode($data_unit);

?>
            <div class="container-fluid p-4">
                <div class="page-header">
                    <h4 class="font-weight-bold mb-0">Grafik Data Pegawai</h4>
                    <p class="text-muted mb-0">Visualisasi data kepegawaian BPS Kota Tanjungbalai.</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header font-weight-bold">
                                Komposisi Pegawai Berdasarkan Jenis Kelamin
                            </div>
                            <div class="card-body">
                                <canvas id="grafikGender"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header font-weight-bold">
                                Distribusi Pegawai Berdasarkan Golongan
                            </div>
                            <div class="card-body">
                                <canvas id="grafikGolongan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header font-weight-bold">
                                Distribusi Pegawai Berdasarkan Unit Kerja (Seksi)
                            </div>
                            <div class="card-body">
                                <canvas id="grafikUnitKerja"></canvas>
                            </div>
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
        const dataUnit = <?php echo $json_unit; ?>;

        // Grafik 1: Jenis Kelamin (Pie Chart)
        const ctxGender = document.getElementById('grafikGender').getContext('2d');
        new Chart(ctxGender, {
            type: 'polarArea', // Tipe diubah menjadi Polar Area
            data: {
                labels: dataGender.labels,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataGender.data,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)', // Biru
                        'rgba(255, 99, 132, 0.8)' // Pink
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 2
                }]
            }
        });

        // Grafik 2: Golongan (Bar Chart)
        const ctxGolongan = document.getElementById('grafikGolongan').getContext('2d');
        new Chart(ctxGolongan, {
            type: 'bar',
            data: {
                labels: dataGolongan.labels,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataGolongan.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)', // Warna baru: Teal
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: { // Konfigurasi untuk menampilkan label di atas bar
                        anchor: 'end',
                        align: 'top',
                        formatter: Math.round,
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Grafik 3: Unit Kerja (Horizontal Bar Chart)
        const ctxUnit = document.getElementById('grafikUnitKerja').getContext('2d');
        new Chart(ctxUnit, {
            type: 'bar',
            data: {
                labels: dataUnit.labels,
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: dataUnit.data,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)', // Oranye
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Membuat chart menjadi horizontal
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    </script>
<?php include 'template_footer.php'; ?>