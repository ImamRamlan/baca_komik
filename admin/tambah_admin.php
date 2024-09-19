<?php
session_start();
$title = "Tambah Admin | Baca Komik";
include '../koneksi.php';

// Ensure only admins can access this page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $nama_admin = trim($_POST['nama_admin']);
    $username = trim($_POST['username']);
    $kata_sandi = trim($_POST['kata_sandi']);
    $hak_akses = trim($_POST['hak_akses']);
    $no_telp = trim($_POST['no_telp']);

    // Validate input
    if (empty($nama_admin) || empty($username) || empty($kata_sandi) || empty($hak_akses)) {
        $error_message = "Semua field harus diisi.";
    } else {
        // Check if username already exists
        $query = $db->prepare("SELECT * FROM admin WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username telah ada. Silakan gunakan username lain.";
        } else {
            // Insert new admin data
            $insert_query = $db->prepare("INSERT INTO admin (nama_admin, username, kata_sandi, hak_akses , no_telp) VALUES (?, ?, ?, ?, ?)");
            $insert_query->bind_param("sssss", $nama_admin, $username, $kata_sandi, $hak_akses, $no_telp);

            if ($insert_query->execute()) {
                $_SESSION['success_message'] = "Data admin berhasil ditambahkan.";
                header("Location: data_admin.php");
                exit();
            } else {
                $error_message = "Terjadi kesalahan saat menambahkan admin.";
            }
        }
    }
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Tambah Admin Baru</h2>
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <form method="post">
                <div class="form-group">
                    <label for="nama_admin">Nama Admin:</label>
                    <input type="text" class="form-control" id="nama_admin" name="nama_admin" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="kata_sandi">Kata Sandi:</label>
                    <input type="password" class="form-control" id="kata_sandi" name="kata_sandi" required>
                </div>
                <div class="form-group">
                    <label for="hak_akses">Hak Akses:</label>
                    <select class="form-control" id="hak_akses" name="hak_akses" required>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                    </select>
                </div>
                <!-- kolom baru -->
                <div class="form-group">
                    <label for="no_telp">Nomor Telepon:</label>
                    <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                </div>

                <button type="submit" class="btn btn-primary">Tambah Admin</button>
                <a href="data_admin.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
