<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

$idUser = intval($_SESSION['idUser']);
$success = "";
$error = "";

// Ambil foto lama (jika ada)
$fotoSekarang = "";
$get = mysqli_query($koneksi, "SELECT * FROM tbl_foto WHERE idUser = $idUser");
if ($row = mysqli_fetch_assoc($get)) {
    $fotoSekarang = $row['foto'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['foto'])) {
    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $newName = 'user_' . $idUser . '_' . time() . '.' . $ext;
        $folder  = 'uploads/' . $newName;

        if (move_uploaded_file($tmp, $folder)) {
            if ($fotoSekarang && file_exists('uploads/' . $fotoSekarang)) {
                unlink('uploads/' . $fotoSekarang);
            }

            if ($fotoSekarang) {
                mysqli_query($koneksi, "UPDATE tbl_foto SET foto='$newName' WHERE idUser=$idUser");
            } else {
                mysqli_query($koneksi, "INSERT INTO tbl_foto (idUser, foto) VALUES ($idUser, '$newName')");
            }

            $success = "✅ Foto berhasil diunggah.";
            $fotoSekarang = $newName;
        } else {
            $error = "❌ Gagal upload file.";
        }
    } else {
        $error = "❌ Format file tidak valid (hanya jpg, jpeg, png, gif).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Foto Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .profile-card {
            border-radius: 16px;
            padding: 2rem;
            background-color: #fff;
        }
        .profile-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media (max-width: 576px) {
            .profile-img {
                width: 85px;
                height: 85px;
            }
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="profile-card shadow">
                <h4 class="text-center text-primary fw-bold mb-3">Upload Foto Mahasiswa</h4>

                <?php if ($success): ?>
                    <div class="alert alert-success text-center"><?= $success ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($fotoSekarang): ?>
                    <div class="text-center mb-4">
                        <img src="uploads/<?= $fotoSekarang ?>" class="profile-img" alt="Foto Sekarang">
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto Baru</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
