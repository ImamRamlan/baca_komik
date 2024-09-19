<?php
// Koneksi ke database
include '../koneksi.php';

// Query untuk mengambil data komik beserta halamannya
$query = "SELECT halaman.*,komik.url_gambar_sampul
          FROM halaman
          LEFT JOIN komik ON halaman.id_komik = komik.id_komik
          ORDER BY halaman.diperbaru_pada DESC";

$result = mysqli_query($db, $query);

while ($row = mysqli_fetch_assoc($result)) {
    // Tampilkan gambar sampul
    ?>
    <a href="details.html"><img src="<?php echo htmlspecialchars($row['url_gambar_sampul']); ?>" alt=""></a>
    <?php
}
?>
