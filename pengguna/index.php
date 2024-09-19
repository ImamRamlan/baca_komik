<?php
// Fungsi untuk mengubah selisih waktu menjadi format "1 hari yang lalu", "2 jam yang lalu", dst.
function waktu_terakhir($timestamp)
{
    $selisih = time() - strtotime($timestamp);
    if ($selisih < 60) {
        return 'Semenit yang lalu';
    } elseif ($selisih < 3600) {
        $menit = round($selisih / 60);
        return $menit . ' Menit yang lalu';
    } elseif ($selisih < 86400) {
        $jam = round($selisih / 3600);
        return $jam . ' Jam yang lalu';
    } else {
        // Ubah format waktu ke tanggal sebenarnya
        $hari = date('d M', strtotime($timestamp));
        return 'Update ' . $hari;
    }
}

$hari_ini = date("l");

// Daftar hari dalam bahasa Indonesia
$daftar_hari = array(
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
);

session_start();
include '../koneksi.php';
include 'header.php';

$selected_day = isset($_GET['day']) ? $_GET['day'] : $hari_ini;

// Query untuk mengambil data komik beserta halamannya berdasarkan hari yang dipilih
$query = "SELECT halaman.*, komik.judul, komik.genre, komik.penulis, komik.status, komik.deskripsi, komik.url_gambar_sampul
          FROM halaman
          LEFT JOIN komik ON halaman.id_komik = komik.id_komik
          WHERE DAYNAME(halaman.diperbaru_pada) = '$selected_day'
          ORDER BY halaman.diperbaru_pada DESC";

$result = mysqli_query($db, $query);
?>

<div class="lastest container mt-4 mt-sm-5">
    <div class="row">
        <div class="col-lg-6">
            <h2 class="font-weight-bolder float-left">Update Terakhir</h2>
        </div>
        <div class="col-lg-6">
            <ul class="calendar list-unstyled list-inline float-right font-weight-bold mt-3 mt-sm-0">
                <?php foreach ($daftar_hari as $nama_hari_inggris => $nama_hari_indonesia) : ?>
                    <?php $kelas_aktif = ($nama_hari_inggris == $selected_day) ? 'active' : ''; ?>
                    <li class="list-inline-item <?php echo $kelas_aktif; ?>" onclick="loadData('<?php echo $nama_hari_inggris; ?>')"><?php echo $nama_hari_indonesia; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div id="post-container" class="posts row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <div class="card mb-3">
                        <a href="detail.php?id=<?php echo $row['id_komik']; ?>"><img src="../admin/<?php echo $row['url_gambar_sampul']; ?>" class="card-img-top" alt=""></a>
                        <div class="over text-center">
                            <div class="head text-left">
                                <h6>Judul <?php echo $row['judul']; ?></h6>
                            </div>
                            <div class="about-list">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Genre:</th>
                                            <td><?php echo $row['genre']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Penulis:</th>
                                            <td><?php echo $row['penulis']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Update:</th>
                                            <td><?php echo waktu_terakhir($row['diperbaru_pada']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="about text-muted">
                                <?php echo $row['deskripsi']; ?>
                            </p>
                            <a class="reading btn" href="details.html">Mulai (Baca Halaman <?php echo $row['nomor_halaman']; ?>)</a>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><a href="details.html"><?php echo $row['judul']; ?></a></h5>
                            <p class="card-text">Chapter, <?php echo $row['nomor_halaman']; ?></p>
                            <p class="card-text"><small class="text-muted text-uppercase">Update <?php echo waktu_terakhir($row['diperbaru_pada']); ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Tidak ada komik yang diupdate pada hari ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function loadData(day) {
    window.location.href = 'index.php?day=' + day;
}
</script>

<?php include 'footer.php'; ?>
