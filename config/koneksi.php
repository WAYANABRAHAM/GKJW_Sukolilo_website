<?php
// Konfigurasi Database
$host     = "localhost";      // Biasanya localhost jika menggunakan XAMPP/Laragon
$username = "root";           // Username default XAMPP/Laragon
$password = "";               // Password default kosong di XAMPP/Laragon
$database = "db_gkjwsukolilo"; // Sesuaikan dengan nama database yang kita buat sebelumnya

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Jika gagal terhubung, tampilkan pesan error dan hentikan eksekusi
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Opsional: Set charset ke UTF-8 agar karakter khusus aman
$conn->set_charset("utf8");

// Catatan: Jangan tambahkan tag penutup "?>" jika file ini hanya berisi PHP 
// untuk menghindari error 'headers already sent' nantinya.