<?php
session_start();
$title = "Edit Komik | Baca Komik";
include '../koneksi.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";

// Ambil ID komik dari URL
if (isset($_GET['id_komik'])) {
    $id_komik = $_GET['id_komik'];

    // Query untuk mendapatkan data komik dari database
    $query = $db->prepare("SELECT * FROM komik WHERE id_komik = ?");
    $query->bind_param("i", $id_komik);
    $query->execute();
    $result = $query->get_result();
    $komik = $result->fetch_assoc();

    if (!$komik) {
        $error_message = "Komik tidak ditemukan.";
    }
} else {
    header("Location: data_komik.php");
    exit();
}

//Ini yang terjadi ketika update komik diklik
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $judul = trim($_POST['judul']);
    $penulis = trim($_POST['penulis']);
    $deskripsi = trim($_POST['deskripsi']);
    $genreArray = $_POST['genre']; // genre akan berupa array
    $genre = implode(', ', $genreArray); // Ubah array menjadi string yang dipisahkan koma
    $diterbitkan_pada = $_POST['diterbitkan_pada'];
    $status = trim($_POST['status']);
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null; // Mengambil dan konversi nilai rating menjadi float

    // Tangani unggahan file
    if (isset($_FILES['url_gambar_sampul']) && $_FILES['url_gambar_sampul']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['url_gambar_sampul']['tmp_name'];
        $fileName = $_FILES['url_gambar_sampul']['name'];
        $fileSize = $_FILES['url_gambar_sampul']['size'];
        $fileType = $_FILES['url_gambar_sampul']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitasi nama file
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Periksa apakah file memiliki salah satu ekstensi berikut
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Direktori di mana file yang diunggah akan dipindahkan
            $uploadFileDir = 'sampul/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $url_gambar_sampul = $dest_path;
            } else {
                $error_message = 'Terjadi kesalahan saat memindahkan file yang diunggah.';
            }
        } else {
            $error_message = 'Unggahan gagal. Jenis file yang diperbolehkan: ' . implode(',', $allowedfileExtensions);
        }
    } else {
        $url_gambar_sampul = $komik['url_gambar_sampul'];
    }

    // Validasi input
    if (empty($judul) || empty($diterbitkan_pada) || empty($status) || $rating === null) {
        $error_message = "Judul, Tanggal Diterbitkan, Status, dan Rating harus diisi.";
    } else {
        // Query untuk memperbarui data komik di database
        $update_query = $db->prepare("UPDATE komik SET judul = ?, penulis = ?, deskripsi = ?, genre = ?, url_gambar_sampul = ?, diterbitkan_pada = ?, status = ?, rating = ? WHERE id_komik = ?");
        $update_query->bind_param("sssssssdi", $judul, $penulis, $deskripsi, $genre, $url_gambar_sampul, $diterbitkan_pada, $status, $rating, $id_komik);

        // Eksekusi query
        if ($update_query->execute()) {
            $_SESSION['success_message'] = "Data komik berhasil diperbarui.";

            // Redirect ke halaman daftar komik setelah berhasil memperbarui
            header("Location: data_komik.php");
            exit();
        } else {
            $error_message = "Terjadi kesalahan saat memperbarui data komik.";
        }
    }
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Edit Komik</h2>
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul">Judul Komik:</label>
                    <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($komik['judul']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="penulis">Penulis:</label>
                    <input type="text" class="form-control" id="penulis" name="penulis" value="<?php echo htmlspecialchars($komik['penulis']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo htmlspecialchars($komik['deskripsi']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <select class="form-control choices-multiple" id="genre" name="genre[]" multiple required>
                        <?php
                        $genres = array('Action', 'Adventure', 'Comedy', 'Fantasy', 'Sci-fi', 'Horror', 'Romance', 'Slice of Life');
                        $selected_genres = explode(', ', $komik['genre']);
                        foreach ($genres as $genre) {
                            $selected = in_array($genre, $selected_genres) ? 'selected' : '';
                            echo "<option value='$genre' $selected>" . ucfirst($genre) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="url_gambar_sampul">Gambar Sampul:</label>
                    <input type="file" class="form-control" id="url_gambar_sampul" name="url_gambar_sampul">
                    <?php if (!empty($komik['url_gambar_sampul'])) { ?>
                        <img src="<?php echo $komik['url_gambar_sampul']; ?>" alt="Gambar Sampul" width="100">
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="diterbitkan_pada">Tanggal Diterbitkan:</label>
                    <input type="date" class="form-control" id="diterbitkan_pada" name="diterbitkan_pada" value="<?php echo htmlspecialchars($komik['diterbitkan_pada']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option>Pilih Status</option>
                        <option value="Ongoing" <?php echo ($komik['status'] == 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="Completed" <?php echo ($komik['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <input type="number" class="form-control" id="rating" name="rating" min="0" max="5" step="0.01" value="<?php echo htmlspecialchars($komik['rating']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Perbarui Komik</button>
                <a href="data_komik.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script>
  new Choices(document.querySelector(".choices-multiple"));
</script>

