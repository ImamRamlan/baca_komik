<?php
session_start();

include '../koneksi.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id_halaman'])) {
    $id_halaman = $_GET['id_halaman'];

    // Query untuk mendapatkan detail halaman dan judul komik
    $query = "SELECT halaman.*, komik.judul FROM halaman LEFT JOIN komik ON halaman.id_komik = komik.id_komik WHERE halaman.id_halaman = $id_halaman";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "Data tidak ditemukan!";
        exit();
    }
} else {
    echo "ID Halaman tidak disediakan!";
    exit();
}

include 'header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Halaman</h1>
    </div>
    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Halaman Komik</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Judul Komik</th>
                    <td><?php echo htmlspecialchars($data['judul']); ?></td>
                </tr>
                <tr>
                    <th>Nomor Halaman</th>
                    <td><?php echo htmlspecialchars($data['nomor_halaman']); ?></td>
                </tr>
                <?php for ($i = 1; $i <= 10; $i++) { ?>
                    <?php if (!empty($data['url_gambar_' . $i])) { ?>
                        <tr>
                            <th>Gambar Halaman <?php echo $i; ?></th>
                            <td><img src="<?php echo htmlspecialchars($data['url_gambar_' . $i]); ?>" alt="" class="img-fluid" style="max-width: 200px; max-height: 200px;"></td>
                        </tr>
                    <?php } ?>
                <?php } ?>

            </table>
            <a href="data_halaman.php" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>