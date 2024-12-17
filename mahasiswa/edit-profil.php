<!DOCTYPE html>
<html lang="en">

<?php
session_start();
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SiBeTa POLINEMA - Edit Profil</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .profile-image {
            width: 175px;
            height: 175px;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-image2 {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
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
                    <span>Beranda</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="form-akhir.php">
                    <i class="fas fa-file-signature"></i>
                    <span>Form Akhir</span></a>
            </li>

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
                    <span>Profil</span></a>
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

                <div class="d-flex align-items-center mt-2 mx-3">
            <i class="fas fa-arrow-left text-secondary"></i>
            <button class="btn btn-link text-secondary p-0 ml-2" onclick="goBack()">Back</button>
        </div>

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- User Info Dropdown -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['nama_mahasiswa']); ?>
                                </span>
                                <img src="<?php echo htmlspecialchars($_SESSION['foto_profil'] ?? 'img/undraw_profile.svg'); ?>"
                                    alt="Foto Profil" class="img-fluid profile-image2">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profil.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Content Row for Profile -->
                    <div class="row">

                        <!-- Profile Card Example -->
                        <div class="col-xl-9 col-lg-7 mx-auto">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Profile Mahasiswa</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Profile Picture and Name -->
                                        <div class="col-md-4 d-flex flex-column align-items-center text-center">
                                            <form action="update-profil.php" method="POST" enctype="multipart/form-data"
                                                id="profileForm" style="padding-top: 80px">
                                                <img src="<?php echo htmlspecialchars($_SESSION['foto_profil'] ?? 'img/undraw_profile.svg'); ?>"
                                                    id="profileImagePreview" alt="Foto Profil"
                                                    class="img-fluid profile-image">

                                                <!-- Custom File Input -->
                                                <div class="file-input-wrapper mb-3">
                                                    <input type="file" name="foto_profil" id="fotoProfil"
                                                        class="file-input" accept=".jpg, .jpeg, .png, .gif"
                                                        onchange="previewImage()">
                                                </div>
                                        </div>

                                        <!-- Profile Form -->
                                        <div class="col-md-8">
                                            <form action="update-profil.php" method="POST"
                                                enctype="multipart/form-data">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>NIM</th>
                                                        <td class="readonly">
                                                            <?php echo htmlspecialchars($_SESSION['nim_mahasiswa']); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nama</th>
                                                        <td class="readonly">
                                                            <?php echo htmlspecialchars($_SESSION['nama_mahasiswa']); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Angkatan</th>
                                                        <td class="readonly">
                                                            <?php echo htmlspecialchars($_SESSION['angkatan_mahasiswa']); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jurusan</th>
                                                        <td class="readonly">
                                                            <?php echo htmlspecialchars($_SESSION['jurusan_mahasiswa']); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Program Studi</th>
                                                        <td class="readonly">
                                                            <?php echo htmlspecialchars($_SESSION['prodi_mahasiswa']); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td><input type="email" name="email" class="form-control"
                                                                value="<?php echo htmlspecialchars($_SESSION['email_mahasiswa']); ?>"
                                                                required></td>
                                                    </tr>
                                                    <tr>
                                                        <th>No. Telepon</th>
                                                        <td><input type="text" name="no_telp" class="form-control"
                                                                value="<?php echo htmlspecialchars($_SESSION['no_telp_mahasiswa']); ?>"
                                                                required></td>
                                                    </tr>
                                                </table>
                                                <button type="submit" class="btn btn-success">Save Changes</button>
                                            </form>
                                            </form>
                                        </div>
                                    </div>
                                </div>

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
                        <span>Copyright &copy; SiBeTa - Sistem Bebas Tanggungan 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Custom JS for profile -->
    <script>
          function goBack() {
        window.history.back();
    }
        function previewImage() {
            const file = document.getElementById("fotoProfil").files[0];
            const reader = new FileReader();
            reader.onloadend = function() {
                document.getElementById("profileImagePreview").src = reader.result;
            };
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>

</body>

</html>