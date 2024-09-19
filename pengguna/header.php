<?php
// Fungsi untuk mendapatkan nama file dari URL saat ini
function getCurrentPage()
{
    return basename($_SERVER['PHP_SELF']);
}

$current_page = getCurrentPage();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manga Reader</title>

    <!-- css files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.10.2/css/all.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <!-- start navbar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow py-2 py-sm-0">
        <a class="navbar-brand" href="index.php">
            <h5>Baca Komik</h5>
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="container-fluid">
                <div class="row py-3">
                    <div class="col-lg-6 col-sm-12 mb-3 mb-sm-0">
                        <ul class="navbar-nav mr-auto">
                            <!-- always use single word for li -->
                            <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php">Beranda</a>
                            </li>
                            <li class="nav-item <?php echo $current_page == 'new.php' ? 'active' : ''; ?>">
                                <a class="nav-link" href="new.php">New</a>
                            </li>
                            <li class="nav-item <?php echo $current_page == 'populer.php' ? 'active' : ''; ?>">
                                <a class="nav-link" href="populer.php">Populer</a>
                            </li>

                        </ul>
                    </div>
                    <div class="col">
                        <form class="form-inline search" action="search.php" method="GET">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control" placeholder="Type Title, author or genre" aria-label="Type Title, author or genre" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile float-right">
            <button class="btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bookmark fa-2x"></i>
            </button>

            <div class="profile float-right">
                <?php
                // Periksa apakah pengguna telah login berdasarkan keberadaan username atau email
                if (isset($_SESSION['username_email'])) :
                ?>
                    <!-- Jika pengguna telah login, tampilkan dropdown menu dengan opsi akun dan logout -->
                    <div class="account">
                        <button class="btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user-circle fa-2x"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="favorit.php">Daftar Favorit</a>
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- Jika pengguna belum login, tampilkan tombol untuk login -->
                    <div class="account">
                        <a class="btn" href="login.php">
                            <i class="fa fa-user-circle fa-2x"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>
    <!-- end navbar-->

    <!-- start slider -->
    <div id="mangaslider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="img/slider1.jpg" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="img/slider2.jpg" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="img/slider3.jpg" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#mangaslider" role="button" data-slide="prev">
            <div><span class="carousel-control-prev-icon" aria-hidden="true"></span></div>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#mangaslider" role="button" data-slide="next">
            <div><span class="carousel-control-next-icon" aria-hidden="true"></span></div>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <!-- end slider -->