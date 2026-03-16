<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi Keamanan: Pastikan sudah login
if (!isset($_SESSION['pemilih_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah admin benar-benar membuka Fase 2
$q_fase = $conn->query("SELECT isi_konten FROM konten_web WHERE kategori = 'fase_pemilihan'");
$fase_aktif = $q_fase->fetch_assoc()['isi_konten'];
if ($fase_aktif != 'fase2') {
    header("Location: index.php");
    exit;
}

// Cek apakah jemaat ini sudah pernah memilih di Fase 2 (mencegah double vote)
if ($_SESSION['status_fase2'] == 1) {
    header("Location: index.php");
    exit;
}

$error = "";

// PROSES PENYIMPANAN SUARA FASE 2
if (isset($_POST['submit_vote_fase2'])) {
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
                // Update jumlah suara pada kolom suara_fase2
                $conn->query("UPDATE kandidat_majelis SET suara_fase2 = suara_fase2 + 1 WHERE id_kandidat = $id_k");
            }

            // Kunci status jemaat untuk Fase 2
            $id_pemilih = $_SESSION['id_pemilih'];
            $conn->query("UPDATE pemilih SET status_fase2 = 1 WHERE id_pemilih = $id_pemilih");
            
            $_SESSION['status_fase2'] = 1;

            header("Location: index.php");
            exit;
        } else {
            $error = "Anda belum memilih satupun kandidat. Silakan pilih minimal 1 nama.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Voting Fase 2 - GKJW Sukolilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Desain Kartu Kandidat & Efek Hover */
        .kandidat-card { cursor: pointer; transition: all 0.2s ease-in-out; border: 2px solid #dee2e6; border-radius: 8px; overflow: hidden; height: 100%;}
        .kandidat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .foto-kandidat { width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid #eee; }
        
        /* Highlight Kartu saat dicentang (W1 - Biru) */
        .form-check-input:checked + .kandidat-card-w1 { border-color: #0d6efd; background-color: #e7f1ff; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
        .form-check-input:checked + .kandidat-card-w1 .card-title { color: #0d6efd; font-weight: bold; }

        /* Highlight Kartu saat dicentang (W2 - Hijau) */
        .form-check-input:checked + .kandidat-card-w2 { border-color: #198754; background-color: #e8f5e9; box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2); }
        .form-check-input:checked + .kandidat-card-w2 .card-title { color: #198754; font-weight: bold; }

        .sticky-header { position: sticky; top: 0; z-index: 100; background: white; padding: 15px 0; border-bottom: 3px solid #198754;}
        label { width: 100%; height: 100%; margin: 0; padding: 0; }
    </style>
</head>
<body class="bg-light pb-5">

<div class="container mt-4">
    <div class="text-center mb-4">
        <h2 class="text-success fw-bold">Surat Suara - Fase 2 (Final)</h2>
        <p class="text-muted fs-5">Kenali wajah kandidat dan pilih <b>Maksimal 10 Nama</b> dari tiap wilayah.<br> <span class="badge bg-warning text-dark mt-2 fs-6">Total Keseluruhan: 20 Orang</span></p>
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

        <div class="card shadow-sm border-primary mb-5">
            <div class="card-header bg-primary text-white fw-bold fs-5">Kandidat Wilayah 1 (Lolos Fase 1)</div>
            <div class="card-body bg-light">
                <div class="row">
                    <?php
                    // Ambil HANYA yang lolos Fase 2 dari W1
                    $q_w1 = $conn->query("SELECT * FROM kandidat_majelis WHERE wilayah = 'Wilayah 1' AND lolos_fase2 = 1 ORDER BY nama_kandidat ASC");
                    while ($row = $q_w1->fetch_assoc()) {
                        $foto = ($row['foto'] == 'kandidat_kosong.png' || empty($row['foto'])) ? 'https://via.placeholder.com/200x250?text=Tanpa+Foto' : '../assets/foto_kandidat/' . $row['foto'];
                        
                        echo "<div class='col-6 col-md-4 col-lg-3 col-xl-2 mb-4'>
                                <label>
                                    <input class='form-check-input chk-w1 d-none' type='checkbox' name='kandidat_w1[]' value='{$row['id_kandidat']}'>
                                    <div class='card kandidat-card kandidat-card-w1 text-center bg-white'>
                                        <img src='{$foto}' class='foto-kandidat' alt='Foto {$row['nama_kandidat']}'>
                                        <div class='card-body p-2 d-flex align-items-center justify-content-center'>
                                            <h6 class='card-title mb-0' style='font-size:0.9rem;'>{$row['nama_kandidat']}</h6>
                                        </div>
                                    </div>
                                </label>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-success mb-4">
            <div class="card-header bg-success text-white fw-bold fs-5">Kandidat Wilayah 2 (Lolos Fase 1)</div>
            <div class="card-body bg-light">
                <div class="row">
                    <?php
                    // Ambil HANYA yang lolos Fase 2 dari W2
                    $q_w2 = $conn->query("SELECT * FROM kandidat_majelis WHERE wilayah = 'Wilayah 2' AND lolos_fase2 = 1 ORDER BY nama_kandidat ASC");
                    while ($row = $q_w2->fetch_assoc()) {
                        $foto = ($row['foto'] == 'kandidat_kosong.png' || empty($row['foto'])) ? 'https://via.placeholder.com/200x250?text=Tanpa+Foto' : '../assets/foto_kandidat/' . $row['foto'];
                        
                        echo "<div class='col-6 col-md-4 col-lg-3 col-xl-2 mb-4'>
                                <label>
                                    <input class='form-check-input chk-w2 d-none' type='checkbox' name='kandidat_w2[]' value='{$row['id_kandidat']}'>
                                    <div class='card kandidat-card kandidat-card-w2 text-center bg-white'>
                                        <img src='{$foto}' class='foto-kandidat' alt='Foto {$row['nama_kandidat']}'>
                                        <div class='card-body p-2 d-flex align-items-center justify-content-center'>
                                            <h6 class='card-title mb-0' style='font-size:0.9rem;'>{$row['nama_kandidat']}</h6>
                                        </div>
                                    </div>
                                </label>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" id="btnReviewFase2" class="btn btn-success btn-lg px-5 fw-bold shadow">SUBMIT PILIHAN FASE 2</button>
            <a href="index.php" class="btn btn-outline-secondary btn-lg ms-2">Batal</a>
        </div>

        <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalLabel">🔍 Konfirmasi Pilihan Final Anda</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning text-center fw-bold mb-4">
                            Apakah Anda yakin dengan pilihan Final Majelis di bawah ini?<br>Suara yang sudah dikirim tidak dapat diubah kembali.
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
                        <button type="submit" name="submit_vote_fase2" class="btn btn-success px-5 fw-bold">YA, KIRIM SUARA FINAL SAYA</button>
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

        // LOGIKA MEMUNCULKAN MODAL KONFIRMASI FASE 2
        $('#btnReviewFase2').click(function() {
            // Bersihkan daftar sebelumnya
            $('#list-review-w1').empty();
            $('#list-review-w2').empty();

            let countW1 = 0;
            let countW2 = 0;

            // Tarik teks nama dari Wilayah 1 (Mencari teks di dalam div .card-title)
            $('.chk-w1:checked').each(function() {
                let namaKandidat = $(this).siblings('.card').find('.card-title').text().trim();
                $('#list-review-w1').append('<li>' + namaKandidat + '</li>');
                countW1++;
            });

            // Tarik teks nama dari Wilayah 2
            $('.chk-w2:checked').each(function() {
                let namaKandidat = $(this).siblings('.card').find('.card-title').text().trim();
                $('#list-review-w2').append('<li>' + namaKandidat + '</li>');
                countW2++;
            });

            // Validasi jika belum milih sama sekali
            if (countW1 === 0 && countW2 === 0) {
                alert("Anda belum memilih satupun kandidat. Silakan klik foto minimal 1 orang sebelum Submit.");
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