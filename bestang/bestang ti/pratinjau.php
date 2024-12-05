<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    die("Anda tidak memiliki akses.");
}

$role = $_SESSION['role']; 
$sql = "SELECT nama, nip FROM Admin WHERE role = ?";
$params = [$role];
$stmt = sqlsrv_query($conn, $sql, $params);

// Periksa apakah query berhasil
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Ambil data admin
$data_admin = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Jika data tidak ditemukan
if ($data_admin === null) {
    die("Data admin tidak ditemukan.");
}

$_SESSION['nama_admin'] = $data_admin['nama'];
$_SESSION['nip_admin'] = $data_admin['nip'];
?>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>SiBeTa - Akademik</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    
    <!-- Custom styles for this template-->
    <link href="sb-admin-2.min.css" rel="stylesheet" />
    <style>
        /* File list container */
        .file-list {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        /* Each file item box */
        .file-item {
            background-color: #ffffff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        /* File title (with box) */
        .file-title {
            font-size: 1.25rem;
            color: #ffffff;
            padding: 10px;
            background-color: #007bff;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-bottom: 10px;
            border: 2px solid #0056b3;
        }

        /* File details section */
        .file-details {
            padding-top: 10px;
            color: #5a5c69;
            padding-left: 10px;
        }

        /* Status indicator */
        .status {
            font-weight: bold;
        }

        /* Styling for confirmed and not confirmed status */
        .status.not-confirmed {
            color: red;
        }

        .status.confirmed {
            color: green;
        }

        /* Adding some space and margin */
        .file-item p {
            margin-bottom: 5px;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SiBeTa Polinema</div>
            </a>
            <hr class="sidebar-divider my-0" />
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>
            </li>
            <hr class="sidebar-divider my-0" />
            <li class="nav-item">
                <a class="nav-link collapsed" href="tabel.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Mahasiswa</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block" />
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-dark bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['nama_admin']); ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" />
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Main Content -->
                <main>
                    <section class="file-list">
                        <h2>List File</h2>
                        <div class="file-item">
                            <h3 class="file-title" data-toggle="collapse" data-target="#file1Details" aria-expanded="false" aria-controls="file1Details">Distribusi Laporan Skripsi</h3>
                            <div id="file1Details" class="collapse file-details">
                                <p>Tanggal: -</p>
                                <p>File Laporan Skripsi: -</p>
                                <p>Status: <span class="status not-confirmed">Belum Dikonfirmasi</span></p>
                                <p>Komentar: -</p>
                            </div>
                        </div>
                        <div class="file-item">
                            <h3 class="file-title" data-toggle="collapse" data-target="#file2Details" aria-expanded="false" aria-controls="file2Details">Distribusi Laporan Magang</h3>
                            <div id="file2Details" class="collapse file-details">
                                <p>Tanggal: -</p>
                                <p>File Laporan Magang: -</p>
                                <p>Status: <span class="status confirmed">Dikonfirmasi</span></p>
                                <p>Komentar: -</p>
                            </div>
                        </div>
                        <div class="file-item">
                            <h3 class="file-title" data-toggle="collapse" data-target="#file3Details" aria-expanded="false" aria-controls="file3Details">Distribusi Laporan Bebas Kompensasi</h3>
                            <div id="file3Details" class="collapse file-details">
                                <p>Tanggal: -</p>
                                <p>File Bebas Kompensasi: -</p>
                                <p>Status: <span class="status confirmed">Dikonfirmasi</span></p>
                                <p>Komentar: -</p>
                            </div>
                        </div>
                        <div class="file-item">
                            <h3 class="file-title" data-toggle="collapse" data-target="#file4Details" aria-expanded="false" aria-controls="file4Details">Distribusi Laporan Nilai TOEIC</h3>
                            <div id="file4Details" class="collapse file-details">
                                <p>Tanggal: -</p>
                                <p>File Nilai TOEIC: -</p>
                                <p>Status: <span class="status confirmed">Dikonfirmasi</span></p>
                                <p>Komentar: -</p>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
