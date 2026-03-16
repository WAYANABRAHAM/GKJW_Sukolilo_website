<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan jemaat sudah login
if (!isset($_SESSION['pemilih_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Cek status fase pemilihan saat ini dari Admin
$query_fase = "SELECT isi_konten FROM konten_web WHERE kategori = 'fase_pemilihan'";
$hasil_fase = $conn->query($query_fase)->fetch_assoc();
$status_fase_aktif = $hasil_fase['isi_konten']; // 'tutup', 'fase1', atau 'fase2'

$status_jemaat_fase1 = $_SESSION['status_fase1'];
$status_jemaat_fase2 = $_SESSION['status_fase2'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Portal Voting - GKJW Sukolilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 text-center">
    <h2>Halo, <?php echo $_SESSION['nama_pemilih']; ?>!</h2>
    <p class="text-muted">Anda terdaftar di <?php echo $_SESSION['wilayah_pemilih']; ?></p>
    
    <div class="card p-5 mt-4 shadow-sm mx-auto" style="max-width: 600px;">
        <?php if ($status_fase_aktif == 'tutup'): ?>
            <h4 class="text-danger">⛔ Pemilihan Sedang Ditutup</h4>
            <p>Admin belum mengaktifkan sesi pemilihan majelis.</p>
            
        <?php elseif ($status_fase_aktif == 'fase1'): ?>
            <h4 class="text-primary">Fase 1: Pemilihan 48 Bakal Calon</h4>
            <?php if ($status_jemaat_fase1 == 1): ?>
                <div class="alert alert-success mt-3">✅ Anda sudah menggunakan hak suara pada Fase 1. Terima kasih!</div>
            <?php else: ?>
                <p>Silakan pilih 24 nama dari Wilayah 1 dan 24 nama dari Wilayah 2.</p>
                <a href="fase1.php" class="btn btn-primary btn-lg mt-2">Mulai Memilih Fase 1</a>
            <?php endif; ?>
            
        <?php elseif ($status_fase_aktif == 'fase2'): ?>
            <h4 class="text-success">Fase 2: Pemilihan 24 Majelis Terpilih</h4>
            <?php if ($status_jemaat_fase2 == 1): ?>
                <div class="alert alert-success mt-3">✅ Anda sudah menggunakan hak suara pada Fase 2. Terima kasih!</div>
            <?php else: ?>
                <p>Silakan pilih 12 nama dari Wilayah 1 dan 12 nama dari Wilayah 2 dari daftar kandidat yang lolos Fase 1.</p>
                <a href="fase2.php" class="btn btn-success btn-lg mt-2">Mulai Memilih Fase 2</a>
            <?php endif; ?>
        <?php endif; ?>
        
        <a href="logout.php" class="btn btn-outline-danger mt-4">Keluar (Logout)</a>
    </div>
</div>

</body>
</html>