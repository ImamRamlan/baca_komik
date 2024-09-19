<?php
session_start();
$title = "Detail Komik | Baca Komik";
include '../koneksi.php';

// Ensure only logged in users can access this page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'header.php';

// Get the comic id from the URL
$id_komik = isset($_GET['id_komik']) ? intval($_GET['id_komik']) : 0;

if ($id_komik <= 0) {
    echo "<div class='alert alert-danger'>ID Komik tidak valid.</div>";
    exit();
}

// Pemanggilan berdasarkan id komik
$query = $db->prepare("SELECT * FROM komik WHERE id_komik = ?");
$query->bind_param("i", $id_komik);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Komik tidak ditemukan.</div>";
    include 'footer.php';
    exit();
}

$komik = $result->fetch_assoc();
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Detail Komik</h2>
            <table class="table table-bordered">
                <tr>
                    <th>Judul</th>
                    <td><?php echo htmlspecialchars($komik['judul']); ?></td>
                </tr>
                <tr>
                    <th>Penulis</th>
                    <td><?php echo htmlspecialchars($komik['penulis']); ?></td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td><?php echo nl2br(htmlspecialchars($komik['deskripsi'])); ?></td>
                </tr>
                <tr>
                    <th>Genre</th>
                    <td><?php echo htmlspecialchars($komik['genre']); ?></td>
                </tr>
                <tr>
                    <th>Gambar Sampul</th>
                    <td>
                        <img src="<?php echo htmlspecialchars($komik['url_gambar_sampul']); ?>" alt="Gambar Sampul" class="img-fluid">
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Diterbitkan</th>
                    <td><?php echo htmlspecialchars($komik['diterbitkan_pada']); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo htmlspecialchars($komik['status']); ?></td>
                </tr>
            </table>
            <a href="data_komik.php" class="btn btn-secondary">Kembali ke Daftar Komik</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
