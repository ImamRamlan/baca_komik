<?php
session_start();
$title = "Tambah Halaman | Baca Komik";
include '../koneksi.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil daftar komik untuk dropdown
$query_komik = "SELECT id_komik, judul FROM komik";
$result_komik = mysqli_query($db, $query_komik);

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $id_komik = $_POST['id_komik'];
    $nomor_halaman = $_POST['nomor_halaman'];
    $judul_halaman = $_POST['judul_halaman'];

    // Validate input
    if (empty($id_komik) || empty($nomor_halaman)) {
        $error_message = "Komik dan Nomor Halaman harus diisi.";
    } else {
        // Prepare the SQL query
        $query_insert = "INSERT INTO halaman (id_komik, nomor_halaman, judul_halaman";
        $query_values = ") VALUES ('$id_komik', '$nomor_halaman', '$judul_halaman'";

        // Loop through each uploaded file
        for ($i = 1; $i <= 10; $i++) {
            $fileInputName = 'url_gambar_' . $i;
            if (!empty($_FILES[$fileInputName]['name'])) {
                $fileName = $_FILES[$fileInputName]['name'];
                $fileTmpName = $_FILES[$fileInputName]['tmp_name'];
                $fileSize = $_FILES[$fileInputName]['size'];
                $fileError = $_FILES[$fileInputName]['error'];
                $fileType = $_FILES[$fileInputName]['type'];

                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));

                $allowed = array('jpg', 'jpeg', 'png', 'gif');

                if (in_array($fileActualExt, $allowed)) {
                    if ($fileError === 0) {
                        if ($fileSize < 5000000) {
                            $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                            $fileDestination = 'gambar_halaman/' . $fileNameNew;
                            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                                // Add the file destination to the query
                                $query_insert .= ", url_gambar_$i";
                                $query_values .= ", '$fileDestination'";
                            } else {
                                $error_message = "Terjadi kesalahan saat memindahkan file.";
                                break;
                            }
                        } else {
                            $error_message = "Ukuran file terlalu besar.";
                            break;
                        }
                    } else {
                        $error_message = "Terjadi kesalahan saat mengunggah file.";
                        break;
                    }
                } else {
                    $error_message = "Tipe file tidak didukung.";
                    break;
                }
            }
        }

        if (empty($error_message)) {
            // Complete the query and execute
            $query_insert .= $query_values . ")";
            if (mysqli_query($db, $query_insert)) {
                $success_message = "Halaman baru berhasil ditambahkan.";
            } else {
                $error_message = "Gagal menambahkan halaman baru. Kesalahan: " . mysqli_error($db);
            }
        }
    }
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Tambah Halaman Baru</h2>
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <?php if (!empty($success_message)) { ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php } ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="id_komik">Komik:</label>
                    <select class="form-control" id="id_komik" name="id_komik" required>
                        <option value="">Pilih Komik</option>
                        <?php while ($row_komik = mysqli_fetch_assoc($result_komik)) { ?>
                            <option value="<?php echo $row_komik['id_komik']; ?>"><?php echo $row_komik['judul']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nomor_halaman">Nomor Halaman:</label>
                    <input type="number" class="form-control" id="nomor_halaman" name="nomor_halaman" required>
                </div>
                <div class="form-group">
                    <label for="judul_halaman">Judul Halaman</label>
                    <input type="text" class="form-control" id="judul_halaman" name="judul_halaman" required>
                </div>
                <?php for ($i = 1; $i <= 10; $i++) { ?>
                    <div class="form-group">
                        <label for="url_gambar_<?php echo $i; ?>">Gambar Halaman <?php echo $i; ?>:</label>
                        <input type="file" class="form-control-file" id="url_gambar_<?php echo $i; ?>" name="url_gambar_<?php echo $i; ?>" accept="image/jpeg, image/png, image/gif">
                    </div>
                <?php } ?>

                <button type="submit" class="btn btn-primary">Tambah Halaman</button>
                <a href="data_halaman.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
