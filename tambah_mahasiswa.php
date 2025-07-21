<?php
session_start();
include 'koneksi.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim     = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);

    $cek = mysqli_query($koneksi, "SELECT * FROM tbl_mahasiswa WHERE nim='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "❌ NIM sudah terdaftar!";
    } else {
        $foto     = $_FILES['foto']['name'];
        $tmp      = $_FILES['foto']['tmp_name'];
        $folder   = "uploads/";
        $ext      = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
        $namaBaru = uniqid("foto_") . '.' . $ext;

        if (!in_array($ext, $allowed)) {
            $error = "❌ Format foto tidak valid! (jpg, jpeg, png, gif)";
        } elseif (!move_uploaded_file($tmp, $folder . $namaBaru)) {
            $error = "❌ Gagal mengunggah foto.";
        } else {
            $insertMhs = mysqli_query($koneksi, "INSERT INTO tbl_mahasiswa (nim, nama, jurusan) VALUES ('$nim', '$nama', '$jurusan')");
            if ($insertMhs) {
                $idMhs = mysqli_insert_id($koneksi);
                $password = password_hash($nim, PASSWORD_DEFAULT);

                $insertUser = mysqli_query($koneksi, "INSERT INTO tbl_user (username, password, idMhs) VALUES ('$nim', '$password', '$idMhs')");
                if ($insertUser) {
                    $idUser = mysqli_insert_id($koneksi);
                    mysqli_query($koneksi, "INSERT INTO tbl_foto (idUser, foto) VALUES ('$idUser', '$namaBaru')");
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "❌ Gagal menyimpan data user.";
                }
            } else {
                $error = "❌ Gagal menyimpan data mahasiswa.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #457b9d, #1d3557);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card-mahasiswa {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .form-label {
            font-weight: 500;
        }
        .card-header h5 {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
            <div class="card card-mahasiswa p-3">
                <div class="card-header bg-primary text-white text-center rounded">
                    <h5>📘 Tambah Data Mahasiswa</h5>
                    <small class="d-block">Form pengisian biodata dan akun login</small>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-muted small">
                    Sistem Akademik Mahasiswa • <?= date('Y') ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
