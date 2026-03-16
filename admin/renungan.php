<?php
require_once '../config/koneksi.php';
include '../includes/admin_header.php';

$pesan = "";

// Proses Update Renungan Pagi
if (isset($_POST['update_pagi'])) {
    // Biarkan tag HTML lolos karena berasal dari editor teks
    $isi_pagi = $conn->real_escape_string($_POST['isi_pagi']);
    $query = "UPDATE konten_web SET isi_konten = '$isi_pagi' WHERE kategori = 'renungan_pagi'";
    if ($conn->query($query) === TRUE) {
        $pesan = "<div class='alert alert-success'>Renungan Pagi berhasil diperbarui!</div>";
    }
}

// Proses Update Renungan Malam
if (isset($_POST['update_malam'])) {
    $isi_malam = $conn->real_escape_string($_POST['isi_malam']);
    $query = "UPDATE konten_web SET isi_konten = '$isi_malam' WHERE kategori = 'renungan_malam'";
    if ($conn->query($query) === TRUE) {
        $pesan = "<div class='alert alert-success'>Renungan Malam berhasil diperbarui!</div>";
    }
}

// Ambil data renungan saat ini untuk ditampilkan di dalam editor
$query_pagi = $conn->query("SELECT isi_konten FROM konten_web WHERE kategori = 'renungan_pagi'");
$data_pagi = $query_pagi->fetch_assoc();

$query_malam = $conn->query("SELECT isi_konten FROM konten_web WHERE kategori = 'renungan_malam'");
$data_malam = $query_malam->fetch_assoc();
?>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '.teks-editor', // Targetkan class ini untuk dijadikan editor
    plugins: 'lists link',
    toolbar: 'undo redo | bold italic strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist',
    menubar: false,
    height: 300
  });
</script>

<h3>Kelola Renungan Harian</h3>
<hr>
<?php echo $pesan; ?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark fw-bold">
                🌅 Update Renungan Pagi
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <textarea name="isi_pagi" class="form-control teks-editor"><?php echo $data_pagi['isi_konten']; ?></textarea>
                    </div>
                    <button type="submit" name="update_pagi" class="btn btn-warning w-100">Simpan Renungan Pagi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">
                🌃 Update Renungan Malam
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <textarea name="isi_malam" class="form-control teks-editor"><?php echo $data_malam['isi_konten']; ?></textarea>
                    </div>
                    <button type="submit" name="update_malam" class="btn btn-dark w-100">Simpan Renungan Malam</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>