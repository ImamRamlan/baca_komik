<?php
session_start();
include '../koneksi.php';
include 'header.php';

// Pastikan id_pengguna tersedia dan valid
if (isset($_SESSION['id_pengguna'])) {
    $id_pengguna = $_SESSION['id_pengguna'];

    // Query untuk mendapatkan data favorit berdasarkan id_pengguna
    $query = "SELECT favorit.*, komik.judul AS judul_komik, komik.url_gambar_sampul
              FROM favorit
              INNER JOIN komik ON favorit.id_komik = komik.id_komik
              WHERE favorit.id_pengguna = $id_pengguna";

    $result = mysqli_query($db, $query);
?>
    <div class="lastest container mt-4 mt-sm-5">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="font-weight-bolder float-left">Komik Favorit Anda</h2>
            </div>
        </div>

        <div id="post-container" class="posts row">
            <?php if (mysqli_num_rows($result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="card mb-3">
                            <a href="detail.php?id=<?php echo $row['id_komik']; ?>">
                                <img src="../admin/<?php echo $row['url_gambar_sampul']; ?>" class="card-img-top" alt="">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $row['judul_komik']; ?></h6>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="text-center">Tidak ada komik yang Anda favoritkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php
} else {
    // Tampilkan pesan jika id_pengguna tidak tersedia dalam sesi
    echo "ID Pengguna tidak tersedia.";
}
include 'footer.php';
?>
