<?php
require_once '../config/koneksi.php';
include '../includes/admin_header.php';

$pesan = "";

// Jika admin menekan tombol Simpan
if (isset($_POST['update_youtube'])) {
    $link_baru = $_POST['link_youtube'];
    
    // Mencegah error karakter khusus
    $link_baru = $conn->real_escape_string($link_baru);

    // Update data dimana kategorinya 'youtube'
    $query_update = "UPDATE konten_web SET isi_konten = '$link_baru' WHERE kategori = 'youtube'";
    
    if ($conn->query($query_update) === TRUE) {
        $pesan = "<div class='alert alert-success'>Link YouTube berhasil diperbarui!</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui: " . $conn->error . "</div>";
    }
}

// Ambil link yang sedang aktif saat ini untuk ditampilkan di form
$query_tampil = "SELECT isi_konten FROM konten_web WHERE kategori = 'youtube'";
$hasil = $conn->query($query_tampil);
$data_saat_ini = $hasil->fetch_assoc();
?>

<h3>Update Link YouTube Streaming</h3>
<hr>

<?php echo $pesan; ?>

<div class="card p-4 shadow-sm mb-4">
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label fw-bold">Link Embed YouTube Saat Ini:</label>
            <input type="text" name="link_youtube" class="form-control" value="<?php echo htmlspecialchars($data_saat_ini['isi_konten']); ?>" required>
            <div class="form-text">Pastikan mengambil link "Embed" dari YouTube (contoh: <code>https://www.youtube.com/embed/xxxxx</code>).</div>
        </div>
        <button type="submit" name="update_youtube" class="btn btn-primary">Simpan Link Baru</button>
    </form>
</div>

<div class="card p-4 shadow-sm">
    <h5 class="mb-3">Preview Video Aktif:</h5>
    <div class="ratio ratio-16x9 w-50">
        <iframe src="<?php echo htmlspecialchars($data_saat_ini['isi_konten']); ?>" allowfullscreen></iframe>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>