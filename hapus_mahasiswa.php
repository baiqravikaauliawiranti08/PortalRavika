<?php
include 'koneksi.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMhs = intval($_GET['id']);

    // Cek dulu apakah data mahasiswa ada
    $cek = mysqli_query($koneksi, "SELECT u.idUser, f.foto 
        FROM tbl_user u
        LEFT JOIN tbl_foto f ON u.idUser = f.idUser
        WHERE u.idMhs = $idMhs");

    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        $idUser = $data['idUser'];
        $foto = $data['foto'];

        // Hapus foto dari folder jika ada
        if ($foto && file_exists("uploads/$foto")) {
            unlink("uploads/$foto");
        }

        // Hapus dari tbl_foto
        mysqli_query($koneksi, "DELETE FROM tbl_foto WHERE idUser = $idUser");

        // Hapus dari tbl_user
        mysqli_query($koneksi, "DELETE FROM tbl_user WHERE idMhs = $idMhs");
    }

    // Hapus dari tbl_mahasiswa
    mysqli_query($koneksi, "DELETE FROM tbl_mahasiswa WHERE idMhs = $idMhs");

    // Jika ingin pakai AJAX, bisa balas dengan JSON:
    // echo json_encode(["status" => "success"]);
    // exit;

    header("Location: index.php");
    exit;
} else {
    // Untuk error handling jika pakai AJAX
    // http_response_code(400);
    echo "❌ ID tidak valid atau tidak ditemukan.";
}
