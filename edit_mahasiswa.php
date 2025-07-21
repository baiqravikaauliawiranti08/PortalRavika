<?php
session_start();
include 'koneksi.php';

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$idMhs = (int) $_GET['id'];

// Ambil data mahasiswa + user + foto
$query = mysqli_query($koneksi, "SELECT m.*, u.idUser, f.foto 
    FROM tbl_mahasiswa m
    LEFT JOIN tbl_user u ON m.idMhs = u.idMhs
    LEFT JOIN tbl_foto f ON u.idUser = f.idUser
    WHERE m.idMhs = $idMhs");

$data = mysqli_fetch_assoc($query);
if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim     = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);

    // Update data mahasiswa
    mysqli_query($koneksi, "UPDATE tbl_mahasiswa SET nim='$nim', nama='$nama', jurusan='$jurusan' WHERE idMhs=$idMhs");

    // Update username di tbl_user
    mysqli_query($koneksi, "UPDATE tbl_user SET username='$nim' WHERE idMhs=$idMhs");

    // Upload foto baru jika ada
    if (!empty($_FILES['foto']['name'])) {
        $foto     = $_FILES['foto']['name'];
        $tmp      = $_FILES['foto']['tmp_name'];
        $folder   = "uploads/";
        $ext      = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
        $namaBaru = uniqid('foto_') . '.' . $ext;

        if (!in_array($ext, $allowed)) {
            $error = "Format file tidak didukung.";
        } elseif (!move_uploaded_file($tmp, $folder . $namaBaru)) {
            $error = "Gagal upload foto.";
        } else {
            $idUser = $data['idUser'];
            $cekFoto = mysqli_query($koneksi, "SELECT * FROM tbl_foto WHERE idUser = $idUser");

            if (mysqli_num_rows($cekFoto) > 0) {
                mysqli_query($koneksi, "UPDATE tbl_foto SET foto='$namaBaru' WHERE idUser=$idUser");
            } else {
                mysqli_query($koneksi, "INSERT INTO tbl_foto (idUser, foto) VALUES ($idUser, '$namaBaru')");
            }
        }
    }

    if (!$error) {
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mahasiswa - Mahasiswa Card UI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f2f5f9;
        }
        .card {
            border: none;
            border-radius: 16px;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ffc107;
        }
        @media (max-width: 576px) {
            .profile-img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow p-4">
                <h4 class="text-center text-warning fw-bold mb-4">Edit Data Mahasiswa</h4>

                <div class="text-center mb-3">
                    <?php if (!empty($data['foto'])): ?>
                        <img src="uploads/<?= $data['foto'] ?>" class="profile-img mb-2" alt="Foto Mahasiswa">
                    <?php else: ?>
                        <div class="text-muted">(Belum ada foto)</div>
                    <?php endif; ?>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success text-center"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($data['nim']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($data['jurusan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Baru (Opsional)</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
