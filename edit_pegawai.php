<?php
$page_title = "Edit Data Pegawai";
include 'template_header.php';

// Cek apakah NIP ada di parameter URL
if (!isset($_GET['nip'])) {
    header("location:data_pegawai.php");
    exit;
}

$nip = mysqli_real_escape_string($koneksi, $_GET['nip']);

// Query untuk mengambil data pegawai berdasarkan NIP
$query = "SELECT * FROM pegawai WHERE nip = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $nip);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan, redirect ke data_pegawai.php
if (!$data) {
    header("location:data_pegawai.php");
    exit;
}
?>
            <div class="container-fluid p-4">
                <h1 class="mt-4">Edit Data Pegawai</h1>
                
                <form action="proses_edit_pegawai.php" method="POST">
                    <input type="hidden" name="nip_lama" value="<?php echo htmlspecialchars($data['nip']); ?>">

                    <div class="form-group">
                        <label for="nip">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" value="<?php echo htmlspecialchars($data['nip']); ?>" required minlength="18" maxlength="18">
                    </div>
                    <div class="form-group">
                        <label for="nip_bps">NIP BPS</label>
                        <input type="text" class="form-control" id="nip_bps" name="nip_bps" value="<?php echo htmlspecialchars($data['nip_bps'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_org">Kode.org</label>
                        <input type="text" class="form-control" id="kode_org" name="kode_org" value="<?php echo htmlspecialchars($data['kode_org'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="wilayah">Wilayah</label>
                        <input type="text" class="form-control" id="wilayah" name="wilayah" value="<?php echo htmlspecialchars($data['wilayah'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?php echo htmlspecialchars($data['tempat_lahir'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?php echo htmlspecialchars($data['tgl_lahir'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="Laki-laki" <?php echo (($data['jenis_kelamin'] ?? '') == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo (($data['jenis_kelamin'] ?? '') == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <input type="text" class="form-control" id="agama" name="agama" value="<?php echo htmlspecialchars($data['agama'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="golongan">Golongan Akhir</label>
                        <input type="text" class="form-control" id="golongan" name="golongan" value="<?php echo htmlspecialchars($data['golongan'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tmt_gol">TMT Gol</label>
                        <input type="date" class="form-control" id="tmt_gol" name="tmt_gol" value="<?php echo htmlspecialchars($data['tmt_gol'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="status_pend">Status.pend(SK)</label>
                        <input type="text" class="form-control" id="status_pend" name="status_pend" value="<?php echo htmlspecialchars($data['status_pend'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($data['jabatan'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="unit_kerja">Unit Kerja (Seksi)</label>
                        <input type="text" class="form-control" id="unit_kerja" name="unit_kerja" value="<?php echo htmlspecialchars($data['unit_kerja'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tmt_kerja">TMT Kerja</label>
                        <input type="date" class="form-control" id="tmt_kerja" name="tmt_kerja" value="<?php echo htmlspecialchars($data['tmt_kerja'] ?? ''); ?>">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="data_pegawai.php" class="btn btn-secondary">Batal</a>
                </form>

            </div>
<?php include 'template_footer.php'; ?>