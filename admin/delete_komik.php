<?php
session_start();

include '../koneksi.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID komik dari URL
if (isset($_GET['id_komik'])) {
    $id_komik = $_GET['id_komik'];

    // Query untuk mendapatkan data komik dari database
    $query = $db->prepare("SELECT * FROM komik WHERE id_komik = ?");
    $query->bind_param("i", $id_komik);
    $query->execute();
    $result = $query->get_result();
    $komik = $result->fetch_assoc();

    if ($komik) {
        // Hapus file gambar sampul jika ada
        if (!empty($komik['url_gambar_sampul']) && file_exists($komik['url_gambar_sampul'])) {
            unlink($komik['url_gambar_sampul']);
        }

        // Query untuk menghapus data komik dari database
        $delete_query = $db->prepare("DELETE FROM komik WHERE id_komik = ?");
        $delete_query->bind_param("i", $id_komik);

        // Eksekusi query
        if ($delete_query->execute()) {
            $_SESSION['success_message'] = "Data komik berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus data komik.";
        }
    } else {
        $_SESSION['error_message'] = "Komik tidak ditemukan.";
    }
} else {
    $_SESSION['error_message'] = "ID komik tidak valid.";
}

// Redirect ke halaman daftar komik setelah proses penghapusan
header("Location: data_komik.php");
exit();
?>
