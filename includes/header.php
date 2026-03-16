<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GKJW Sukolilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; } /* Memberi jarak agar konten tidak tertutup navbar */
        
        /* CSS Tambahan untuk Dropdown Bertingkat (Nested Dropdown) */
        .dropdown-menu li { position: relative; }
        .dropdown-menu .dropdown-submenu { display: none; position: absolute; left: 100%; top: -7px; margin-top: 0; border-radius: 0.375rem; }
        /* Memunculkan submenu saat menu induknya disorot (hover) */
        .dropdown-menu > li:hover > .dropdown-submenu { display: block; }
        
        /* Perbaikan untuk tampilan di HP agar submenu turun ke bawah, bukan ke samping */
        @media (max-width: 991px) {
            .dropdown-menu .dropdown-submenu { position: static; display: none; padding-left: 1rem; border: none; box-shadow: none; }
            .dropdown-menu > li:hover > .dropdown-submenu { display: block; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">GKJW Sukolilo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="sejarah.php">Sejarah</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Informasi</a>
                    <ul class="dropdown-menu shadow">
                        <li><a class="dropdown-item py-2" href="https://maps.app.goo.gl/tiLDSWQDYtqJcWVp8" target="_blank">📍 Lokasi (Google Maps)</a></li>
                        
                        <li><a class="dropdown-item py-2" href="mailto:gkjwsukolilo@yahoo.co.id">📧 gkjwsukolilo@yahoo.co.id</a></li>
                        
                        <li><a class="dropdown-item py-2" href="tel:+6281249827510">📞 (031) 5915811 / 081249827510</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li class="dropend">
                            <a class="dropdown-item dropdown-toggle py-2" href="#">📱 Media Sosial</a>
                            <ul class="dropdown-menu dropdown-submenu shadow-sm">
                                <li><a class="dropdown-item" href="https://www.youtube.com/@gkjwjemaatsukolilo1603" target="_blank">YouTube</a></li>
                                <li><a class="dropdown-item" href="https://www.instagram.com/gkjwsukolilo?igsh=ZmZyY2h1NGoyaXdy" target="_blank">Instagram</a></li>
                                <li><a class="dropdown-item" href="https://www.facebook.com/profile.php?id=100071765695862" target="_blank">Facebook</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Renungan</a>
                    <ul class="dropdown-menu shadow">
                        <li><a class="dropdown-item" href="renungan.php?waktu=pagi">Pagi</a></li>
                        <li><a class="dropdown-item" href="renungan.php?waktu=malam">Malam</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="warta.php">Warta Jemaat</a></li>
                <li class="nav-item"><a class="nav-link text-warning fw-bold" href="pemilihan/login.php">E-Voting Majelis</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="btn btn-outline-light btn-sm mt-1" href="admin/login.php">Login Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">