<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include '../koneksi.php';

$idMahasiswa = $_SESSION['id_mahasiswa'];

// Mengecek status pengajuan akademik
$queryAkademik = "
SELECT k.status1, k.status2
FROM pengajuan_akademik p
LEFT JOIN konfirmasi_akademik k ON p.id = k.id_pengajuan
WHERE p.id_mahasiswa = ?
";
$stmtAkademik = sqlsrv_query($conn, $queryAkademik, array($idMahasiswa));
$statusAkademik = sqlsrv_fetch_array($stmtAkademik, SQLSRV_FETCH_ASSOC);

// Mengecek status pengajuan jurusan
$queryJurusan = "
SELECT k.status1, k.status2, k.status3
FROM pengajuan_jurusan p
LEFT JOIN konfirmasi_admin_jurusan k ON p.id = k.id_pengajuan
WHERE p.id_mahasiswa = ?
";
$stmtJurusan = sqlsrv_query($conn, $queryJurusan, array($idMahasiswa));
$statusJurusan = sqlsrv_fetch_array($stmtJurusan, SQLSRV_FETCH_ASSOC);

// Mengecek status pengajuan prodi
$queryProdi = "
SELECT k.status1, k.status2, k.status3, k.status4
FROM pengajuan_prodi p
LEFT JOIN konfirmasi_admin_prodi k ON p.id = k.id_pengajuan
WHERE p.id_mahasiswa = ?
";
$stmtProdi = sqlsrv_query($conn, $queryProdi, array($idMahasiswa));
$statusProdi = sqlsrv_fetch_array($stmtProdi, SQLSRV_FETCH_ASSOC);

// Mengecek status pengajuan perpustakaan
$queryPerpus = "
SELECT k.status
FROM pengajuan_perpustakaan p
LEFT JOIN konfirmasi_perpus k ON p.id = k.id_pengajuan
WHERE p.id_mahasiswa = ?
";
$stmtPerpus = sqlsrv_query($conn, $queryPerpus, array($idMahasiswa));
$statusPerpus = sqlsrv_fetch_array($stmtPerpus, SQLSRV_FETCH_ASSOC);

// Debugging: Tampilkan status yang diterima untuk memastikan data yang benar diterima

// Mengecek apakah semua status sesuai
$statusValid = true; // Default status valid

// Mengecek status akademik (termasuk cek untuk NULL atau tidak ditemukan data)
if (
    empty($statusAkademik) ||
    in_array('Menunggu', [$statusAkademik['status1'], $statusAkademik['status2']]) ||
    is_null($statusAkademik['status1']) || is_null($statusAkademik['status2'])
) {
    $statusValid = false; // Jika tidak ada data atau ada status akademik yang "Menunggu" atau NULL
}

// Mengecek status jurusan (termasuk cek untuk NULL atau tidak ditemukan data)
if (
    empty($statusJurusan) ||
    in_array('Menunggu', [$statusJurusan['status1'], $statusJurusan['status2'], $statusJurusan['status3']]) ||
    is_null($statusJurusan['status1']) || is_null($statusJurusan['status2']) || is_null($statusJurusan['status3'])
) {
    $statusValid = false; // Jika tidak ada data atau ada status jurusan yang "Menunggu" atau NULL
}

// Mengecek status prodi (termasuk cek untuk NULL atau tidak ditemukan data)
if (
    empty($statusProdi) ||
    in_array('Menunggu', [$statusProdi['status1'], $statusProdi['status2'], $statusProdi['status3'], $statusProdi['status4']]) ||
    is_null($statusProdi['status1']) || is_null($statusProdi['status2']) || is_null($statusProdi['status3']) || is_null($statusProdi['status4'])
) {
    $statusValid = false; // Jika tidak ada data atau ada status prodi yang "Menunggu" atau NULL
}

// Mengecek status perpustakaan
if (isset($statusPerpus) && (strtolower($statusPerpus['status']) !== 'sesuai')) {
    $statusValid = false; // Jika status perpustakaan tidak "sesuai"
}

// Jika semua status sesuai, maka file bisa diunduh
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SiBeTa POLINEMA - Perpustakaan</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .profile-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            /* Membuat gambar berbentuk lingkaran */
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SiBeTa polinema</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Beranda -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="form-akhir.php">
                    <i class="fas fa-file-signature"></i>
                    <span>Form Akhir</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="admin-perpustakaan.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span> Perpustakaan</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admin-jurusan.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span> Jurusan</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admin-bebastanggungan.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Prodi</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="admin-akademik.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span> Akademik</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>

                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['nama_mahasiswa']); ?></span>
                                <img src="<?php echo htmlspecialchars($_SESSION['foto_profil'] ?? 'img/undraw_profile.svg'); ?>"
                                    id="profileImagePreview" alt="Foto Profil" class="img-fluid profile-image">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profil.php">
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
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Content Row for Profile -->
                    <div class="row">
                        <!-- Profile Card Example -->
                        <div class="col-xl-9 col-lg-7 mx-auto">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                                    <h6 class="m-0 font-weight-bold text-primary">Form Akhir</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover mt-3">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Status</th>
                                                        <th>Download File</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <?php echo ($statusValid) ? 'Dokumen Lengkap' : 'Dokumen Tidak Lengkap'; ?>
                                                        </td>
                                                        <td>
    <?php if ($statusValid): ?>
        <!-- Gunakan path absolut berdasarkan struktur yang benar -->
        <a href="/Empati-Bebas-Tanggungan/form/form-bestang.docx" class="btn btn-primary" download>
            Download File
        </a>
    <?php else: ?>
        <button class="btn btn-secondary" disabled>File Tidak Tersedia</button>
    <?php endif; ?>
</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>


                    <!-- End Content Row -->
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SiBeTa - Sistem Bebas Tanggungan 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="\Empati-Bebas-Tanggungan\index-mahasiswa.html">Logout</a>
                </div>
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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>