<?php 
$page_title = "Tambah Data Pegawai";
include 'template_header.php'; // Gunakan template header

// Bagian 1: Logika PHP untuk Memproses Form
$pesan_error = "";

// Cek apakah tombol 'simpan' telah ditekan
if (isset($_POST['simpan'])) {
    // Ambil data dari form (tanpa mysqli_real_escape_string karena sudah pakai prepared statements)
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $golongan = $_POST['golongan'];
    $jabatan = $_POST['jabatan'];
    $unit_kerja = $_POST['unit_kerja'];
    $tmt_kerja = $_POST['tmt_kerja'];
    $nip_bps = $_POST['nip_bps'];
    $kode_org = $_POST['kode_org'];
    $wilayah = $_POST['wilayah'];
    $tmt_gol = $_POST['tmt_gol'];
    $status_pend = $_POST['status_pend'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $agama = $_POST['agama'];
    
     // Cek apakah NIP sudah ada (agar tidak duplikat)
     $cek_nip_query = "SELECT * FROM pegawai WHERE nip = ?";
     $cek_nip_stmt = mysqli_prepare($koneksi, $cek_nip_query);
     mysqli_stmt_bind_param($cek_nip_stmt, "s", $nip);
     mysqli_stmt_execute($cek_nip_stmt);
     $cek_nip_result = mysqli_stmt_get_result($cek_nip_stmt);
 
     if (mysqli_num_rows($cek_nip_result) > 0) {
         $pesan_error = "NIP sudah terdaftar! Data tidak dapat disimpan.";
     } else {
         // Query untuk INSERT data menggunakan prepared statement
         $query = "INSERT INTO pegawai (nip, nip_bps, nama, kode_org, wilayah, tgl_lahir, jenis_kelamin, golongan, tmt_gol, status_pend, tempat_lahir, agama, jabatan, unit_kerja, tmt_kerja) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         $stmt = mysqli_prepare($koneksi, $query);         
         
         // Penanganan eror jika prepare gagal
         if ($stmt === false) {
            $pesan_error = "Gagal mempersiapkan query: " . mysqli_error($koneksi);
         } else {
            mysqli_stmt_bind_param($stmt, "sssssssssssssss", $nip, $nip_bps, $nama, $kode_org, $wilayah, $tgl_lahir, $jenis_kelamin, $golongan, $tmt_gol, $status_pend, $tempat_lahir, $agama, $jabatan, $unit_kerja, $tmt_kerja);

            // Eksekusi query
            if (mysqli_stmt_execute($stmt)) {
                // Catat aktivitas ke log
                catat_log($koneksi, $_SESSION['username'], "Menambahkan pegawai baru dengan NIP: " . $nip);

                // Jika berhasil, alihkan kembali ke halaman data_pegawai.php
                header("location:data_pegawai.php?pesan=tambah_sukses");
                exit;
            } else {
                $pesan_error = "Gagal menyimpan data ke database: " . mysqli_stmt_error($stmt);
            }
         }
     }
 }
?>
                <h1 class="mt-4">Formulir Tambah Data Pegawai</h1>
                <p>Silakan isi data pegawai baru di bawah ini.</p>
                
                <?php if (!empty($pesan_error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $pesan_error; ?>
                    </div>
                <?php endif; ?>

                <form action="tambah_pegawai.php" method="POST">
                    <div class="form-group">
                        <label for="nip">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" required minlength="18" maxlength="18">
                    </div>
                    <div class="form-group">
                        <label for="nip_bps">NIP BPS</label>
                        <input type="text" class="form-control" id="nip_bps" name="nip_bps">
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_org">Kode.org</label>
                        <input type="text" class="form-control" id="kode_org" name="kode_org">
                    </div>
                    <div class="form-group">
                        <label for="wilayah">Wilayah</label>
                        <input type="text" class="form-control" id="wilayah" name="wilayah">
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                    </div>
                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir">
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <input type="text" class="form-control" id="agama" name="agama">
                    </div>
                    <div class="form-group">
                        <label for="golongan">Golongan Akhir</label>
                        <input type="text" class="form-control" id="golongan" name="golongan" placeholder="Contoh: III/d">
                    </div>
                    <div class="form-group">
                        <label for="tmt_gol">TMT Gol</label>
                        <input type="date" class="form-control" id="tmt_gol" name="tmt_gol">
                    </div>
                    <div class="form-group">
                        <label for="status_pend">Status.pend(SK)</label>
                        <input type="text" class="form-control" id="status_pend" name="status_pend">
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan">
                    </div>
                    <div class="form-group">
                        <label for="unit_kerja">Unit Kerja (Seksi)</label>
                        <input type="text" class="form-control" id="unit_kerja" name="unit_kerja" placeholder="Contoh: Seksi IPDS">
                    </div>
                     <div class="form-group">
                        <label for="tmt_kerja">TMT Kerja</label>
                        <input type="date" class="form-control" id="tmt_kerja" name="tmt_kerja">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                    <a href="data_pegawai.php" class="btn btn-secondary">Batal</a>
                </form>

            </div>
        </div>
        </div>
    </body>