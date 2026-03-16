<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login, arahkan ke halaman voting yang sesuai fasenya
if (isset($_SESSION['pemilih_logged_in'])) {
    header("Location: index.php"); 
    exit;
}

$error = "";

if (isset($_POST['login_pemilih'])) {
    $id_pemilih = $_POST['id_pemilih'];
    $password = $_POST['password'];

    $id_pemilih = $conn->real_escape_string($id_pemilih);
    $password = $conn->real_escape_string($password);

    // Cek kecocokan data
    $query = "SELECT * FROM pemilih WHERE id_pemilih = '$id_pemilih' AND password_pemilih = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data_pemilih = $result->fetch_assoc();
        
        // Simpan sesi jemaat
        $_SESSION['pemilih_logged_in'] = true;
        $_SESSION['id_pemilih'] = $data_pemilih['id_pemilih'];
        $_SESSION['nama_pemilih'] = $data_pemilih['nama_pemilih'];
        $_SESSION['wilayah_pemilih'] = $data_pemilih['wilayah'];
        $_SESSION['status_fase1'] = $data_pemilih['status_fase1'];
        $_SESSION['status_fase2'] = $data_pemilih['status_fase2'];
        
        header("Location: index.php"); // Nanti kita buat file index.php sebagai pengatur arah rute
        exit;
    } else {
        $error = "Nama atau Password salah!";
    }
}

// Ambil semua data pemilih untuk diisi ke dalam Dropdown
$query_dropdown = "SELECT id_pemilih, nama_pemilih, wilayah FROM pemilih ORDER BY nama_pemilih ASC";
$hasil_dropdown = $conn->query($query_dropdown);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Pemilihan Majelis - GKJW Sukolilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container .select2-selection--single { height: 38px; line-height: 38px; border: 1px solid #ced4da;}
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card p-4 shadow-lg" style="width: 100%; max-width: 450px;">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">E-Voting Majelis</h3>
        <p class="text-muted">GKJW Sukolilo</p>
    </div>

    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Jemaat</label>
            <select name="id_pemilih" class="form-control select2-pencarian" required>
                <option value="">-- Ketik dan Pilih Nama Anda --</option>
                <?php
                if ($hasil_dropdown->num_rows > 0) {
                    while ($baris = $hasil_dropdown->fetch_assoc()) {
                        echo "<option value='{$baris['id_pemilih']}'>{$baris['nama_pemilih']} ({$baris['wilayah']})</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password yang dibagikan" required>
        </div>
        <button type="submit" name="login_pemilih" class="btn btn-primary w-100 fw-bold">Masuk & Mulai Memilih</button>
        <a href="../index.php" class="btn btn-outline-secondary w-100 mt-2">Kembali ke Beranda</a>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.select2-pencarian').select2({
            placeholder: "-- Ketik dan Pilih Nama Anda --",
            allowClear: true
        });
    });
</script>

</body>
</html>