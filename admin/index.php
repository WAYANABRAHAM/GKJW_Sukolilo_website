<?php 
require_once '../config/koneksi.php';
include '../includes/admin_header.php'; 
?>

<h2>Selamat Datang, Admin!</h2>
<p>Pilih menu di atas untuk mengelola konten website GKJW Sukolilo.</p>

<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Update YouTube</h5>
                <p class="card-text">Ganti link streaming ibadah minggu ini.</p>
                <a href="youtube.php" class="btn btn-light btn-sm">Kelola</a>
            </div>
        </div>
    </div>
    </div>

<?php include '../includes/admin_footer.php'; ?>