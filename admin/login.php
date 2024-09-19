<?php
session_start();
include '../koneksi.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (empty(trim($_POST["username"])) || empty(trim($_POST["password"])) || empty($_POST["hak_akses"])) {
        $error_message = "Mohon lengkapi semua field.";
    } else {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $hak_akses = $_POST["hak_akses"];
        
        // Query untuk memeriksa kredensial admin
        $sql = "SELECT id_admin, nama_admin FROM admin WHERE username = ? AND hak_akses = ?";
        
        if ($stmt = $db->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_hak_akses);
            $param_username = $username;
            $param_hak_akses = $hak_akses;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                // Memeriksa jika username dan hak akses sesuai
                if ($stmt->num_rows == 1) {
                    // Bind hasil query ke variabel
                    $stmt->bind_result($id_admin, $nama_admin);
                    if ($stmt->fetch()) {
                        // Verifikasi password di sini sesuai kebutuhan
                        // Jika verifikasi password berhasil, buat sesi
                        // Anda dapat menggunakan fungsi password_verify() untuk memeriksa password yang di-hash
                        // Contoh: if (password_verify($password, $hashed_password)) {
                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["username"] = $username;
                        $_SESSION["hak_akses"] = $hak_akses;
                        $_SESSION["nama_admin"] = $nama_admin;
                        $_SESSION["id_admin"] = $id_admin;
                        
                        // Mengarahkan pengguna ke halaman admin atau editor sesuai dengan hak akses
                        if ($hak_akses == 'Admin') {
                            header("location: index.php");
                        } elseif ($hak_akses == 'Editor') {
                            header("location: index.php");
                        }
                        // } else {
                        //     // Jika verifikasi password gagal
                        //     $error_message = "Password salah.";
                        // }
                    }
                } else {
                    // Menampilkan pesan kesalahan jika username atau hak akses salah
                    $error_message = "Username atau hak akses salah.";
                }
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            
            // Menutup statement
            $stmt->close();
        }
        
        // Menutup koneksi
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Baca Komik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('bg_login.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100%;
            overflow: auto;
        }

        .login-container {
            margin-top: 100px;
        }

        .card {
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center login-container">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-black">
                        <h5 class="mb-0 text-center">Masuk</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <?php if (!empty($error_message)) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan Username..">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan Kata sandi..">
                            </div>
                            <div class="form-group">
                                <select class="form-control" id="hak_akses" name="hak_akses" required>
                                    <option value="">Pilih Hak Akses</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Editor">Editor</option>
                                </select>
                            </div>
                            <p class="text-secondary text-center">Lupa password? Konfirmasi ke Admin.</p>
                            <button type="submit" class="btn btn-success btn-block">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
