<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    die("Anda tidak memiliki akses.");
}

$role = $_SESSION['role']; // Ambil role dari session (sesuaikan dengan session yang digunakan)
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

// Simpan data admin ke dalam session (jika diperlukan)
$_SESSION['nama_admin'] = $data_admin['nama'];
$_SESSION['nip_admin'] = $data_admin['nip'];
?>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SiBeta - Akademik</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a
                class="sidebar-brand d-flex align-items-center justify-content-center"
                href="index.php"
            >
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
            <span>Beranda</span></a
          >
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0" />

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a
                class="nav-link collapsed"
                href="#"
                data-toggle="collapse"
                data-target="#collapseSIB"
                aria-expanded="true"
                aria-controls="collapsePages"
            >
                <i class="fas fa-fw fa-folder"></i>
                <span>Angkatan</span>
            </a>    
            <div
                id="collapseSIB"
                class="collapse"
                aria-labelledby="headingPages"
                data-parent="#accordionSidebar"
            >
                <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">List Angkatan:</h6>
                <a class="collapse-item" href="tables.php?angkatan=2021">2021</a>
                <a class="collapse-item" href="tables.php?angkatan=2020">2020</a>
                <a class="collapse-item" href="tables.php?angkatan=2019">2019</a>
                <a class="collapse-item" href="tables.php?angkatan=2018">2018</a>
                <a class="collapse-item" href="tables.php?angkatan=2017">2017</a>
                </div>
            </div>
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
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['nama_admin']); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
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

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Mahasiswa</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Nama</th>
                                            <th>Prodi</th>
                                            <th>NIM</th>
                                            <th>Angkatan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            include 'koneksi.php';
                                            $id = $_GET['id'] ?? null; // Menggunakan null coalescing operator untuk menghindari undefined index
                                            if ($id === null) {
                                                die("Angkatan tidak ditentukan.");
                                            }
                                            
                                            $query = "SELECT m.nama, m.prodi, m.nim, a.angkatan, k.status 
                                            FROM Mahasiswa m 
                                            LEFT JOIN pengajuan_akademik p ON m.id = p.id_mahasiswa
                                            LEFT JOIN konfirmasi_akademik k ON p.id = k.id_pengajuan
                                            LEFT JOIN Angkatan a ON m.id_angkatan = a.id
                                            WHERE a.angkatan = ?";
                                            $params = [$id];
                                            $stmt = sqlsrv_query($conn, $query, $params);
                                            function getNextId() {
                                                static $last_id = 0; // Variabel statis
                                                $last_id++; // Tambah 1 setiap kali fungsi dipanggil
                                                return $last_id; // Kembalikan ID yang baru
                                            }
                                            
                                            $current_id = getNextId();
                                            
                                            while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                echo "<tr>";
                                                
                                                echo "<td>" . $current_id. "</td>";
                                                echo "<td>" . $data['nama'] . "</td>";
                                                echo "<td>" . $data['prodi'] . "</td>";
                                                echo "<td>" . $data['nim'] . "</td>";
                                                echo "<td>" . $data['angkatan'] . "</td>";
                                                echo "<td>" . $data['status'] . "</td>";
                                                echo "</tr>";
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
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
                    <a class="btn btn-primary" href="login.html">Logout</a>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>