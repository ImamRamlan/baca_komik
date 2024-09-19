<?php
session_start();
include '../koneksi.php';
include 'header.php';

// Query untuk mendapatkan data komik populer berdasarkan jumlah favorit
$query = "SELECT komik.*, COUNT(favorit.id_favorit) AS jumlah_favorit
          FROM komik
          INNER JOIN favorit ON komik.id_komik = favorit.id_komik
          GROUP BY komik.id_komik
          ORDER BY jumlah_favorit DESC
          LIMIT 10";  // Menampilkan 10 komik terpopuler

$result = mysqli_query($db, $query);
?>

<div class="lastest container mt-4 mt-sm-5">
    <div class="row">
        <div class="col-lg-6">
            <h2 class="font-weight-bolder float-left">Komik Populer</h2>
        </div>
    </div>

    <div id="post-container" class="posts row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col-lg-2 col-md-3 col-sm-4">
                <h6><?php echo $row['judul']; ?></h6>
                    <div class="card mb-3">
                        <a href="detail.php?id=<?php echo $row['id_komik']; ?>"><img src="../admin/<?php echo $row['url_gambar_sampul']; ?>" class="card-img-top" alt=""></a>   
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Tidak ada komik yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
