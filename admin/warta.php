<?php
require_once '../config/koneksi.php';
include '../includes/admin_header.php';

$pesan = "";

// Jika admin melakukan upload file
if (isset($_POST['upload_warta'])) {
    $nama_file_asli = $_FILES['file_pdf']['name'];
    $tmp_file = $_FILES['file_pdf']['tmp_name'];
    $ukuran_file = $_FILES['file_pdf']['size'];
    $ekstensi = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));

    // Validasi apakah file benar-benar PDF
    if ($ekstensi != "pdf") {
        $pesan = "<div class='alert alert-danger'>Gagal: File harus berformat PDF!</div>";
    } elseif ($ukuran_file > 5000000) { // Maksimal 5MB
        $pesan = "<div class='alert alert-danger'>Gagal: Ukuran file maksimal 5MB!</div>";
    } else {
        // Buat nama file unik agar tidak bentrok
        $nama_file_baru = time() . "_" . preg_replace('/[^A-Za-z0-9.\-]/', '_', $nama_file_asli);
        $path_simpan = "../assets/warta/" . $nama_file_baru;

        // Cek jumlah warta yang ada saat ini
        $query_cek = "SELECT id_warta, nama_file FROM warta_jemaat ORDER BY tanggal_upload ASC";
        $result_cek = $conn->query($query_cek);

        // Jika sudah ada 3 atau lebih, hapus yang paling lama (urutan pertama)
        if ($result_cek->num_rows >= 3) {
            $data_lama = $result_cek->fetch_assoc();
            $id_hapus = $data_lama['id_warta'];
            $file_hapus = "../assets/warta/" . $data_lama['nama_file'];

            // Hapus file fisik jika ada
            if (file_exists($file_hapus)) {
                unlink($file_hapus);
            }
            // Hapus dari database
            $conn->query("DELETE FROM warta_jemaat WHERE id_warta = '$id_hapus'");
        }

        // Simpan file baru
        if (move_uploaded_file($tmp_file, $path_simpan)) {
            $query_insert = "INSERT INTO warta_jemaat (nama_file) VALUES ('$nama_file_baru')";
            $conn->query($query_insert);
            $pesan = "<div class='alert alert-success'>Warta Jemaat berhasil diupload!</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal mengupload file ke server.</div>";
        }
    }
}

// Fitur Hapus Manual oleh Admin
if (isset($_GET['hapus'])) {
    $id_hapus = $conn->real_escape_string($_GET['hapus']);
    
    // Cari nama file
    $query_file = "SELECT nama_file FROM warta_jemaat WHERE id_warta = '$id_hapus'";
    $hasil = $conn->query($query_file);
    if ($hasil->num_rows > 0) {
        $data = $hasil->fetch_assoc();
        $file_path = "../assets/warta/" . $data['nama_file'];
        
        if (file_exists($file_path)) { unlink($file_path); }
        $conn->query("DELETE FROM warta_jemaat WHERE id_warta = '$id_hapus'");
        $pesan = "<div class='alert alert-success'>Warta berhasil dihapus!</div>";
    }
}
?>

<h3>Kelola Warta Jemaat</h3>
<hr>
<?php echo $pesan; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card p-3 shadow-sm mb-4">
            <h5>Upload Warta Baru (PDF)</h5>
            <p class="text-muted small">Sistem maksimal menyimpan 3 warta. File tertua akan terganti otomatis.</p>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="file_pdf" class="form-control" accept=".pdf" required>
                </div>
                <button type="submit" name="upload_warta" class="btn btn-primary w-100">Upload PDF</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card p-3 shadow-sm">
            <h5>Daftar Warta Saat Ini</h5>
            <table class="table table-bordered mt-2">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $warta_query = $conn->query("SELECT * FROM warta_jemaat ORDER BY tanggal_upload DESC");
                    $no = 1;
                    if ($warta_query->num_rows > 0) {
                        while ($row = $warta_query->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$no}</td>";
                            // Tampilkan nama file lebih rapi (hilangkan timestamp)
                            $tampil_nama = substr($row['nama_file'], strpos($row['nama_file'], "_") + 1);
                            echo "<td>{$tampil_nama}</td>";
                            echo "<td>{$row['tanggal_upload']}</td>";
                            echo "<td>
                                    <a href='../assets/warta/{$row['nama_file']}' target='_blank' class='btn btn-sm btn-info'>Lihat</a>
                                    <a href='warta.php?hapus={$row['id_warta']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus warta ini?\")'>Hapus</a>
                                  </td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Belum ada warta yang diupload.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>