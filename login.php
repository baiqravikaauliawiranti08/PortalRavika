<?php
session_start();
include 'koneksi.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npm = mysqli_real_escape_string($koneksi, $_POST['npm']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username='$npm'");
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['idUser'] = $data['idUser'];
        $_SESSION['username'] = $data['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ NPM atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1d3557, #457b9d);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card-login {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1d3557;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7 col-sm-9">
            <div class="card card-login p-4">
                <div class="text-center mb-3">
                    <div class="logo">🎓 Sistem Akademik</div>
                    <div class="text-muted">Login Mahasiswa</div>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="npm" class="form-label">NPM</label>
                        <input type="text" name="npm" id="npm" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="text-center mt-3 mb-0 text-muted">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>