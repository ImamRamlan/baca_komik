<?php
session_start();

include '../koneksi.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah parameter id_pengguna ada dalam URL
if (!isset($_GET['id_pengguna']) || empty($_GET['id_pengguna'])) {
    // Jika tidak, arahkan kembali ke halaman data_pengguna.php
    header("Location: login.php");
    exit();
}

$id_pengguna = $_GET['id_pengguna'];

// Hapus data pengguna dari database
$query = "DELETE FROM pengguna WHERE id_pengguna = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_pengguna);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    // Jika penghapusan berhasil, atur pesan sukses dan arahkan ke halaman data_pengguna.php
    $_SESSION['success_message'] = "Data pengguna berhasil dihapus.";
    header("Location: data_pengguna.php");
    exit();
} else {
    // Jika penghapusan gagal, atur pesan bahaya dan arahkan kembali ke halaman data_pengguna.php
    $_SESSION['danger_message'] = "Gagal menghapus data pengguna.";
    header("Location: data_pengguna.php");
    exit();
}
