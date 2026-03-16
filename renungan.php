<?php
require_once 'config/koneksi.php';
include 'includes/header.php';

// Cek paramater 'waktu' di URL. Default ke 'pagi' jika kosong atau diketik sembarangan.
$waktu = isset($_GET['waktu']) ? $_GET['waktu'] : 'pagi';

if ($waktu == 'malam') {
    $kategori_db = 'renungan_malam';
    $judul_halaman = "Renungan Malam";
    $ikon = "🌃";
    $tema_warna = "bg-dark text-white";
} else {
    $kategori_db = 'renungan_pagi';
    $judul_halaman = "Renungan Pagi";
    $ikon = "🌅";
    $tema_warna = "bg-warning text-dark";
}

// Ambil isi renungan dari database
$query = "SELECT isi_konten, diupdate_pada FROM konten_web WHERE kategori = '$kategori_db'";
$hasil = $conn->query($query);
$data_renungan = $hasil->fetch_assoc();

// Format tanggal update
$tanggal_update = date('d F Y', strtotime($data_renungan['diupdate_pada']));
?>

<div class="row justify-content-center mb-5">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header <?php echo $tema_warna; ?> text-center py-3">
                <h3 class="mb-0"><?php echo $ikon . " " . $judul_halaman; ?></h3>
                <small>Update terakhir: <?php echo $tanggal_update; ?></small>
            </div>
            
            <div class="card-body p-4" style="line-height: 1.8; font-size: 1.1rem;">
                <?php echo $data_renungan['isi_konten']; ?>
            </div>
            
            <div class="card-footer text-center bg-light">
                <?php if ($waktu == 'pagi'): ?>
                    <a href="renungan.php?waktu=malam" class="btn btn-outline-dark">Baca Renungan Malam 🌃</a>
                <?php else: ?>
                    <a href="renungan.php?waktu=pagi" class="btn btn-outline-warning text-dark">Baca Renungan Pagi 🌅</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>