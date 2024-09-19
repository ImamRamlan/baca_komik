<?php
session_start();
$title = "Edit Admin | Baca Komik";
include '../koneksi.php';

// Ensure only admins can access this page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";

// Get the admin ID from the URL
if (isset($_GET['id_admin'])) {
    $id_admin = intval($_GET['id_admin']);
} else {
    header("Location: data_admin.php");
    exit();
}

// Fetch the existing admin data
$query = $db->prepare("SELECT * FROM admin WHERE id_admin = ?");
$query->bind_param("i", $id_admin);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    header("Location: data_admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $nama_admin = trim($_POST['nama_admin']);
    $username = trim($_POST['username']);
    $kata_sandi = trim($_POST['kata_sandi']);
    $hak_akses = trim($_POST['hak_akses']);
    $no_telp = trim($_POST['no_telp']);

    // Validate input
    if (empty($nama_admin) || empty($username) || empty($hak_akses)) {
        $error_message = "Nama Admin, Username, dan Hak Akses harus diisi.";
    } else {
        // Check if username already exists for other admins
        $query = $db->prepare("SELECT * FROM admin WHERE username = ? AND id_admin != ?");
        $query->bind_param("si", $username, $id_admin);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username telah ada. Silakan gunakan username lain.";
        } else {
            // Update admin data
            if (!empty($kata_sandi)) {
                $update_query = $db->prepare("UPDATE admin SET nama_admin = ?, username = ?, kata_sandi = ?, hak_akses = ?, no_telp = ? WHERE id_admin = ?");
                $update_query->bind_param("sssssi", $nama_admin, $username, $kata_sandi, $hak_akses, $no_telp, $id_admin);
            } else {
                $update_query = $db->prepare("UPDATE admin SET nama_admin = ?, username = ?, hak_akses = ?, no_telp = ? WHERE id_admin = ?");
                $update_query->bind_param("ssssi", $nama_admin, $username, $hak_akses, $no_telp, $id_admin);
            }

            if ($update_query->execute()) {
                $_SESSION['success_message'] = "Admin berhasil diperbarui.";
                header("Location: data_admin.php");
                exit();
            } else {
                $error_message = "Terjadi kesalahan saat memperbarui data admin.";
            }
        }
    }
}

include 'header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2>Edit Admin</h2>
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <form method="post">
                <div class="form-group">
                    <label for="nama_admin">Nama Admin:</label>
                    <input type="text" class="form-control" id="nama_admin" name="nama_admin" value="<?php echo htmlspecialchars($admin['nama_admin']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="kata_sandi">Kata Sandi (kosongkan jika tidak ingin mengubah):</label>
                    <input type="password" class="form-control" id="kata_sandi" name="kata_sandi">
                </div>
                <div class="form-group">
                    <label for="hak_akses">Hak Akses:</label>
                    <select class="form-control" id="hak_akses" name="hak_akses" required>
                        <option value="admin" <?php echo $admin['hak_akses'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="editor" <?php echo $admin['hak_akses'] == 'editor' ? 'selected' : ''; ?>>Editor</option>
                    </select>
                </div>
                <!-- kolom baru -->
                <div class="form-group">
                    <label for="no_telp">Nomor Telepon:</label>
                    <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($admin['no_telp']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="data_admin.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
