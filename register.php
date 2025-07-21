<?php
session_start();
include 'koneksi.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $password = $_POST['password'];

    // Cek apakah username sudah digunakan
    $cek = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "❌ NIM sudah digunakan!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $simpan = mysqli_query($koneksi, "INSERT INTO tbl_user (username, password) VALUES ('$nim', '$hash')");

        if ($simpan) {
            header("Location: login.php");
            exit;
        } else {
            $error = "❌ Gagal menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #457b9d, #1d3557);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card-register {
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        .header-title {
            font-size: 1.4rem;
            font-weight: bold;
        }
        .logo {
            color: #ffffff;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-9">
            <div class="card card-register p-4">
                <div class="text-center mb-3">
                    <div class="header-title text-primary">🎓 Sistem Akademik</div>
                    <div class="text-muted">Form Registrasi Mahasiswa</div>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">NPM</label>
                        <input type="text" name="nim" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>
                <p class="text-center mt-3 mb-0 text-muted">
                    Sudah punya akun? <a href="login.php">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
