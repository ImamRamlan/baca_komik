<?php
// Menyertakan file koneksi.php untuk menghubungkan ke database
include '../koneksi.php';

// Memulai sesi
session_start();

// Memeriksa apakah pengguna sudah login atau belum
if (!isset($_SESSION['id_pengguna'])) {
    // Jika belum login, alihkan ke halaman login
    header("Location: login.php");
    exit();
}

// Memeriksa apakah ada data yang dikirimkan melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah id_komik dikirimkan melalui form
    if (isset($_POST['id_komik'])) {
        // Menangkap id_pengguna dari sesi
        $id_pengguna = $_SESSION['id_pengguna'];
        // Menangkap id_komik dari form
        $id_komik = mysqli_real_escape_string($db, $_POST['id_komik']);

        // Query untuk menyimpan bookmark ke database
        $query = "INSERT INTO favorit (id_pengguna, id_komik) VALUES ('$id_pengguna', '$id_komik')";
        // Menjalankan query
        if (mysqli_query($db, $query)) {
            // Jika berhasil disimpan, alihkan ke halaman detail.php atau halaman lain yang sesuai
            header("Location: detail.php?id_komik=" . $id_komik);
            exit();
        } else {
            // Jika terjadi kesalahan saat menyimpan, tampilkan pesan error
            echo "Error: " . $query . "<br>" . mysqli_error($db);
        }
    } else {
        // Jika id_komik tidak dikirimkan melalui form, tampilkan pesan error
        echo "Error: ID Komik tidak ditemukan.";
    }
} else {
    // Jika data tidak dikirimkan melalui metode POST, alihkan ke halaman detail.php atau halaman lain yang sesuai
    header("Location: detail.php");
    exit();
}
?>
