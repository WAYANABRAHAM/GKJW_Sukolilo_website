<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi Keamanan: Pastikan sudah login dan fase memang sedang Fase 1
if (!isset($_SESSION['pemilih_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah admin benar-benar membuka Fase 1
$q_fase = $conn->query("SELECT isi_konten FROM konten_web WHERE kategori = 'fase_pemilihan'");
$fase_aktif = $q_fase->fetch_assoc()['isi_konten'];
if ($fase_aktif != 'fase1') {
    header("Location: index.php");
    exit;
}

// Cek apakah jemaat ini sudah pernah memilih di Fase 1 (mencegah double vote)
if ($_SESSION['status_fase1'] == 1) {
    header("Location: index.php");
    exit;
}

$error = "";

// PROSES PENYIMPANAN SUARA
if (isset($_POST['submit_vote_fase1'])) {
    $pilihan_w1 = isset($_POST['kandidat_w1']) ? $_POST['kandidat_w1'] : [];
    $pilihan_w2 = isset($_POST['kandidat_w2']) ? $_POST['kandidat_w2'] : [];

    // Validasi di sisi server: Maksimal 10 per wilayah (Total 20)
    if (count($pilihan_w1) > 10 || count($pilihan_w2) > 10) {
        $error = "Pilihan gagal disimpan! Anda hanya boleh memilih maksimal 10 orang dari masing-masing wilayah.";
    } else {
        $semua_pilihan = array_merge($pilihan_w1, $pilihan_w2);

        if (!empty($semua_pilihan)) {
            foreach ($semua_pilihan as $id_kandidat) {
                $id_k = (int)$id_kandidat;
                $conn->query("UPDATE kandidat_majelis SET suara_fase1 = suara_fase1 + 1 WHERE id_kandidat = $id_k");
            }

            $id_pemilih = $_SESSION['id_pemilih'];
            $conn->query("UPDATE pemilih SET status_fase1 = 1 WHERE id_pemilih = $id_pemilih");
            
            $_SESSION['status_fase1'] = 1;
            header("Location: index.php");
            exit;
        } else {
            $error = "Anda belum memilih satupun kandidat. Silakan pilih nama sebelum klik Submit.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Voting Fase 1 - GKJW Sukolilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .kandidat-label { cursor: pointer; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; display: block; transition: 0.2s;}
        .kandidat-label:hover { background-color: #f8f9fa; }
        .form-check-input:checked + .kandidat-label { background-color: #cff4fc; border-color: #b6effb; font-weight: bold;}
        .sticky-header { position: sticky; top: 0; z-index: 100; background: white; padding: 15px 0; border-bottom: 3px solid #0d6efd;}
    </style>
</head>
<body class="bg-light pb-5">

<div class="container mt-4">
    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold">Surat Suara - Fase 1</h2>
        <p class="text-muted fs-5">Pilih <b>10 Nama</b> dari Wilayah 1 dan <b>10 Nama</b> dari Wilayah 2.<br> <span class="badge bg-warning text-dark mt-2 fs-6">Total Keseluruhan: 20 Orang</span></p>
    </div>

    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST" action="" id="formVoting">
        
        <div class="sticky-header shadow-sm mb-4 rounded px-3">
            <div class="row text-center fw-bold fs-5 align-items-center">
                <div class="col-md-4 text-primary mb-2 mb-md-0">
                    Wilayah 1: <span id="counter-w1" class="badge bg-primary">0</span> / 10
                </div>
                <div class="col-md-4 text-success mb-2 mb-md-0">
                    Wilayah 2: <span id="counter-w2" class="badge bg-success">0</span> / 10
                </div>
                <div class="col-md-4 text-dark border-start border-2">
                    Total: <span id="counter-total" class="badge bg-dark">0</span> / 20
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-primary h-100">
                    <div class="card-header bg-primary text-white fw-bold">Daftar Kandidat Wilayah 1</div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php
                        $q_w1 = $conn->query("SELECT * FROM kandidat_majelis WHERE wilayah = 'Wilayah 1' ORDER BY nama_kandidat ASC");
                        while ($row = $q_w1->fetch_assoc()) {
                            echo "<div class='form-check mb-2'>
                                    <input class='form-check-input chk-w1 d-none' type='checkbox' name='kandidat_w1[]' value='{$row['id_kandidat']}' id='kandidat_{$row['id_kandidat']}'>
                                    <label class='form-check-label kandidat-label' for='kandidat_{$row['id_kandidat']}'>
                                        {$row['nama_kandidat']}
                                    </label>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-success h-100">
                    <div class="card-header bg-success text-white fw-bold">Daftar Kandidat Wilayah 2</div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <?php
                        $q_w2 = $conn->query("SELECT * FROM kandidat_majelis WHERE wilayah = 'Wilayah 2' ORDER BY nama_kandidat ASC");
                        while ($row = $q_w2->fetch_assoc()) {
                            echo "<div class='form-check mb-2'>
                                    <input class='form-check-input chk-w2 d-none' type='checkbox' name='kandidat_w2[]' value='{$row['id_kandidat']}' id='kandidat_{$row['id_kandidat']}'>
                                    <label class='form-check-label kandidat-label' for='kandidat_{$row['id_kandidat']}'>
                                        {$row['nama_kandidat']}
                                    </label>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" id="btnReview" class="btn btn-danger btn-lg px-5 fw-bold shadow">SUBMIT PILIHAN SAYA</button>
            <a href="index.php" class="btn btn-outline-secondary btn-lg ms-2">Batal</a>
        </div>

        <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold" id="modalLabel">🔍 Konfirmasi Pilihan Anda</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning text-center fw-bold mb-4">
                            Apakah Anda yakin dengan pilihan di bawah ini?<br>Suara yang sudah dikirim tidak dapat diubah kembali.
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-primary border-bottom pb-2">Pilihan Wilayah 1 (<span id="modal-jml-w1">0</span>/10)</h6>
                                <ol id="list-review-w1" class="ps-3 text-muted" style="font-size: 0.95rem;"></ol>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-success border-bottom pb-2">Pilihan Wilayah 2 (<span id="modal-jml-w2">0</span>/10)</h6>
                                <ol id="list-review-w2" class="ps-3 text-muted" style="font-size: 0.95rem;"></ol>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cek Kembali</button>
                        <button type="submit" name="submit_vote_fase1" class="btn btn-danger px-5 fw-bold">YA, KIRIM SUARA SAYA</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        const MAKSIMAL_PER_WILAYAH = 10;

        function hitungTotal() {
            let totalW1 = $('.chk-w1:checked').length;
            let totalW2 = $('.chk-w2:checked').length;
            let totalSemua = totalW1 + totalW2;
            $('#counter-total').text(totalSemua);
        }

        // Logika Penguncian Wilayah 1
        $('.chk-w1').change(function() {
            let jumlahW1 = $('.chk-w1:checked').length;
            $('#counter-w1').text(jumlahW1);
            if (jumlahW1 >= MAKSIMAL_PER_WILAYAH) { $('.chk-w1:not(:checked)').prop('disabled', true); } 
            else { $('.chk-w1').prop('disabled', false); }
            hitungTotal();
        });

        // Logika Penguncian Wilayah 2
        $('.chk-w2').change(function() {
            let jumlahW2 = $('.chk-w2:checked').length;
            $('#counter-w2').text(jumlahW2);
            if (jumlahW2 >= MAKSIMAL_PER_WILAYAH) { $('.chk-w2:not(:checked)').prop('disabled', true); } 
            else { $('.chk-w2').prop('disabled', false); }
            hitungTotal();
        });

        // LOGIKA MEMUNCULKAN MODAL KONFIRMASI
        $('#btnReview').click(function() {
            // Bersihkan daftar sebelumnya
            $('#list-review-w1').empty();
            $('#list-review-w2').empty();

            let countW1 = 0;
            let countW2 = 0;

            // Tarik teks nama dari Wilayah 1 yang dicentang
            $('.chk-w1:checked').each(function() {
                let namaKandidat = $(this).next('label').text().trim();
                $('#list-review-w1').append('<li>' + namaKandidat + '</li>');
                countW1++;
            });

            // Tarik teks nama dari Wilayah 2 yang dicentang
            $('.chk-w2:checked').each(function() {
                let namaKandidat = $(this).next('label').text().trim();
                $('#list-review-w2').append('<li>' + namaKandidat + '</li>');
                countW2++;
            });

            // Validasi jika belum milih sama sekali
            if (countW1 === 0 && countW2 === 0) {
                alert("Anda belum memilih satupun kandidat. Silakan pilih minimal 1 nama sebelum Submit.");
                return;
            }

            // Update angka di dalam modal
            $('#modal-jml-w1').text(countW1);
            $('#modal-jml-w2').text(countW2);

            // Tampilkan Modal
            var myModal = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
            myModal.show();
        });
    });
</script>

</body>
</html>