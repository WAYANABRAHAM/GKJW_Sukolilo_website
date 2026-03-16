<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mencegah SQL Injection sederhana
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Cek database (sesuai data dummy: admin_gkjw / admin123)
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary d-flex align-items-center justify-content-center vh-100">

<div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-4">Login Admin</h3>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        <a href="../index.php" class="btn btn-link w-100 mt-2">Kembali ke Web Pengunjung</a>
    </form>
</div>

</body>
</html>