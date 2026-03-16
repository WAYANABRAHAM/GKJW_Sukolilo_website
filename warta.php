<?php
require_once 'config/koneksi.php';
include 'includes/header.php';

// Ambil semua daftar warta untuk ditampilkan di menu sidebar
$query_warta = "SELECT * FROM warta_jemaat ORDER BY tanggal_upload DESC";
$hasil_warta = $conn->query($query_warta);

$warta_tersedia = [];
if ($hasil_warta->num_rows > 0) {
    while ($row = $hasil_warta->fetch_assoc()) {
        $warta_tersedia[] = $row;
    }
}

// Menentukan warta mana yang sedang dibuka (Berdasarkan parameter URL ?id=...)
$warta_aktif = null;
if (isset($_GET['id'])) {
    $id_aktif = (int)$_GET['id'];
    foreach ($warta_tersedia as $w) {
        if ($w['id_warta'] == $id_aktif) {
            $warta_aktif = $w;
            break;
        }
    }
}

// Jika tidak ada ID yang dipilih (baru buka halaman), otomatis tampilkan warta paling baru (urutan ke-0)
if (!$warta_aktif && count($warta_tersedia) > 0) {
    $warta_aktif = $warta_tersedia[0];
}
?>

<link href="https://cdn.jsdelivr.net/npm/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>

<div class="row mb-4">
    <div class="col-12 text-center">
        <h2>Warta Jemaat</h2>
        <p class="text-muted">Baca warta mingguan seperti membaca buku interaktif.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <div class="list-group-item bg-dark text-white fw-bold">
                Pilih Edisi Warta
            </div>
            <?php
            if (count($warta_tersedia) > 0) {
                foreach ($warta_tersedia as $warta) {
                    $tampil_nama = substr($warta['nama_file'], strpos($warta['nama_file'], "_") + 1);
                    $judul = str_replace('.pdf', '', $tampil_nama); 
                    
                    // Beri warna biru (active) pada warta yang sedang dibaca
                    $kelas_aktif = ($warta_aktif && $warta['id_warta'] == $warta_aktif['id_warta']) ? 'active' : '';
                    
                    // Link akan merefresh halaman dan mengirim parameter ID warta yang diklik
                    echo "<a class='list-group-item list-group-item-action {$kelas_aktif}' href='warta.php?id={$warta['id_warta']}'>
                            📄 {$judul}
                          </a>";
                }
            } else {
                echo "<div class='list-group-item text-muted'>Warta belum tersedia.</div>";
            }
            ?>
        </div>
    </div>

    <div class="col-md-9">
        <?php if ($warta_aktif): ?>
            <?php 
            $file_url = "assets/warta/" . $warta_aktif['nama_file']; 
            ?>
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary">Interaktif PDF Reader</span>
                    <a href="<?php echo $file_url; ?>" download class="btn btn-sm btn-success">
                        📥 Download File Asli
                    </a>
                </div>
                <div class="card-body p-0 bg-light" style="border-radius: 0 0 8px 8px; overflow: hidden;">
                    <div class="_df_book" source="<?php echo $file_url; ?>" id="flipbook_warta" style="height: 600px;"></div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="alert alert-secondary text-center">
                Belum ada Warta Jemaat yang diunggah oleh Admin.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Mengatasi bug z-index pada dFlip agar tidak menutupi navbar Bootstrap */
    .df-ui-wrapper { z-index: 90 !important; }
    .df-lightbox-wrapper { z-index: 1050 !important; }
</style>

<?php include 'includes/footer.php'; ?>