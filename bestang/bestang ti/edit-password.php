<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    die("Anda tidak memiliki akses.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip_admin = $_SESSION['nip_admin'];
    $old_password = trim($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password baru
    if ($new_password !== $confirm_password) {
        $error = "Password baru tidak cocok.";
    } else {
        // Cek password lama di database
        $sql = "SELECT password FROM [dbo].[Admin] WHERE nip = ?";
        $params = [$nip_admin];
        $stmt = sqlsrv_query($conn, $sql, $params);
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($data) {
            // Debugging sementara
            // echo "Password dari database: " . htmlspecialchars($data['password']) . "<br>";
            // echo "Password lama yang dimasukkan: " . htmlspecialchars($old_password) . "<br>";
            echo "Password dari database (asli): " . htmlspecialchars($data['password']) . "<br>";

            // Verifikasi password lama
            if (password_verify($old_password, $data['password'])) {
                // Hash dan simpan password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE [dbo].[Admin] SET password = ? WHERE nip = ?";
                $update_params = [$hashed_password, $nip_admin];
                $update_stmt = sqlsrv_query($conn, $update_sql, $update_params);
        
                if ($update_stmt) {
                    $success = "Password berhasil diubah.";
                } else {
                    $error = "Gagal mengubah password: " . print_r(sqlsrv_errors(), true);
                }
            } else {
                $error = "Password lama salah.";
            }
        } else {
            $error = "Data admin tidak ditemukan.";
        }
        
    }
}
?>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>SiBeTa - Prodi </title>

      <!-- Custom fonts for this template-->
      <link
        href="vendor/fontawesome-free/css/all.min.css"
        rel="stylesheet"
        type="text/css"
      />
      <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet"
      />

    <!-- Custom styles for this template-->
    <link href="sb-admin-2.min.css" rel="stylesheet" />
    </head>

    <body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
      <!-- Sidebar -->
      <ul
        class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
        id="accordionSidebar"
      >
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
        <hr class="sidebar-divider my-0" />

        <!-- Nav Item - Beranda -->
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home"></i>
            <span>Beranda</span></a
          >
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0" />

        <!-- Nav Item - Pages Mahasiswa -->
        <li class="nav-item">
          <a
            class="nav-link collapsed"
            href="tabel.php"
          >
            <i class="fas fa-fw fa-folder"></i>
            <span>
              Mahasiswa
            </span>
          </a>
        </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0" />

      <!-- Nav Item - Beranda -->
      <li class="nav-item">
        <a class="nav-link" href="index.php">
        <i class="fas fa-fw fa-folder"></i>
          <span>Rekapan Data</span></a
        >
      </li>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block" />

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
      <nav
        class="navbar navbar-expand navbar-dark bg-white topbar mb-4 static-top shadow"
      >
        <!-- Sidebar Toggle (Topbar) -->
        <button
          id="sidebarToggleTop"
          class="btn btn-link d-md-none rounded-circle mr-3"
        >
          <i class="fa fa-bars"></i>
        </button>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">

    <div class="topbar-divider d-none d-sm-block"></div>
              <!-- Nav Item - User Information -->
              <li class="nav-item dropdown no-arrow">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  id="userDropdown"
                  role="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo htmlspecialchars($_SESSION['nama_admin']); ?></span
                  >
                  <img
                    class="img-profile rounded-circle"
                    src="img/undraw_profile.svg"
                  />
                </a>
                <!-- Dropdown - User Information -->
                <div
                  class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                  aria-labelledby="userDropdown"
                >
                  <a class="dropdown-item" href="profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                  </a>
                  <div class="dropdown-divider"></div>
                  <a
                    class="dropdown-item"
                    href="#"
                    data-toggle="modal"
                    data-target="#logoutModal"
                  >
                    <i
                      class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"
                    ></i>
                    Logout
                  </a>
                </div>
              </li>
            </ul>
          </nav>
          <!-- End of Topbar -->

          <div class="container">
        <h2>Edit Password</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="old_password">Password Lama</label>
                <input type="password" class="form-control" name="old_password" required />
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" class="form-control" name="new_password" required />
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" name="confirm_password" required />
            </div>
            <button type="submit" class="btn btn-primary">Konfirmasi</button>
        </form>
    </div>
          <!-- End of Main Content -->


        </div>
        <!-- End of Content Wrapper -->

                   <!-- Footer -->
                   <footer class="sticky-footer bg-white">
            <div class="container my-auto">
              <div class="copyright text-center my-auto">
                <span
                  >Copyright &copy; SiBeTa - Sistem Bebas Tanggungan 2024</span
                >
              </div>
            </div>
          </footer>
          <!-- End of Footer -->
           
      </div>
      <!-- End of Page Wrapper -->

      <!-- Scroll to Top Button-->
      <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
      </a>

      <!-- Logout Modal-->
      <div
        class="modal fade"
        id="logoutModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">
                Ready to Leave?
              </h5>
              <button
                class="close"
                type="button"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              Select "Logout" below if you are ready to end your current
              session.
            </div>
            <div class="modal-footer">
              <button
                class="btn btn-secondary"
                type="button"
                data-dismiss="modal"
              >
                Cancel
              </button>
              <a
                class="btn btn-primary"
                href="\Empati-Bebas-Tanggungan\index-admin.html"
                >Logout</a
              >
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
    </div>
  </body>
</html>
