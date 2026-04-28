<?php 
$page_title = "Data Pegawai";
include 'template_header.php';
?>
                <div class="d-flex justify-content-between align-items-center page-header">
                    <div>
                        <h4 class="font-weight-bold mb-0">Manajemen Data Pegawai</h4>
                        <p class="text-muted mb-0">Kelola data seluruh pegawai BPS Kota Tanjungbalai.</p>
                    </div>
                    <a href="tambah_pegawai.php" class="btn btn-primary font-weight-bold"><i class="bi bi-plus-circle-fill mr-2"></i>Tambah Data</a>
                </div>
                
                <!-- Kolom Pencarian -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="data_pegawai.php" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari berdasarkan Nama, NIP, atau NIP BPS..." name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if(isset($_GET['pesan'])): ?>
                    <div class="alert alert-custom-success" role="alert">
                        <?php 
                            if($_GET['pesan'] == 'tambah_sukses') echo 'Data pegawai berhasil ditambahkan!';
                            if($_GET['pesan'] == 'edit_sukses') echo 'Data pegawai berhasil diperbarui!';
                            if($_GET['pesan'] == 'hapus_sukses') echo 'Data pegawai berhasil dihapus!';
                            if($_GET['pesan'] == 'akses_ditolak') {
                                echo '<div class="alert alert-danger" role="alert">Anda tidak memiliki hak akses untuk melakukan aksi tersebut.</div>';
                            }
                        ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom">
                                <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP BPS</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Tempat Lahir</th>
                                <th>Tgl Lahir</th>
                                <th>Jenis Kelamin</th>
                                <th>Agama</th>
                                <th>Gol Akhir</th>
                                <th>TMT Gol</th>
                                <th>Jabatan</th>
                                <th>Unit Kerja</th>
                                <th>TMT Kerja</th>
                                <th>Status.pend(SK)</th>
                                <th>Kode.org</th>
                                <th>Wilayah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // --- LOGIKA PAGINASI ---
                            $limit = 10; // Jumlah data per halaman
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($page - 1) * $limit;

                            // Query untuk menghitung total data (diperlukan untuk paginasi)
                            $count_query = "SELECT COUNT(*) as total FROM pegawai";
                            // Tambahkan kondisi filter/pencarian ke query hitung juga
                            $count_conditions = [];
                            if (isset($_GET['filter_gender']) && !empty($_GET['filter_gender'])) {
                                $count_conditions[] = "jenis_kelamin = '" . mysqli_real_escape_string($koneksi, $_GET['filter_gender']) . "'";
                            }
                            if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
                                $keyword_count = "'%" . mysqli_real_escape_string($koneksi, $_GET['keyword']) . "%'";
                                $count_conditions[] = "(nama LIKE $keyword_count OR nip LIKE $keyword_count OR nip_bps LIKE $keyword_count)";
                            }
                            if (!empty($count_conditions)) {
                                $count_query .= " WHERE " . implode(' AND ', $count_conditions);
                            }
                            $count_result = mysqli_query($koneksi, $count_query);
                            $total_data = mysqli_fetch_assoc($count_result)['total'];
                            $total_pages = ceil($total_data / $limit);
                            // --- END LOGIKA PAGINASI ---

                            // Siapkan query dasar
                            $query = "SELECT * FROM pegawai ";
                            $conditions = [];
                            $params = [];
                            $types = '';

                            // Cek filter dari dashboard
                            if (isset($_GET['filter_gender']) && !empty($_GET['filter_gender'])) {
                                $conditions[] = "jenis_kelamin = ?";
                                $params[] = $_GET['filter_gender'];
                                $types .= 's';
                            }

                            // Cek keyword pencarian dari form
                            if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
                                $keyword = "%" . $_GET['keyword'] . "%";
                                $conditions[] = "(nama LIKE ? OR nip LIKE ? OR nip_bps LIKE ?)";
                                $params = array_merge($params, [$keyword, $keyword, $keyword]);
                                $types .= 'sss';
                            }

                            if (!empty($conditions)) {
                                $query .= " WHERE " . implode(' AND ', $conditions);
                            }
                            $query .= " ORDER BY nama ASC LIMIT $limit OFFSET $offset";

                            // Menyiapkan dan menjalankan query dengan cara yang aman (prepared statement)
                            $stmt = mysqli_prepare($koneksi, $query);

                            if ($stmt) {
                                // Bind parameter jika ada
                                if (!empty($params)) {
                                    // '...' (splat operator) untuk membongkar array menjadi argumen individual
                                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                                }
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                            } else {
                                // Jika query gagal dipersiapkan, set result ke false
                                $result = false;
                            }
                            
                            $no = $offset + 1; // Variabel nomor urut disesuaikan dengan halaman

                            if ($result && mysqli_num_rows($result) > 0) {
                                // Looping untuk menampilkan setiap baris data
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
                                    echo '<td>';
                                    echo '<button type="button" class="btn btn-info btn-sm mr-1 view-detail" data-toggle="modal" data-target="#detailPegawaiModal" data-nip="' . $data['nip'] . '"><i class="bi bi-eye"></i></button>';
                                    echo '<a href="edit_pegawai.php?nip=' . $data['nip'] . '" class="btn btn-warning btn-sm mr-1 has-tooltip" data-toggle="tooltip" data-placement="top" title="Edit Data"><i class="bi bi-pencil-square"></i></a>';
                                    echo '<a href="hapus_pegawai.php?nip=' . $data['nip'] . '" class="btn btn-danger btn-sm has-tooltip" onclick="return confirm(\'Yakin ingin menghapus data ini?\')" data-toggle="tooltip" data-placement="top" title="Hapus Data"><i class="bi bi-trash3-fill"></i></a>';
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                // Tampilkan pesan jika data masih kosong
                                echo '<tr><td colspan="17" class="text-center py-4">Belum ada data pegawai yang cocok dengan kriteria.</td></tr>';
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
                        echo '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="?page=' . $prev_page . '&' . http_build_query($_GET, '', '&') . '">Sebelumnya</a></li>';

                        // Link halaman
                        for ($i = 1; $i <= $total_pages; $i++) {
                            // Menambahkan parameter GET yang sudah ada ke link paginasi
                            $query_params = http_build_query(array_merge($_GET, ['page' => $i]));
                            echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?' . $query_params . '">' . $i . '</a></li>';
                        }

                        // Link ke halaman berikutnya
                        $next_page = $page < $total_pages ? $page + 1 : $total_pages;
                        echo '<li class="page-item ' . ($page >= $total_pages ? 'disabled' : '') . '"><a class="page-link" href="?page=' . $next_page . '&' . http_build_query($_GET, '', '&') . '">Berikutnya</a></li>';
                        ?>
                    </ul>
                </nav>
            </div>
<script>
    $(document).ready(function() {
        // Inisialisasi tooltip HANYA pada elemen dengan kelas .has-tooltip
        $('.has-tooltip').tooltip();

        // Menggunakan event 'show.bs.modal' dari Bootstrap.
        // Event ini akan berjalan TEPAT SEBELUM modal ditampilkan, mencegah efek kedip.
        $('#detailPegawaiModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Dapatkan tombol yang memicu modal
            // Ekstrak NIP dari atribut data-*
            var nip = button.data('nip'); 
            var modal = $(this);
            
            // Atur modal ke status loading sebelum animasi modal dimulai
            modal.find('.modal-title').text('Memuat Detail Pegawai...');
            $('#detailPegawaiModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Memuat data...</p></div>');

            // Lakukan AJAX request untuk mengambil data detail
            $.ajax({
                url: 'get_pegawai_detail.php',
                type: 'GET',
                data: { nip: nip },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var pegawai = response.data;
                        // Bangun konten HTML untuk detail pegawai
                        var modalBody = `
                            <table class="table table-bordered table-sm">
                                <tr><th style="width: 30%;">NIP BPS</th><td>${pegawai.nip_bps || '-'}</td></tr>
                                <tr><th>NIP</th><td>${pegawai.nip || '-'}</td></tr>
                                <tr><th>Nama</th><td><strong>${pegawai.nama || '-'}</strong></td></tr>
                                <tr><th>Tempat, Tgl Lahir</th><td>${pegawai.tempat_lahir || '-'}, ${pegawai.tgl_lahir || '-'}</td></tr>
                                <tr><th>Jenis Kelamin</th><td>${pegawai.jenis_kelamin || '-'}</td></tr>
                                <tr><th>Agama</th><td>${pegawai.agama || '-'}</td></tr>
                                <tr><th>Golongan Akhir</th><td>${pegawai.golongan || '-'}</td></tr>
                                <tr><th>TMT Golongan</th><td>${pegawai.tmt_gol || '-'}</td></tr>
                                <tr><th>Jabatan</th><td>${pegawai.jabatan || '-'}</td></tr>
                                <tr><th>Unit Kerja</th><td>${pegawai.unit_kerja || '-'}</td></tr>
                                <tr><th>TMT Kerja</th><td>${pegawai.tmt_kerja || '-'}</td></tr>
                                <tr><th>Status Pendidikan (SK)</th><td>${pegawai.status_pend || '-'}</td></tr>
                                <tr><th>Kode Organisasi</th><td>${pegawai.kode_org || '-'}</td></tr>
                                <tr><th>Wilayah</th><td>${pegawai.wilayah || '-'}</td></tr>
                            </table>
                        `;
                        // Perbarui judul dan isi modal dengan data yang diterima
                        modal.find('.modal-title').text('Detail Pegawai: ' + pegawai.nama);
                        modal.find('.modal-body').html(modalBody);
                    } else {
                        modal.find('.modal-body').html('<p class="text-danger text-center">' + response.message + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    modal.find('.modal-title').text('Error Koneksi');
                    modal.find('.modal-body').html('<p class="text-danger text-center">Gagal memuat data. Silakan coba lagi.</p>');
                    console.error("AJAX Error: ", status, error);
                }
            });
        });
    });
</script>

<!-- Modal untuk Detail Pegawai -->
<div class="modal fade" id="detailPegawaiModal" tabindex="-1" aria-labelledby="detailPegawaiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPegawaiModalLabel">Detail Pegawai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="detailPegawaiModalBody">
        <!-- Konten detail pegawai akan dimuat di sini melalui AJAX -->
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat data...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<?php include 'template_footer.php'; ?>