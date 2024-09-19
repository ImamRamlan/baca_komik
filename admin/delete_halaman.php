<?php
session_start();
include '../koneksi.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_halaman'])) {
    $_SESSION['danger_message'] = "ID Halaman tidak disediakan!";
    header("Location: data_halaman.php");
    exit();
}

$id_halaman = $_GET['id_halaman'];

// Ambil data halaman untuk mendapatkan URL file
$query = "SELECT url_gambar_1, url_gambar_2, url_gambar_3, url_gambar_4, url_gambar_5, 
                 url_gambar_6, url_gambar_7, url_gambar_8, url_gambar_9, url_gambar_10 
          FROM halaman WHERE id_halaman = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_halaman);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // Hapus halaman dari database
    $delete_query = "DELETE FROM halaman WHERE id_halaman = ?";
    $stmt_delete = $db->prepare($delete_query);
    $stmt_delete->bind_param("i", $id_halaman);
    if ($stmt_delete->execute()) {
        // Hapus file gambar dari folder
        foreach ($data as $file_path) {
            if (!empty($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $_SESSION['success_message'] = "Halaman berhasil dihapus.";
    } else {
        $_SESSION['danger_message'] = "Terjadi kesalahan saat menghapus halaman.";
    }
} else {
    $_SESSION['danger_message'] = "Data halaman tidak ditemukan.";
}

header("Location: data_halaman.php");
exit();
?>
