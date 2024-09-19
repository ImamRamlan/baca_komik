<?php
session_start();
include '../koneksi.php';

// Ensure only admins can access this page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$delete_message = "";

// Get the admin ID from the URL
if (isset($_GET['id_admin'])) {
    $id_admin = intval($_GET['id_admin']);

    // Check if the admin exists
    $query = $db->prepare("SELECT * FROM admin WHERE id_admin = ?");
    $query->bind_param("i", $id_admin);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Delete the admin
        $delete_query = $db->prepare("DELETE FROM admin WHERE id_admin = ?");
        $delete_query->bind_param("i", $id_admin);

        if ($delete_query->execute()) {
            $_SESSION['delete_message'] = "Admin berhasil dihapus.";
        } else {
            $_SESSION['delete_message'] = "Terjadi kesalahan saat menghapus admin.";
        }
    } else {
        $_SESSION['delete_message'] = "Admin tidak ditemukan.";
    }
} else {
    $_SESSION['delete_message'] = "ID admin tidak valid.";
}

// Redirect to the admin list page
header("Location: data_admin.php");
exit();
?>
