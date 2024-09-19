<?php
session_start();
$title = "Data Pengguna | Baca Komik";
include '../koneksi.php';
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit(); // Pastikan kode selanjutnya tidak dieksekusi setelah mengarahkan
}
$success_message = '';

// Periksa apakah ada pesan sukses dalam sesi
if (isset($_SESSION['success_message'])) {
    // Jika ada, simpan ke variabel lokal dan hapus dari sesi
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
$danger_message = '';

// Periksa apakah ada pesan sukses dalam sesi
if (isset($_SESSION['danger_message'])) {
    // Jika ada, simpan ke variabel lokal dan hapus dari sesi
    $danger_message = $_SESSION['danger_message'];
    unset($_SESSION['danger_message']);
}
include 'header.php';

?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <span> <strong>Data Pengguna</strong></span>
    </div>
    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengguna</h6>
            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($danger_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $danger_message; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Username</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Username</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM pengguna"; // Query untuk mengambil data dari tabel pengguna
                        $result = mysqli_query($db, $query); // Eksekusi query
                        $i = 1; // Inisialisasi counter untuk nomor urut
                        while ($row = mysqli_fetch_assoc($result)) { // Looping untuk menampilkan data
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td> <!-- Menampilkan nomor urut -->
                                <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td> <!-- Menampilkan data nama pengguna -->
                                <td><?php echo htmlspecialchars($row['username']); ?></td> <!-- Menampilkan data username -->
                                <td class="text-center">
                                    <!-- Tombol untuk hapus dan edit data pengguna -->
                                    <a href="delete_pengguna.php?id_pengguna=<?php echo $row['id_pengguna']; ?>" class="btn btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data pengguna ini?');"><i class="fas fa-trash"> Hapus</i></a>
                                    
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
