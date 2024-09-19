<?php
session_start();
include '../koneksi.php';
include 'header.php';

// Terima id_komik dari URL
$id_komik = isset($_GET['id']) ? $_GET['id'] : null;

// Jika id_komik tidak ditemukan dalam URL, beri tanggapan dan keluar dari skrip
if (!$id_komik) {
    echo "Parameter 'id' tidak ditemukan dalam URL.";
    exit();
}

// Query untuk mengambil detail komik berdasarkan id_komik
$query_komik = "SELECT * FROM komik WHERE id_komik = $id_komik";
$result_komik = mysqli_query($db, $query_komik);
$row_komik = mysqli_fetch_assoc($result_komik);

// Query untuk mengambil daftar halaman komik berdasarkan id_komik
$query_halaman = "SELECT * FROM halaman WHERE id_komik = $id_komik ORDER BY nomor_halaman ASC";
$result_halaman = mysqli_query($db, $query_halaman);

// Salin hasil query untuk penggunaan berikutnya
$result_halaman_copy = mysqli_query($db, $query_halaman);

// Fungsi untuk mengubah waktu terakhir pembaruan menjadi format "satu menit yang lalu", "satu jam yang lalu", dst.
function waktu_terakhir_detail($timestamp)
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
        $tanggal = date('d M Y, H:i', strtotime($timestamp));
        return 'Update ' . $tanggal;
    }
}

// Jika pengguna menekan tombol bookmark atau unbookmark
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['bookmark'])) {
        // Jika tombol bookmark ditekan

        // Pastikan pengguna sudah masuk
        if (isset($_SESSION['id_pengguna'])) {
            $id_pengguna = $_SESSION['id_pengguna'];

            // Lakukan operasi penyimpanan ke dalam database favorit
            $query = "INSERT INTO favorit (id_pengguna, id_komik) VALUES ($id_pengguna, $id_komik)";
            if (mysqli_query($db, $query)) {
                echo "Komik berhasil ditambahkan ke daftar favorit.";
            } else {
                echo "Terjadi kesalahan saat menambahkan komik ke daftar favorit: " . mysqli_error($db);
            }
        } else {
            echo "Anda harus masuk untuk menggunakan fitur bookmark!";
        }
    } elseif (isset($_POST['unbookmark'])) {
        // Jika tombol unbookmark ditekan

        // Ambil id_pengguna dan id_komik dari form
        $id_pengguna = isset($_POST['id_pengguna']) ? $_POST['id_pengguna'] : null;
        $id_komik = isset($_POST['id_komik']) ? $_POST['id_komik'] : null;

        // Lakukan operasi penghapusan dari tabel favorit
        $query_delete_bookmark = "DELETE FROM favorit WHERE id_pengguna = $id_pengguna AND id_komik = $id_komik";
        if (mysqli_query($db, $query_delete_bookmark)) {
            echo "Komik berhasil dihapus dari daftar favorit.";
        } else {
            echo "Terjadi kesalahan saat menghapus komik dari daftar favorit: " . mysqli_error($db);
        }
    }
}

$bookmarked = false; // Default value if user is not logged in
if (isset($_SESSION['id_pengguna'])) {
    $query_check_bookmark = "SELECT * FROM favorit WHERE id_pengguna = $_SESSION[id_pengguna] AND id_komik = $id_komik";
    $result_check_bookmark = mysqli_query($db, $query_check_bookmark);
    // Periksa apakah ada hasil dari query, jika ya, artinya komik sudah dibookmark
    $bookmarked = mysqli_num_rows($result_check_bookmark) > 0;
}
?>

<div class="container my-5">
    <div class="read-intro bg-light">
        <?php if (isset($_SESSION['username_email'])) : ?>
            <!-- Jika pengguna sudah login -->
            <form action="" method="post">
                <?php if ($bookmarked) : ?>
                    <button type="submit" name="unbookmark" class="btn btn-link">
                        <i class="fa fa-bookmark fa-3x"></i>
                    </button>
                <?php else : ?>
                    <!-- Jika komik belum dibookmark -->
                    <button type="submit" name="bookmark" class="btn btn-link">
                        <i class="far fa-bookmark fa-3x"></i>
                    </button>
                <?php endif; ?>
                <!-- Sisipkan input hidden untuk menyimpan id_pengguna dan id_komik -->
                <input type="hidden" name="id_pengguna" value="<?php echo $_SESSION['id_pengguna']; ?>">
                <input type="hidden" name="id_komik" value="<?php echo $id_komik; ?>">
            </form>
        <?php else : ?>
            <!-- Jika pengguna belum login -->
            <a href="login.php" class="btn btn-link" onclick="return confirm('Anda Harus Login untuk menggunakan fitur bookmark!!');">
                <i class="far fa-bookmark fa-3x"></i>
            </a>
        <?php endif; ?>
        <div class="row">
            <div class="cover col-*">
                <img class="shadow" src="../admin/<?php echo $row_komik['url_gambar_sampul']; ?>" alt="">
            </div>
            <div class="info col">
                <h2 class="head"><?php echo $row_komik['judul']; ?></h2>
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row">Genre:</th>
                            <td><?php echo $row_komik['genre']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Artist:</th>
                            <td><?php echo $row_komik['penulis']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Update:</th>
                            <!-- Perbaikan disini -->
                            <td>Chapter Terakhir,
                                <?php
                                if (mysqli_num_rows($result_halaman) > 0) {
                                    mysqli_data_seek($result_halaman, mysqli_num_rows($result_halaman) - 1); // Pindahkan pointer ke baris terakhir
                                    $row_last_page = mysqli_fetch_assoc($result_halaman);
                                    echo $row_last_page['nomor_halaman'];
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Rating:</th>
                            <td>
                                <?php
                                $rating = $row_komik['rating'];
                                $stars = floor($rating); // Bagian integer dari rating
                                $half_star = ceil($rating - $stars); // Apakah ada setengah bintang?

                                // Tampilkan bintang penuh
                                for ($i = 0; $i < $stars; $i++) {
                                    echo '<i class="fa fa-star fa-2x"></i>';
                                }

                                // Tampilkan setengah bintang jika ada
                                if ($half_star) {
                                    echo '<i class="fa fa-star-half-alt fa-2x"></i>';
                                }
                                ?>
                                <!-- Tampilkan rating dalam tanda kurung -->
                                <span class="font-weight-bold ml-3">(<?php echo $rating; ?>/5)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>
                    <?php echo $row_komik['deskripsi']; ?>
                </p>
            </div>
        </div>
        <div class="row">
            <a class="btn btn-red my-3 mx-1 px-5" href="#">Start reading Chap.1</a>
        </div>
    </div>
</div>

<!-- end reading intro -->

<!-- start intro lists -->
<div class="container my-5 bg-white">
    <div class="intro-lists">
        <div class="head-list row bg-light">
            <ul class="list-unstyled list-inline">
                <li class="list-inline-item"><a data-toggle="tab" class="active" href="#ch">Chapter</a></li>
            </ul>
        </div>
        <!-- lakukan perulangan halaman -->
        <div class="tab-content">
            <!-- start ch -->
            <div id="ch" class="tab-pane fade in active show">
                <div class="row">
                    <table class="table table-striped">
                        <tbody>
                            <?php while ($row_halaman = mysqli_fetch_assoc($result_halaman_copy)) : ?>
                                <tr>
                                    <th><a href="baca_komik.php?id=<?php echo $row_halaman['id_halaman']; ?>">CH. <?php echo $row_halaman['nomor_halaman']; ?>, <?php echo $row_halaman['judul_halaman']; ?></a></th>
                                    <td class="text-muted text-uppercase float-right"><?php echo waktu_terakhir_detail($row_halaman['diperbaru_pada']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end ch -->
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>