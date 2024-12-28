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
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>SiBeTa - Prodi</title>

  <!-- Custom fonts for this template-->
  <link
    href="vendor/fontawesome-free/css/all.min.css"
    rel="stylesheet"
    type="text/css" />
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet" />

  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />

  <!-- Custom styles for this template-->
  <link href="sb-admin-2.min.css" rel="stylesheet" />

</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <ul
      class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion bg-sidebar"
      id="accordionSidebar">
      <!-- Sidebar - Brand -->
      <a
        class="sidebar-brand d-flex align-items-center justify-content-center"
        href="index.php">
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
          <span>Beranda</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0" />

      <!-- Nav Item - Pages Mahasiswa -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="tabel.php">
          <i class="fas fa-fw fa-folder"></i>
          <span>
            Mahasiswa
          </span>
        </a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0" />

      <!-- Nav Item - Pages Mahasiswa -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="rekapan.php">
          <i class="fas fa-fw fa-folder"></i>
          <span>
            Rekapan
          </span>
        </a>
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
          class="navbar navbar-expand navbar-dark bg-white topbar mb-4 static-top shadow">
          <button class="btn" onclick="window.history.back();">
            <i class="fas fa-arrow-left"></i> Kembali
          </button>
          <!-- Sidebar Toggle (Topbar) -->
          <button
            id="sidebarToggleTop"
            class="btn btn-link d-md-none rounded-circle mr-3">
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
                aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                  <?php echo htmlspecialchars($_SESSION['nama_admin']); ?></span>
                <img
                  class="img-profile rounded-circle"
                  src="img/undraw_profile.svg" />
              </a>
              <!-- Dropdown - User Information -->
              <div
                class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <div class="dropdown-divider"></div>
                <a
                  class="dropdown-item"
                  href="#"
                  data-toggle="modal"
                  data-target="#logoutModal">
                  <i
                    class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
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
              <div class="mb-3">
                <div class="row">
                  <!-- Filter Prodi -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="filterProdi" class="form-label">Filter Prodi</label>
                      <select id="filterProdi" class="form-control">
                        <option value="">Semua Prodi</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                      </select>
                    </div>
                  </div>

                  <!-- Filter Angkatan -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="filterAngkatan" class="form-label">Filter Angkatan</label>
                      <select id="filterAngkatan" class="form-control">
                        <option value="">Semua Angkatan</option>
                        <?php
                        for ($year = date('Y'); $year >= 2000; $year--) {
                          echo "<option value='$year'>$year</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>NIM</th>
                      <th>Nama</th>
                      <th>Jurusan</th>
                      <th>Prodi</th>
                      <th>Angkatan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    include 'koneksi.php';

                    $query = "
                              SELECT nim, nama, jurusan, prodi, angkatan
                              FROM rekapan_admin_prodi
                              WHERE prodi = 'Sistem Informasi Bisnis'
                          ";

                    $stmt = sqlsrv_query($conn, $query, $params);

                    while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($data['nim']) . "</td>";
                      echo "<td>" . htmlspecialchars($data['nama']) . "</td>";
                      echo "<td>" . htmlspecialchars($data['jurusan']) . "</td>";
                      echo "<td>" . htmlspecialchars($data['prodi']) . "</td>";
                      echo "<td>" . htmlspecialchars($data['angkatan']) . "</td>";
                      echo "</td>";
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

      <!-- End of Content Wrapper -->
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; SiBeTa - Sistem Bebas Tanggungan 2024</span>
          </div>
        </div>
      </footer>
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
      aria-hidden="true">
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
              aria-label="Close">
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
              data-dismiss="modal">
              Cancel
            </button>
            <a
              class="btn btn-primary"
              href="\Empati-Bebas-Tanggungan\index-admin.html">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <script>
      function updateCardData() {
        $.ajax({
          url: 'get_data.php',
          method: 'GET',
          dataType: 'json',
          success: function(data) {
            // Update jumlah di card
            console.log(data);
            $('.jumlah_bukti_pelunasan').text(data.jumlah_bukti_pelunasan);
            $('.jumlah_bukti_pengisian').text(data.jumlah_bukti_pengisian);
            $('.jumlah_selesai').text(data.jumlah_selesai);
          },
          error: function(xhr, status, error) {
            console.error('Error fetching data:', error);
          }
        });
      }

      // Panggil fungsi updateCardData setiap 5 detik
      setInterval(updateCardData, 1000);
    </script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Script untuk DataTable dan Filtering -->
    <script>
      $(document).ready(function() {
        const table = $('#dataTable').DataTable({
          pageLength: 25,
        });

        $('#filterProdi').change(function() {
          filterTable();
        });

        $('#filterAngkatan').change(function() {
          filterTable();
        });

        function filterTable() {
          const prodiFilter = $('#filterProdi').val();
          const angkatanFilter = $('#filterAngkatan').val();

          table
            .columns(3).search(prodiFilter ? `^${prodiFilter}$` : '', true, false)
            .columns(4).search(angkatanFilter ? `^${angkatanFilter}$` : '', true, false)
            .draw();
        }
      });
    </script>
</body>

</html>