<?php
session_start();
include '../koneksi.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_halaman'])) {
    $_SESSION['danger_message'] = "ID Halaman tidak disediakan!";
    header("Location: data_halaman.php");
    exit();
}

$id_halaman = $_GET['id_halaman'];

// Ambil data halaman untuk di-edit
$query = "SELECT * FROM halaman WHERE id_halaman = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_halaman);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    $_SESSION['danger_message'] = "Data halaman tidak ditemukan.";
    header("Location: data_halaman.php");
    exit();
}

// Ambil daftar komik untuk dropdown
$query_komik = "SELECT id_komik, judul FROM komik";
$result_komik = mysqli_query($db, $query_komik);

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_komik = $_POST['id_komik'];
    $nomor_halaman = $_POST['nomor_halaman'];
    $judul_halaman = $_POST['judul_halaman'];

    // Validate input
    if (empty($id_komik) || empty($nomor_halaman)) {
        $error_message = "Komik dan Nomor Halaman harus diisi.";
    } else {
        // Update data halaman
        $query_update = "UPDATE halaman SET id_komik = ?, nomor_halaman = ?, judul_halaman = ?";
        $params = [$id_komik, $nomor_halaman, $judul_halaman];
        $types = "iis";

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
                            move_uploaded_file($fileTmpName, $fileDestination);

                            // Add the file destination to the update query
                            $query_update .= ", url_gambar_$i = ?";
                            $params[] = $fileDestination;
                            $types .= "s";

                            // Remove the old file if it exists
                            if (!empty($data["url_gambar_$i"]) && file_exists($data["url_gambar_$i"])) {
                                unlink($data["url_gambar_$i"]);
                            }
                        } else {
                            $error_message = "Ukuran file terlalu besar.";
                        }
                    } else {
                        $error_message = "Terjadi kesalahan saat mengunggah file.";
                    }
                } else {
                    $error_message = "Tipe file tidak didukung.";
                }
            }
        }

        $query_update .= " WHERE id_halaman = ?";
        $params[] = $id_halaman;
        $types .= "i";

        $stmt_update = $db->prepare($query_update);
        $stmt_update->bind_param($types, ...$params);

        if ($stmt_update->execute()) {
            $success_message = "Halaman berhasil diperbarui.";
        } else {
            $error_message = "Gagal memperbarui halaman.";
        }
    }
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Edit Halaman</h2>
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
                            <option value="<?php echo $row_komik['id_komik']; ?>" <?php if ($row_komik['id_komik'] == $data['id_komik']) echo 'selected'; ?>><?php echo $row_komik['judul']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nomor_halaman">Nomor Halaman:</label>
                    <input type="number" class="form-control" id="nomor_halaman" name="nomor_halaman" value="<?php echo $data['nomor_halaman']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="judul_halaman">Judul Halaman</label>
                    <input type="text" class="form-control" id="judul_halaman" name="judul_halaman" value="<?php echo $data['judul_halaman']; ?>" required>
                </div>
                <?php for ($i = 1; $i <= 10; $i++) { ?>
                    <div class="form-group">
                        <label for="url_gambar_<?php echo $i; ?>">Gambar Halaman <?php echo $i; ?>:</label>
                        <?php if (!empty($data["url_gambar_$i"])) { ?>
                            <div class="mb-2">
                                <img src="<?php echo $data["url_gambar_$i"]; ?>" class="img-thumbnail" width="150" alt="Gambar Halaman <?php echo $i; ?>">
                            </div>
                        <?php } ?>
                        <input type="file" class="form-control-file" id="url_gambar_<?php echo $i; ?>" name="url_gambar_<?php echo $i; ?>" accept="image/jpeg, image/png, image/gif">
                    </div>
                <?php } ?>

                <button type="submit" class="btn btn-primary">Perbarui Halaman</button>
                <a href="data_halaman.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
