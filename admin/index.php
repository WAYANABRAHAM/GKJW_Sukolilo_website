<?php 
require_once '../config/koneksi.php';
include '../includes/admin_header.php'; 
?>

<div class="bg-primary text-white text-center py-5 mb-5 rounded shadow" style="background-image: url('assets/img/gereja-bg.jpg'); background-size: cover; background-position: center; position: relative;">
    <div style="background-color: rgba(0,0,0,0.5); position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: inherit;"></div>
    
    <div class="container position-relative z-1 py-4">
        <h1 class="fw-bold display-5">Selamat Datang di GKJW Sukolilo</h1>
        <p class="lead">Bersekutu, Bersaksi, dan Melayani dalam Kasih Kristus</p>
    </div>
</div>

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