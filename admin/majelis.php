<?php
require_once '../config/koneksi.php';
include '../includes/admin_header.php';

$pesan = "";

// 1. Proses Ubah Fase Pemilihan
if (isset($_POST['update_fase'])) {
    $fase_baru = $_POST['status_fase'];
    $conn->query("UPDATE konten_web SET isi_konten = '$fase_baru' WHERE kategori = 'fase_pemilihan'");
    $pesan = "<div class='alert alert-success'>Status Pemilihan berhasil diubah menjadi: <b>" . strtoupper($fase_baru) . "</b></div>";
}

// 2. Proses Tambah Kandidat Baru
if (isset($_POST['tambah_kandidat'])) {
    $nama = $conn->real_escape_string($_POST['nama_kandidat']);
    $wilayah = $_POST['wilayah'];
    $conn->query("INSERT INTO kandidat_majelis (nama_kandidat, wilayah) VALUES ('$nama', '$wilayah')");
    $pesan = "<div class='alert alert-success'>Kandidat <b>$nama</b> berhasil ditambahkan ke $wilayah!</div>";
}

// 3. Proses Loloskan / Batal Lolos ke Fase 2
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_k = (int)$_GET['id'];
    if ($_GET['aksi'] == 'lolos') {
        $conn->query("UPDATE kandidat_majelis SET lolos_fase2 = 1 WHERE id_kandidat = $id_k");
    } elseif ($_GET['aksi'] == 'batal') {
        $conn->query("UPDATE kandidat_majelis SET lolos_fase2 = 0 WHERE id_kandidat = $id_k");
    }
    header("Location: majelis.php?tab=fase1"); // Refresh halaman kembali ke tab fase 1
    exit;
}

// 4. Proses Upload Foto Kandidat (Untuk Fase 2)
if (isset($_POST['upload_foto'])) {
    $id_kandidat = $_POST['id_kandidat'];
    $nama_file = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];
    
    // Buat nama unik agar file tidak bentrok
    $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
    $nama_baru = "kandidat_" . $id_kandidat . "_" . time() . "." . $ext;
    $path = "../assets/foto_kandidat/" . $nama_baru;

    if (move_uploaded_file($tmp_file, $path)) {
        $conn->query("UPDATE kandidat_majelis SET foto = '$nama_baru' WHERE id_kandidat = $id_kandidat");
        $pesan = "<div class='alert alert-success'>Foto berhasil diunggah!</div>";
    }
}

// Ambil status fase saat ini
$fase_aktif = $conn->query("SELECT isi_konten FROM konten_web WHERE kategori = 'fase_pemilihan'")->fetch_assoc()['isi_konten'];

// Menentukan tab mana yang sedang aktif
$tab_aktif = isset($_GET['tab']) ? $_GET['tab'] : 'pengaturan';
?>

<h3>Manajemen E-Voting Majelis</h3>
<hr>
<?php echo $pesan; ?>

<ul class="nav nav-tabs mb-4" id="majelisTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link <?php echo ($tab_aktif == 'pengaturan') ? 'active' : ''; ?>" href="majelis.php?tab=pengaturan">⚙️ Pengaturan & Input</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($tab_aktif == 'fase1') ? 'active' : ''; ?>" href="majelis.php?tab=fase1">📊 Hasil Fase 1</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($tab_aktif == 'fase2') ? 'active' : ''; ?>" href="majelis.php?tab=fase2">📸 Hasil & Foto Fase 2</a>
  </li>
</ul>

<div class="tab-content">
  
    <?php if ($tab_aktif == 'pengaturan'): ?>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white fw-bold">Kontrol Fase Pemilihan</div>
                <div class="card-body">
                    <form method="POST" action="majelis.php?tab=pengaturan">
                        <label class="form-label">Status Saat Ini: <b><?php echo strtoupper($fase_aktif); ?></b></label>
                        <select name="status_fase" class="form-select mb-3">
                            <option value="tutup" <?php if($fase_aktif == 'tutup') echo 'selected'; ?>>⛔ Tutup Pemilihan</option>
                            <option value="fase1" <?php if($fase_aktif == 'fase1') echo 'selected'; ?>>1️⃣ Buka Fase 1</option>
                            <option value="fase2" <?php if($fase_aktif == 'fase2') echo 'selected'; ?>>2️⃣ Buka Fase 2</option>
                        </select>
                        <button type="submit" name="update_fase" class="btn btn-danger w-100">Ubah Status</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white fw-bold">Tambah Kandidat Majelis</div>
                <div class="card-body">
                    <form method="POST" action="majelis.php?tab=pengaturan">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_kandidat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Wilayah</label>
                            <select name="wilayah" class="form-select" required>
                                <option value="Wilayah 1">Wilayah 1</option>
                                <option value="Wilayah 2">Wilayah 2</option>
                            </select>
                        </div>
                        <button type="submit" name="tambah_kandidat" class="btn btn-primary w-100">Tambahkan Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($tab_aktif == 'fase1'): ?>
    <div class="alert alert-info shadow-sm">
        Halaman ini menampilkan perolehan suara Fase 1 secara <i>real-time</i> (Diurutkan dari suara tertinggi). 
        <b>Tugas Admin:</b> Klik tombol "Loloskan" pada nama yang bersedia maju ke Fase 2. Jika ada yang mundur, lewati nama tersebut dan pilih peringkat di bawahnya.
    </div>
    <div class="row">
        <?php 
        $wilayahs = ['Wilayah 1', 'Wilayah 2'];
        foreach ($wilayahs as $wil):
            // Hitung berapa orang yang sudah diloloskan admin di wilayah ini
            $q_hitung = $conn->query("SELECT COUNT(*) as total_lolos FROM kandidat_majelis WHERE wilayah = '$wil' AND lolos_fase2 = 1");
            $jml_lolos = $q_hitung->fetch_assoc()['total_lolos'];
            
            // Beri warna peringatan jika jumlah yang diloloskan belum pas 24
            $badge_color = ($jml_lolos == 24) ? 'bg-success' : (($jml_lolos > 24) ? 'bg-danger' : 'bg-warning text-dark');
        ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Klasemen: <?php echo $wil; ?></span>
                    <span class="badge <?php echo $badge_color; ?> fs-6">Diloloskan: <?php echo $jml_lolos; ?> / 24</span>
                </div>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>No</th>
                                <th>Nama Kandidat</th>
                                <th class="text-center">Suara</th>
                                <th class="text-center">Aksi (Ke Fase 2)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_fase1 = $conn->query("SELECT * FROM kandidat_majelis WHERE wilayah = '$wil' ORDER BY suara_fase1 DESC, nama_kandidat ASC");
                            $no = 1;
                            while ($row = $q_fase1->fetch_assoc()):
                                // Highlight baris jika kandidat sudah diloloskan
                                $row_class = ($row['lolos_fase2'] == 1) ? 'table-success' : '';
                            ?>
                            <tr class="<?php echo $row_class; ?>">
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_kandidat']; ?></td>
                                <td class="text-center"><b class="fs-5"><?php echo $row['suara_fase1']; ?></b></td>
                                <td class="text-center">
                                    <?php if ($row['lolos_fase2'] == 1): ?>
                                        <span class="badge bg-success mb-1">Terpilih</span><br>
                                        <a href="majelis.php?tab=fase1&aksi=batal&id=<?php echo $row['id_kandidat']; ?>" class="btn btn-sm btn-outline-danger" style="font-size: 0.7rem;">Batalkan</a>
                                    <?php else: ?>
                                        <a href="majelis.php?tab=fase1&aksi=lolos&id=<?php echo $row['id_kandidat']; ?>" class="btn btn-sm btn-primary">Loloskan</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($tab_aktif == 'fase2'): ?>
    <div class="alert alert-warning">
        Daftar ini hanya memuat kandidat yang diloloskan admin dari Fase 1. Anda bisa mengunggah foto wajah mereka di sini sebelum membuka Fase 2 untuk jemaat. <br>
        <i>Di bawah ini juga akan terlihat perolehan akhir suara pada Fase 2.</i>
    </div>
    <div class="row">
        <?php 
        // Mengambil kandidat yang HANYA lolos fase 2
        $q_fase2 = $conn->query("SELECT * FROM kandidat_majelis WHERE lolos_fase2 = 1 ORDER BY wilayah ASC, suara_fase2 DESC");
        while ($row = $q_fase2->fetch_assoc()):
            // Jika foto kosong atau default
            $foto = ($row['foto'] == 'kandidat_kosong.png' || empty($row['foto'])) ? 'https://via.placeholder.com/150?text=No+Photo' : '../assets/foto_kandidat/' . $row['foto'];
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm text-center">
                <img src="<?php echo $foto; ?>" class="card-img-top mx-auto mt-3 rounded" style="width: 120px; height: 120px; object-fit: cover;" alt="Foto">
                <div class="card-body p-2 flex-grow-1 d-flex flex-column">
                    <h6 class="card-title fw-bold mb-1" style="font-size: 0.9rem;"><?php echo $row['nama_kandidat']; ?></h6>
                    <div><span class="badge bg-secondary mb-2"><?php echo $row['wilayah']; ?></span></div>
                    <div class="mt-auto">
                        <h5 class="text-success mb-3">Suara: <?php echo $row['suara_fase2']; ?></h5>
                        
                        <form method="POST" action="majelis.php?tab=fase2" enctype="multipart/form-data">
                            <input type="hidden" name="id_kandidat" value="<?php echo $row['id_kandidat']; ?>">
                            <input type="file" name="foto" class="form-control form-control-sm mb-1" accept="image/*" required>
                            <button type="submit" name="upload_foto" class="btn btn-sm btn-outline-dark w-100" style="font-size: 0.8rem;">Upload Foto</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

</div>

<?php include '../includes/admin_footer.php'; ?>