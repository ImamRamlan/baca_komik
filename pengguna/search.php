<?php
session_start();
include '../koneksi.php';
include 'header.php';

// Ambil query pencarian
$query_search = isset($_GET['query']) ? $_GET['query'] : '';

// Query untuk mencari komik berdasarkan judul, genre, atau penulis
$query = "SELECT * FROM komik
          WHERE judul LIKE '%$query_search%'
          OR genre LIKE '%$query_search%'
          OR penulis LIKE '%$query_search%'";

$result = mysqli_query($db, $query);
?>

<div class="lastest container mt-4 mt-sm-5">
    <div class="row">
        <div class="col-lg-6">
            <h2 class="font-weight-bolder float-left">Hasil Pencarian</h2>
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
                            <h6 class="card-title"><?php echo $row['judul']; ?></h6>
                            <p class="card-text">Genre: <?php echo $row['genre']; ?></p>
                            <p class="card-text">Penulis: <?php echo $row['penulis']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-12">
                <p class="text-center">Tidak ada hasil yang ditemukan untuk pencarian "<?php echo htmlspecialchars($query_search); ?>".</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
