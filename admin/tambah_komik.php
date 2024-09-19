<?php
session_start();
$title = "Tambah Komik | Baca Komik";
include '../koneksi.php';

// Ensure only admins can access this page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";
//Kodingan ini jalan ketika tambah komik di klik
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $judul = trim($_POST['judul']);
    $penulis = trim($_POST['penulis']);
    $deskripsi = trim($_POST['deskripsi']);
    $genreArray = $_POST['genre']; // genre will be an array
    $genre = implode(', ', $genreArray); // Convert array to comma-separated string
    $diterbitkan_pada = $_POST['diterbitkan_pada'];
    $status = trim($_POST['status']);
    $rating = $_POST['rating']; // Retrieve rating value
    // Handle the file upload
    if (isset($_FILES['url_gambar_sampul']) && $_FILES['url_gambar_sampul']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['url_gambar_sampul']['tmp_name'];
        $fileName = $_FILES['url_gambar_sampul']['name'];
        $fileSize = $_FILES['url_gambar_sampul']['size'];
        $fileType = $_FILES['url_gambar_sampul']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        //Check jika jenis gambarnya
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory in which the uploaded file will be moved
            $uploadFileDir = 'sampul/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $url_gambar_sampul = $dest_path;
            } else {
                $error_message = 'There was an error moving the uploaded file.';
            }
        } else {
            $error_message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    } else {
        $error_message = 'Error in uploading file. Error:' . $_FILES['url_gambar_sampul']['error'];
    }

    // Validate input harus terisi
    if (empty($judul) || empty($diterbitkan_pada) || empty($status) || empty($url_gambar_sampul) || empty($rating)) {
        $error_message = "Judul, Tanggal Diterbitkan, Status, Gambar Sampul, dan Rating harus diisi.";
    } else {
        // Query tambah data ke tabel  dari form input
        $insert_query = $db->prepare("INSERT INTO komik (judul, penulis, deskripsi, genre, url_gambar_sampul, diterbitkan_pada, status, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_query->bind_param("sssssssd", $judul, $penulis, $deskripsi, $genre, $url_gambar_sampul, $diterbitkan_pada, $status, $rating);

        // Execute the query
        if ($insert_query->execute()) {
            $_SESSION['success_message'] = "Data komik berhasil ditambahkan.";

            // Redirect to the comic list page after successful addition
            header("Location: data_komik.php");
            exit();
        } else {
            $error_message = "Terjadi kesalahan saat menambahkan data komik.";
        }
    }
}
//sampai sini
include 'header.php';
?>
<!-- codingan pembuatan fom tambah komik -->
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Tambah Komik Baru</h2>
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul">Judul Komik:</label>
                    <input type="text" class="form-control" id="judul" name="judul" required>
                </div>
                <div class="form-group">
                    <label for="penulis">Penulis:</label>
                    <input type="text" class="form-control" id="penulis" name="penulis" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <select class="form-control choices-multiple" id="genre" name="genre[]" multiple required>
                        <option value="Action">Action</option>
                        <option value="Adventure">Adventure</option>
                        <option value="Comedy">Comedy</option>
                        <option value="Fantasy">Fantasy</option>
                        <option value="Sci-fi">Sci-Fi</option>
                        <option value="Horror">Horror</option>
                        <option value="Romance">Romance</option>
                        <option value="Slice of Life">Slice of Life</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="url_gambar_sampul">Gambar Sampul:</label>
                    <input type="file" class="form-control" id="url_gambar_sampul" name="url_gambar_sampul" required>
                </div>
                <div class="form-group">
                    <label for="diterbitkan_pada">Tanggal Diterbitkan:</label>
                    <input type="date" class="form-control" id="diterbitkan_pada" name="diterbitkan_pada" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <input type="number" class="form-control" id="rating" name="rating" min="0" max="5" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Tambah Komik</button>
                <a href="data_komik.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script>
  new Choices(document.querySelector(".choices-multiple"));
</script>
