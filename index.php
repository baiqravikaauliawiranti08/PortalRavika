<?php
session_start();
include 'koneksi.php';

// ✅ Cek apakah sudah login
if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

$where = "";
if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    $where = "WHERE m.nim LIKE '%$keyword%' OR m.nama LIKE '%$keyword%'";
}

$query = mysqli_query($koneksi, "SELECT m.*, u.idUser, f.foto 
    FROM tbl_mahasiswa m
    LEFT JOIN tbl_user u ON m.idMhs = u.idMhs
    LEFT JOIN tbl_foto f ON u.idUser = f.idUser
    $where
    ORDER BY m.idMhs DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #fce4ec);
        }
        .card-custom {
            background: linear-gradient(135deg, #ffccbc, #d1c4e9);
            color: #2c3e50;
            border-radius: 1rem;
        }
        .card-custom h5 {
            font-weight: bold;
        }
        .foto-profil {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }
        @media (max-width: 576px) {
            .foto-profil {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🎨 Mahasiswa Card</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <?php if (isset($_SESSION['idUser'])): ?>
                <a href="logout.php" class="btn btn-outline-light ms-2">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light ms-2">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mb-3">
        <h4 class="mb-0">Daftar Mahasiswa</h4>
        <a href="tambah_mahasiswa.php" class="btn btn-success">+ Tambah Mahasiswa</a>
    </div>

    <!-- Form Pencarian -->
    <form method="GET" class="d-flex flex-column flex-sm-row gap-2 mb-4" role="search">
        <input type="text" name="keyword" class="form-control" placeholder="Cari NIM / Nama..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
        <button class="btn btn-outline-primary" type="submit">Cari</button>
    </form>

    <div class="row">
        <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card card-custom shadow h-100">
                <div class="card-body text-center">
                    <?php if (!empty($row['foto'])): ?>
                        <img src="uploads/<?= $row['foto'] ?>" alt="Foto" class="foto-profil mb-3">
                    <?php else: ?>
                        <div class="text-muted mb-3">(Belum ada foto)</div>
                    <?php endif; ?>
                    <h5><?= htmlspecialchars($row['nama']) ?></h5>
                    <p class="mb-1"><strong>NIM:</strong> <?= htmlspecialchars($row['nim']) ?></p>
                    <p class="mb-3"><strong>Jurusan:</strong> <?= htmlspecialchars($row['jurusan']) ?></p>
                    <div class="d-grid gap-2">
                        <a href="edit_mahasiswa.php?id=<?= $row['idMhs'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus_mahasiswa.php?id=<?= $row['idMhs'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
