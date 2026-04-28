<?php 
$page_title = "Laporan Data Pegawai";
include 'template_header.php';

// Ambil data untuk opsi filter dari database
$unit_kerja_list = mysqli_query($koneksi, "SELECT DISTINCT unit_kerja FROM pegawai WHERE unit_kerja != '' ORDER BY unit_kerja ASC");
$golongan_list = mysqli_query($koneksi, "SELECT DISTINCT golongan FROM pegawai WHERE golongan != '' ORDER BY golongan ASC");

// Ambil nilai filter dari URL (jika ada)
$filter_unit = isset($_GET['filter_unit']) ? $_GET['filter_unit'] : '';
$filter_golongan = isset($_GET['filter_golongan']) ? $_GET['filter_golongan'] : '';
$filter_gender = isset($_GET['filter_gender']) ? $_GET['filter_gender'] : '';
?>
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center page-header" id="page-header-laporan">
                    <div>
                        <h4 class="font-weight-bold mb-0">Laporan Data Pegawai</h4>
                        <p class="text-muted mb-0">Cetak laporan data seluruh pegawai BPS Kota Tanjungbalai.</p>
                    </div>
                    <div>
                        <?php $export_query_string = http_build_query($_GET); ?>
                        <a href="export_excel.php?<?php echo $export_query_string; ?>" class="btn btn-success font-weight-bold" id="btn-export">
                            <i class="bi bi-file-earmark-excel-fill mr-2"></i>Ekspor ke Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-primary font-weight-bold" id="btn-cetak">
                            <i class="bi bi-printer-fill mr-2"></i>Cetak Laporan
                        </button>
                    </div>
                </div>
                
                <!-- Panel Filter -->
                <div class="card shadow-sm mb-4" id="filter-panel">
                    <div class="card-body">
                        <form action="laporan.php" method="GET" class="form-inline">
                            <div class="form-group mr-3">
                                <label for="filter_unit" class="mr-2">Unit Kerja:</label>
                                <select name="filter_unit" id="filter_unit" class="form-control">
                                    <option value="">Semua</option>
                                    <?php while($unit = mysqli_fetch_assoc($unit_kerja_list)): ?>
                                        <option value="<?php echo htmlspecialchars($unit['unit_kerja']); ?>" <?php echo ($filter_unit == $unit['unit_kerja']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($unit['unit_kerja']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group mr-3">
                                <label for="filter_golongan" class="mr-2">Golongan:</label>
                                <select name="filter_golongan" id="filter_golongan" class="form-control">
                                    <option value="">Semua</option>
                                    <?php while($gol = mysqli_fetch_assoc($golongan_list)): ?>
                                        <option value="<?php echo htmlspecialchars($gol['golongan']); ?>" <?php echo ($filter_golongan == $gol['golongan']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($gol['golongan']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group mr-3">
                                <label for="filter_gender" class="mr-2">Jenis Kelamin:</label>
                                <select name="filter_gender" id="filter_gender" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="Laki-laki" <?php echo ($filter_gender == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo ($filter_gender == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info mr-2"><i class="bi bi-funnel-fill"></i> Terapkan</button>
                            <a href="laporan.php" class="btn btn-secondary">Reset</a>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom" id="laporan-table">
                                <thead>
                                    <tr>
                                        <th class="col-no">No</th>
                                        <th class="col-nip">NIP BPS</th>
                                        <th class="col-nip">NIP</th>
                                        <th class="col-nama">Nama</th>
                                        <th>Tempat Lahir</th>
                                        <th class="col-tanggal">Tgl Lahir</th>
                                        <th class="col-jk">Jenis Kelamin</th>
                                        <th>Agama</th>
                                        <th class="col-golongan">Gol Akhir</th>
                                        <th class="col-tanggal">TMT Gol</th>
                                        <th class="col-jabatan">Jabatan</th>
                                        <th class="col-unit">Unit Kerja</th>
                                        <th class="col-tanggal">TMT Kerja</th>
                                        <th>Status.pend(SK)</th>
                                        <th>Kode.org</th>
                                        <th>Wilayah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                            // Membangun query berdasarkan filter
                            $query = "SELECT * FROM pegawai";
                            $conditions = [];
                            if (!empty($filter_unit)) {
                                $conditions[] = "unit_kerja = '" . mysqli_real_escape_string($koneksi, $filter_unit) . "'";
                            }
                            if (!empty($filter_golongan)) {
                                $conditions[] = "golongan = '" . mysqli_real_escape_string($koneksi, $filter_golongan) . "'";
                            }
                            if (!empty($filter_gender)) {
                                $conditions[] = "jenis_kelamin = '" . mysqli_real_escape_string($koneksi, $filter_gender) . "'";
                            }
                            if (!empty($conditions)) {
                                $query .= " WHERE " . implode(' AND ', $conditions);
                            }
                            $query .= " ORDER BY nama ASC";
                            $result = mysqli_query($koneksi, $query);
                            $no = 1;
                            if (mysqli_num_rows($result) > 0) {
                                while ($data = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo '<td>' . $no++ . '</td>';
                                    echo '<td>' . htmlspecialchars($data['nip_bps']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['nip']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['nama']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['tempat_lahir']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['tgl_lahir']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['jenis_kelamin']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['agama']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['golongan']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['tmt_gol']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['jabatan']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['unit_kerja']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['tmt_kerja']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['status_pend']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['kode_org']) . '</td>';
                                    echo '<td>' . htmlspecialchars($data['wilayah']) . '</td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo '<tr><td colspan="16" class="text-center py-4">Tidak ada data yang cocok dengan filter yang diterapkan.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<style>
    @media print {
        /* Atur orientasi halaman menjadi landscape dan atur margin */
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body { 
            background-color: #fff; 
            font-size: 8pt; /* Ukuran font diperkecil lagi agar lebih muat */
        }

        /* Sembunyikan elemen yang tidak perlu dicetak */
        #sidebar-wrapper, .navbar, #page-header-laporan, #filter-panel { display: none !important; }
        
        /* Atur ulang layout konten utama untuk cetak */
        #page-content-wrapper { 
            padding: 0 !important; 
            margin: 0 !important; 
            width: 100% !important; 
        }
        .card { box-shadow: none; border: none; }
        /* Ini adalah bagian terpenting untuk memperbaiki tabel yang terpotong */
        .table-responsive { 
            display: block !important;
            width: 100% !important;
            overflow-x: visible !important; /* Memaksa tabel untuk tidak di-scroll secara horizontal */
        }
        #laporan-table { table-layout: auto; width: 100%; } /* Biarkan browser menyesuaikan lebar kolom */
        .table-custom th, .table-custom td {
            padding: 3px 5px !important; /* Padding diperkecil lagi */
            word-break: break-word; /* Izinkan pemotongan kata jika diperlukan */
        }
    }
</style>
<?php include 'template_footer.php'; ?>