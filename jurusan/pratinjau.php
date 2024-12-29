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

  <title>SiBeTa - Jurusan</title>

  <!-- Custom fonts for this template-->
  <link
    href="vendor/fontawesome-free/css/all.min.css"
    rel="stylesheet"
    type="text/css" />
  <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


  <!-- Custom styles for this template-->
  <link href="sb-admin-2.min.css" rel="stylesheet" />
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <ul
      class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
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

      <!-- Nav Item - Beranda -->
      <li class="nav-item">
        <a class="nav-link" href="rekapan.php">
          <i class="fas fa-fw fa-folder"></i>
          <span>Rekapan</span></a>
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

        <!-- Main Content -->
        <!-- Main Content -->
        <div class="wrapper" style="padding-bottom: 60px;">
          <div class="container">
            <h1 class="h3 mb-4 text-gray-800 text-center">Pratinjau File Mahasiswa</h1>

            <?php
            $nim_mahasiswa = $_GET['nim'];
            $query_files = "SELECT m.nama, m.nim, m.prodi, m.jurusan, p.file_bukti_publikasi, p.path1, 
                                  p.file_skripsi, p.path2, p.hasil_akhir_skripsi, p.path3, p.last_modified,
                                  k.status1 AS status_publikasi, k.status2 AS status_skripsi, k.status3 AS status_akhir
                                  FROM Mahasiswa m
                                  JOIN pengajuan_jurusan p ON p.id_mahasiswa = m.id
                                  JOIN konfirmasi_admin_jurusan k ON k.id_pengajuan = p.id 
                                  WHERE m.nim = ?";
            $params_nim = [$nim_mahasiswa];
            $stmt_files = sqlsrv_query($conn, $query_files, $params_nim);

            if ($stmt_files === false) {
              die(print_r(sqlsrv_errors(), true));
            }

            // Tampilkan data mahasiswa
            $data_mahasiswa = sqlsrv_fetch_array($stmt_files, SQLSRV_FETCH_ASSOC);
            if ($data_mahasiswa) {
              // Row untuk data mahasiswa
              echo '<div class="row mb-4">';
              echo '<div class="col-md-12">';
              echo '<div class="card shadow-sm">';
              echo '<div class="card-body">';

              // Judul untuk Data Mahasiswa
              echo '<h5 class="card-title">&nbsp;&nbsp;Data Mahasiswa</h5>';
              // Tabel untuk menampilkan data mahasiswa
              echo '<table class="table table-borderless">';
              echo '<tbody>';
              echo '<tr>';
              echo '<td><strong>Nama</strong></td>';
              echo '<td>:</td>';
              echo '<td>' . htmlspecialchars($data_mahasiswa['nama']) . '</td>';
              echo '<td><strong>Jurusan</strong></td>';
              echo '<td>:</td>';
              echo '<td>' . htmlspecialchars($data_mahasiswa['jurusan']) . '</td>';
              echo '</tr>';
              echo '<tr>';
              echo '<td><strong>NIM</strong></td>';
              echo '<td>:</td>';
              echo '<td>' . htmlspecialchars($data_mahasiswa['nim']) . '</td>';
              echo '<td><strong>Prodi</strong></td>';
              echo '<td>:</td>';
              echo '<td>' . htmlspecialchars($data_mahasiswa['prodi']) . '</td>';
              echo '</tr>';
              echo '</tbody>';
              echo '</table>';
              echo '</div></div></div>'; // Close card and column
              echo '</div>'; // Close row

              // Row untuk data file dan konfirmasi/komentar
              echo '<div class="row mb-4">';

              // Data file yang akan ditampilkan
              $file1_url = str_replace('uploads/', 'http://localhost/Empati-Bebas-Tanggungan/mahasiswa/uploads/', $data_mahasiswa['path1']);
              $file2_url = str_replace('uploads/', 'http://localhost/Empati-Bebas-Tanggungan/mahasiswa/uploads/', $data_mahasiswa['path2']);
              $file3_url = str_replace('uploads/', 'http://localhost/Empati-Bebas-Tanggungan/mahasiswa/uploads/', $data_mahasiswa['path3']);
              $files = [
                ['nama' => htmlspecialchars($data_mahasiswa['file_bukti_publikasi']), 'url' => $file1_url, 'status' => $data_mahasiswa['status_publikasi']],
                ['nama' => htmlspecialchars($data_mahasiswa['file_skripsi']), 'url' => $file2_url, 'status' => $data_mahasiswa['status_skripsi']],
                ['nama' => htmlspecialchars($data_mahasiswa['hasil_akhir_skripsi']), 'url' => $file3_url, 'status' => $data_mahasiswa['status_akhir']],
              ];

              echo '<div class="container mt-4">';
              echo '<form method="post" action="proses_konfirmasi.php?nim=' . htmlspecialchars($data_mahasiswa['nim']) . '" onsubmit="return showAlert()">'; // Form utama untuk semua input

              foreach ($files as $index => $file) {
                echo '<div class="card mb-4 shadow-sm">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($file['nama']) . '</h5>';

                // Preview berdasarkan ekstensi file
                $ext = strtolower(pathinfo($file['url'], PATHINFO_EXTENSION));

                if ($ext === 'pdf') {
                  // Tampilkan PDF dalam iframe
                  echo '<iframe src="' . htmlspecialchars($file['url']) . '" width="100%" height="400px" class="mb-3"></iframe>';
                } elseif (in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
                  // Gunakan Google Docs Viewer untuk file Office
                  echo '<iframe src="https://docs.google.com/viewer?url=' . urlencode($file['url']) . '&embedded=true" width="100%" height="400px" class="mb-3"></iframe>';
                } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'])) {
                  // Tampilkan gambar langsung
                  echo '<img src="' . htmlspecialchars($file['url']) . '" class="img-fluid mb-3" alt="Preview">';
                } else {
                  // Tampilkan link untuk file yang tidak didukung
                  echo '<p>File tidak dapat dipratinjau. <a href="' . htmlspecialchars($file['url']) . '" target="_blank">Unduh file</a>.</p>';
                }

                // Tambahkan checkbox konfirmasi untuk setiap file
                $current_status = strtolower($file['status']);
                echo '<div class="form-check">';
                echo '<input class="form-check-input" type="radio" name="status[' . $index . ']" id="status_sesuai' . $index . '" value="sesuai" ' .
                  (($current_status === 'sesuai') ? 'checked' : '') . '>';
                echo '<label class="form-check-label" for="status_sesuai' . $index . '">Sesuai</label>';
                echo '</div>';
                echo '<div class="form-check">';
                echo '<input class="form-check-input" type="radio" name="status[' . $index . ']" id="status_tidak_sesuai' . $index . '" value="tidak sesuai" ' .
                  (($current_status === 'tidak sesuai') ? 'checked' : '') . '>';
                echo '<label class="form-check-label" for="status_tidak_sesuai' . $index . '">Tidak Sesuai</label>';
                echo '</div>';

                echo '</div>'; // Close card-body
                echo '</div>'; // Close card
              }

              // Tambahkan area komentar tunggal di bawah semua file
              echo '<div class="form-group mt-4">';
              $sql_komentar = "select k.komentar
                                      FROM konfirmasi_admin_jurusan k
                                      JOIN pengajuan_jurusan p ON p.id = k.id_pengajuan
                                      JOIN Mahasiswa m ON m.id = p.id_mahasiswa
                                      WHERE m.nim = ?";
              $stmt_komentar = sqlsrv_query($conn, $sql_komentar, $params_nim);
              if ($stmt_komentar === false) {
                die(print_r(sqlsrv_errors(), true));
              }
              $data_komentar = sqlsrv_fetch_array($stmt_komentar, SQLSRV_FETCH_ASSOC);
              echo '<label for="komentar">Komentar :</label>';
              $komentar = htmlspecialchars($data_komentar['komentar'] ?? '');
              if ($komentar === 'Menunggu' || $komentar === ' ') {
                $komentar = ' ';
              }
              echo '<textarea class="form-control" id="komentar" name="komentar" rows="4">' . htmlspecialchars($komentar) . '</textarea>';
              echo '</div>';

              // Tambahkan tombol submit di bawah komentar
              echo '<div class="mt-4">';
              echo '<button class="btn btn-secondary mr-3" onclick="window.history.back();">';
              echo '<i class="fas fa-arrow-left"></i> Kembali';
              echo '</button>';
              echo '<button type="submit" class="btn btn-success">Kirim</button>';
              echo '</div>';

              echo '</form>';
              echo '</div>'; // Close container
              echo '</div>'; // Close row
            } else {
              echo '<p class="text-center text-danger">Tidak ada file yang diunggah oleh mahasiswa ini.</p>';
            }
            ?>
          </div>
        </div>
        <!-- End of Main Content -->


        <!-- End of Footer -->
      </div>
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

  <script>
    function showAlert() {
      alert("Data sudah dikirimkan ke mahasiswa");
      return true; // Mengizinkan form untuk disubmit
    }
  </script>

</body>

</html>