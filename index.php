<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Joki Game</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0 text-center"></h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">Silakan pilih sesi Anda untuk login.</p>
                        <div class="d-flex justify-content-center mb-3">
                            <a href="admin/login.php" class="btn btn-info mr-3">Staff</a>
                            <a href="pengguna/index.php" class="btn btn-outline-info">Pengguna</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
