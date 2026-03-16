<?php
// 1. Panggil koneksi database
require_once 'config/koneksi.php';

// 2. Ambil link YouTube dari database
// Kita ambil data dimana kategorinya adalah 'youtube'
$query_yt = "SELECT isi_konten FROM konten_web WHERE kategori = 'youtube' LIMIT 1";
$result_yt = $conn->query($query_yt);

$link_youtube = "";
if ($result_yt->num_rows > 0) {
    $row = $result_yt->fetch_assoc();
    $link_youtube = $row['isi_konten'];
} else {
    $link_youtube = "Link streaming belum diatur oleh admin.";
}

// 3. Panggil bagian Header
include 'includes/header.php';
?>

<div class="row text-center mb-4">
    <div class="col-12">
        <h2 class="mb-3">Selamat Datang di Ibadah Minggu GKJW Sukolilo</h2>
        <p class="text-muted">Ikuti ibadah secara langsung melalui tayangan di bawah ini.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
            <?php if(strpos($link_youtube, 'youtube.com/embed') !== false || strpos($link_youtube, 'youtu.be') !== false): ?>
                <iframe src="<?php echo htmlspecialchars($link_youtube); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center bg-secondary text-white h-100">
                    <p class="mb-0">Video Streaming Belum Tersedia / Link Tidak Valid.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// 4. Panggil bagian Footer
include 'includes/footer.php';
?>