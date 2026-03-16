<?php
// includes/admin_header.php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    // Jika belum login, tendang kembali ke halaman login
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - GKJW Sukolilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Admin GKJW</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="youtube.php">Update YouTube</a></li>
                <li class="nav-item"><a class="nav-link" href="renungan.php">Renungan</a></li>
                <li class="nav-item"><a class="nav-link" href="warta.php">Warta Jemaat</a></li>
                <li class="nav-item"><a class="nav-link" href="majelis.php">Pemilihan Majelis</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">