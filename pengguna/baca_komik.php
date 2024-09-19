<?php
session_start();
include '../koneksi.php';

$id_halaman = $_GET['id'];

$query_halaman = "SELECT * FROM halaman WHERE id_halaman = $id_halaman";
$result_halaman = mysqli_query($db, $query_halaman);
$row_halaman = mysqli_fetch_assoc($result_halaman);

$id_komik = $row_halaman['id_komik'];
$query_komik = "SELECT * FROM komik WHERE id_komik = $id_komik";
$result_komik = mysqli_query($db, $query_komik);
$row_komik = mysqli_fetch_assoc($result_komik);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_pengguna'])) {
    $id_pengguna = $_SESSION['id_pengguna'];
    $konten = mysqli_real_escape_string($db, $_POST['konten']);
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : NULL;

    $query_insert = "INSERT INTO komentar (id_pengguna, id_halaman, konten, parent_id) VALUES ('$id_pengguna', '$id_halaman', '$konten', '$parent_id')";
    mysqli_query($db, $query_insert);
    header("Location: baca_komik.php?id=$id_halaman");
    exit();
}
include 'header.php';
$query_chapters = "SELECT * FROM halaman WHERE id_komik = $id_komik ORDER BY nomor_halaman ASC";
$result_chapters = mysqli_query($db, $query_chapters);

// Temukan nomor halaman saat ini dalam daftar chapter
$current_page_number = $row_halaman['nomor_halaman'];

// Temukan nomor halaman sebelum dan setelah halaman saat ini
$previous_page_number = $current_page_number - 1;
$next_page_number = $current_page_number + 1;
$query_previous_halaman = "SELECT * FROM halaman WHERE id_komik = $id_komik AND nomor_halaman < {$row_halaman['nomor_halaman']} ORDER BY nomor_halaman DESC LIMIT 1";
$result_previous_halaman = mysqli_query($db, $query_previous_halaman);
$row_previous_halaman = mysqli_fetch_assoc($result_previous_halaman);

// Query untuk halaman berikutnya
$query_next_halaman = "SELECT * FROM halaman WHERE id_komik = $id_komik AND nomor_halaman > {$row_halaman['nomor_halaman']} ORDER BY nomor_halaman ASC LIMIT 1";
$result_next_halaman = mysqli_query($db, $query_next_halaman);
$row_next_halaman = mysqli_fetch_assoc($result_next_halaman);

// Fungsi untuk menonaktifkan tombol jika halaman tidak tersedia
function disableButton($page_number, $chapters_count)
{
    if ($page_number < 1 || $page_number > $chapters_count) {
        return "disabled";
    }
}

$query_komentar = "SELECT k.*, p.username FROM komentar k JOIN pengguna p ON k.id_pengguna = p.id_pengguna WHERE k.id_halaman = $id_halaman ORDER BY k.dibuat_pada DESC";
$result_komentar = mysqli_query($db, $query_komentar);
?>


<div class="container my-5 bg-white">
    <div class="intro-lists">
        <div class="head-list row bg-light">
            <ul class="list-unstyled list-inline">
                <?php if ($row_previous_halaman) : ?>
                    <li class="list-inline-item">
                        <a href="baca_komik.php?id=<?php echo $row_previous_halaman['id_halaman']; ?>" class="btn btn-primary">Previous</a>
                    </li>
                <?php endif; ?>
                <li class="list-inline-item"><a data-toggle="tab" class="active" href="#ch">Chapter, <?php echo $row_halaman['nomor_halaman']; ?> - <?php echo $row_halaman['judul_halaman']; ?></a></li>
                <?php if ($row_next_halaman) : ?>
                    <li class="list-inline-item">
                        <a href="baca_komik.php?id=<?php echo $row_next_halaman['id_halaman']; ?>" class="btn btn-primary">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-content">
            <div id="ch" class="tab-pane fade in active show">
                <div class="row">
                    <?php for ($i = 1; $i <= 10; $i++) { ?>
                        <?php if (!empty($row_halaman['url_gambar_' . $i])) { ?>
                            <img src="../admin/<?php echo $row_halaman['url_gambar_' . $i]; ?>" class="img-fluid" alt="Halaman Komik <?php echo $i; ?>">
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-3">
    <div class="row">
        <div class="col">
            <?php if ($row_previous_halaman) : ?>
                <a href="baca_komik.php?id=<?php echo $row_previous_halaman['id_halaman']; ?>" class="btn btn-primary">Previous</a>
            <?php endif; ?>
        </div>
        <div class="col text-end">
            <?php if ($row_next_halaman) : ?>
                <a href="baca_komik.php?id=<?php echo $row_next_halaman['id_halaman']; ?>" class="btn btn-primary">Next</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="p-4">
            <h4 class="mb-4 pb-2">Komentar</h4>
            <p class="text-muted"><?php echo mysqli_num_rows($result_komentar); ?> Komentar</p>
            <?php if (isset($_SESSION['username_email'])) : ?>
                <form method="POST" action="">
                    <div class="d-flex flex-start">
                        <div class="card-footer py-3 border-0">
                            <div class="d-flex flex-start w-100">
                                <img class="rounded-circle shadow-1-strong" src="https://png.pngtree.com/png-vector/20190710/ourlarge/pngtree-user-vector-avatar-png-image_1541962.jpg" alt="avatar" width="40" height="40" />
                                <div class="form-outline w-100 px-2">
                                    <textarea class="form-control" name="konten" id="textAreaExample" rows="4" placeholder="Ikut Komentar" cols="2000"></textarea>
                                    <input type="hidden" name="parent_id" value="0">
                                    <button type="submit" class="btn btn-outline-success btn-sm mt-3">Post comment
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            <?php else : ?>
                <a href="login.php" onclick="return confirm('Anda harus login untuk mengomentari.');">
                    <div class="d-flex flex-start mt-4">
                        <div class="card-footer py-3 border-0">
                            <div class="d-flex flex-start w-100">
                                <img class="rounded-circle shadow-1-strong" src="https://png.pngtree.com/png-vector/20190710/ourlarge/pngtree-user-vector-avatar-png-image_1541962.jpg" alt="avatar" width="40" height="40" />
                                <div class="form-outline w-100 px-2">
                                    <textarea class="form-control" id="textAreaExample" rows="4" placeholder="Ikut Komentar" cols="200"></textarea>
                                    <button type="button" class="btn btn-outline-success btn-sm mt-3">Post comment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="p-4">
            <?php while ($row_komentar = mysqli_fetch_assoc($result_komentar)) : ?>
                <div class="d-flex flex-start mt-4">
                    <img class="rounded-circle shadow-1-strong me-3" src="https://png.pngtree.com/png-vector/20190710/ourlarge/pngtree-user-vector-avatar-png-image_1541962.jpg" alt="avatar" width="65" height="65" />
                    <div class="flex-grow-1 flex-shrink-1">
                        <div class="px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-1">
                                    <?php echo $row_komentar['username']; ?> <span class="small">- <?php echo $row_komentar['dibuat_pada']; ?></span>
                                </p>
                            </div>
                            <p class="small mb-0">
                                <?php echo htmlspecialchars($row_komentar['konten']); ?>
                            </p>
                            <a href="#!" class="reply-link" data-id="<?php echo $row_komentar['id_komentar']; ?>"><i class="fas fa-reply fa-xs"></i><span class="small"> reply</span></a>

                            <!-- Form untuk balasan komentar -->
                            <form method="POST" action="" class="reply-form" id="reply-form-<?php echo $row_komentar['id_komentar']; ?>" style="display: none;">
                                <div class="d-flex flex-start mt-2">
                                    <div class="card-footer py-3 border-0">
                                        <div class="d-flex flex-start w-100">
                                            <img class="rounded-circle shadow-1-strong" src="https://png.pngtree.com/png-vector/20190710/ourlarge/pngtree-user-vector-avatar-png-image_1541962.jpg" alt="avatar" width="40" height="40" />
                                            <div class="form-outline w-100 px-2">
                                                <textarea class="form-control" name="konten" rows="2" placeholder="Balas komentar..." cols="200"></textarea>
                                                <input type="hidden" name="parent_id" value="<?php echo $row_komentar['id_komentar']; ?>">
                                                <button type="submit" class="btn btn-outline-success btn-sm mt-3">Reply</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <?php
                // Query untuk menampilkan balasan komentar
                $parent_id = $row_komentar['id_komentar'];
                $query_reply = "SELECT k.*, p.username FROM komentar k JOIN pengguna p ON k.id_pengguna = p.id_pengguna WHERE k.parent_id = $parent_id ORDER BY k.dibuat_pada DESC";
                $result_reply = mysqli_query($db, $query_reply);
                while ($row_reply = mysqli_fetch_assoc($result_reply)) : ?>
                    <div class="d-flex flex-start mt-4 ml-5">
                        <img class="rounded-circle shadow-1-strong m-3" src="https://png.pngtree.com/png-vector/20190710/ourlarge/pngtree-user-vector-avatar-png-image_1541962.jpg" alt="avatar" width="65" height="65" />
                        <div class="flex-grow-1 flex-shrink-1">
                            <div class="px-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-1">
                                        <?php echo $row_reply['username']; ?> <span class="small">- <?php echo $row_reply['dibuat_pada']; ?></span>
                                    </p>
                                </div>
                                <p class="small mb-0">
                                    <?php echo htmlspecialchars($row_reply['konten']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<script>
    // Tampilkan form balasan saat tombol "reply" diklik
    const replyLinks = document.querySelectorAll('.reply-link');
    replyLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.getAttribute('data-id');
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            if (replyForm.style.display === 'none') {
                replyForm.style.display = 'block';
            } else {
                replyForm.style.display = 'none';
            }
        });
    });
</script>
<?php include 'footer.php'; ?>