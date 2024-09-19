<?php
session_start();

// Include file koneksi database
include '../koneksi.php';

// Cek apakah form telah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $nama_pengguna = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $kata_sandi = $_POST['kata_sandi'];

    // Query untuk memeriksa apakah username sudah ada dalam database
    $check_username_query = "SELECT * FROM pengguna WHERE username = '$username'";
    $check_username_result = mysqli_query($db, $check_username_query);

    // Query untuk memeriksa apakah email sudah ada dalam database
    $check_email_query = "SELECT * FROM pengguna WHERE email = '$email'";
    $check_email_result = mysqli_query($db, $check_email_query);

    // Jika username sudah ada dalam database, tampilkan pesan kesalahan
    if (mysqli_num_rows($check_username_result) > 0) {
        $_SESSION['error_message'] = "Username sudah digunakan. Silakan coba username lain.";
        header("Location: daftar.php");
        exit();
    } 
    // Jika email sudah ada dalam database, tampilkan pesan kesalahan
    elseif (mysqli_num_rows($check_email_result) > 0) {
        $_SESSION['error_message'] = "Alamat email sudah digunakan. Silakan gunakan alamat email lain.";
        header("Location: daftar.php");
        exit();
    } else {
        // Jika username dan email belum ada dalam database, lakukan penyimpanan data
        // Query untuk menyimpan data ke dalam tabel pengguna
        $insert_query = "INSERT INTO pengguna (nama_pengguna, username, email, kata_sandi) VALUES ('$nama_pengguna', '$username', '$email', '$kata_sandi')";

        // Jalankan query
        if (mysqli_query($db, $insert_query)) {
            $_SESSION['success_message'] = "Akun berhasil dibuat. Silakan login.";
            header("Location: daftar.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Terjadi kesalahan saat membuat akun. Silakan coba lagi.";
            header("Location: daftar.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Registrasi Akun | Baca Komik</title>

    <!-- Custom fonts for this template-->
    <link href="../admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../admin/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Daftar Akun | Baca Komik</h1>
                            </div>
                            <?php if(isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $_SESSION['error_message']; ?>
                                </div>
                                <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['success_message'])): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $_SESSION['success_message']; ?>
                                </div>
                                <?php unset($_SESSION['success_message']); ?>
                            <?php endif; ?>
                            <form class="user" method="post">
                                <div class="form-group row">
                                    <div class="form-group col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="nama_pengguna"
                                            name="nama_pengguna" placeholder="Nama Anda">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="username"
                                            name="username" placeholder="Username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="email"
                                        name="email" placeholder="Email Address">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="exampleInputPassword" name="kata_sandi" placeholder="Password">
                                    </div>
                                    
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Daftar
                                </button>
                            
                            </form>
                            <hr>
                
                            <div class="text-center">
                                <a class="small" href="login.php">Sudah Punya Akun?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../admin/js/sb-admin-2.min.js"></script>

</body>

</html>

