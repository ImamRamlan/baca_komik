<?php
// Mulai session
session_start();

// Menghapus semua data session
session_destroy();

// Redirect ke halaman login atau halaman lain yang sesuai setelah logout
header("Location: login.php");
exit();
?>
